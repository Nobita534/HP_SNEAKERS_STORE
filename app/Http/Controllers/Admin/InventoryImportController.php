<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryImportController extends Controller
{
    /**
     * Hiển thị danh sách phiếu nhập hàng
     */
    public function index()
    {
        // Lấy danh sách các mã phiếu nhập duy nhất
        $imports = InventoryTransaction::select('import_code', 'imported_at', 'user_id')
            ->selectRaw('SUM(total_cost) as total_amount')
            ->selectRaw('SUM(quantity) as total_items')
            ->with('user')
            ->groupBy('import_code', 'imported_at', 'user_id')
            ->orderBy('imported_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.imports.index', compact('imports'));
    }

    /**
     * Hiển thị form nhập hàng mới
     */
    public function create()
    {
        $products = Product::where('is_active', true)
            ->with('productSizes')
            ->orderBy('name')
            ->get();

        return view('admin.inventory.imports.create', compact('products'));
    }

    /**
     * Xử lý lưu phiếu nhập hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.size' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.import_price' => 'required|numeric|min:0',
        ], [
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại',
            'items.*.size.required' => 'Vui lòng chọn size',
            'items.*.quantity.required' => 'Vui lòng nhập số lượng',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0',
            'items.*.import_price.required' => 'Vui lòng nhập giá nhập',
            'items.*.import_price.min' => 'Giá nhập phải lớn hơn hoặc bằng 0',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mã phiếu nhập duy nhất
            $date = now()->format('Ymd');
            $count = InventoryTransaction::whereDate('created_at', today())->count() + 1;
            $importCode = 'IMP' . $date . str_pad($count, 3, '0', STR_PAD_LEFT);

            $totalAmount = 0;

            // Xử lý từng item
            foreach ($request->items as $item) {
                // Tạo giao dịch nhập hàng
                $transaction = InventoryTransaction::create([
                    'import_code' => $importCode,
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                    'total_cost' => $item['quantity'] * $item['import_price'],
                    'type' => 'import',
                    'note' => $request->note ?? null,
                    'user_id' => auth()->id(),
                    'imported_at' => now()
                ]);

                $totalAmount += $transaction->total_cost;

                // Lấy hoặc tạo ProductSize
                $productSize = ProductSize::firstOrCreate(
                    [
                        'product_id' => $item['product_id'],
                        'size' => $item['size']
                    ],
                    [
                        'quantity' => 0,
                        'total_imported' => 0,
                        'total_sold' => 0,
                        'average_cost_per_size' => 0
                    ]
                );

                // Tính WAC cho size này
                $oldStock = $productSize->quantity;
                $oldCost = $productSize->average_cost_per_size;
                $newQuantity = $item['quantity'];
                $newPrice = $item['import_price'];

                if ($oldStock > 0) {
                    // Công thức WAC: ((Tồn cũ × Giá cũ) + (SL mới × Giá mới)) / Tổng tồn
                    $newAverageCost = (($oldStock * $oldCost) + ($newQuantity * $newPrice)) / ($oldStock + $newQuantity);
                } else {
                    $newAverageCost = $newPrice;
                }

                // Cập nhật ProductSize
                $productSize->update([
                    'quantity' => $oldStock + $newQuantity,
                    'total_imported' => $productSize->total_imported + $newQuantity,
                    'average_cost_per_size' => $newAverageCost
                ]);

                // Cập nhật WAC cho Product (lấy trung bình của tất cả sizes có tồn kho)
                $product = Product::find($item['product_id']);
                $allSizes = ProductSize::where('product_id', $item['product_id'])
                    ->where('quantity', '>', 0)
                    ->get();

                if ($allSizes->count() > 0) {
                    $totalValue = 0;
                    $totalQuantity = 0;

                    foreach ($allSizes as $size) {
                        $totalValue += $size->quantity * $size->average_cost_per_size;
                        $totalQuantity += $size->quantity;
                    }

                    $productAverageCost = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
                    
                    $product->update([
                        'average_cost' => $productAverageCost
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.inventory.imports.show', $importCode)
                ->with('success', "Nhập hàng thành công! Mã phiếu: {$importCode}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị chi tiết phiếu nhập
     */
    public function show($importCode)
    {
        $transactions = InventoryTransaction::where('import_code', $importCode)
            ->with(['product', 'user'])
            ->get();

        if ($transactions->isEmpty()) {
            abort(404, 'Không tìm thấy phiếu nhập hàng');
        }

        $totalAmount = $transactions->sum('total_cost');
        $totalItems = $transactions->sum('quantity');

        return view('admin.inventory.imports.show', compact('transactions', 'importCode', 'totalAmount', 'totalItems'));
    }

    /**
     * API: Lấy thông tin sizes của sản phẩm
     */
    public function getProductSizes($productId)
    {
        $sizes = ProductSize::where('product_id', $productId)->get();
        
        return response()->json([
            'sizes' => $sizes
        ]);
    }
}
