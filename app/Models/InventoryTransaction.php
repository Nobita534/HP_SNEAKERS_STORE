<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'import_code',
        'product_id',
        'size',
        'quantity',
        'import_price',
        'total_cost',
        'type',
        'note',
        'user_id',
        'imported_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'import_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'imported_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Tính tổng tiền nếu chưa có
            if (!$model->total_cost) {
                $model->total_cost = $model->quantity * $model->import_price;
            }
        });
    }

    /**
     * Relationship với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship với User (người nhập hàng)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Xử lý nhập hàng và cập nhật WAC
     */
    public static function processImport($data)
    {
        return DB::transaction(function () use ($data) {
            // Tạo giao dịch nhập hàng
            $transaction = static::create([
                'product_id' => $data['product_id'],
                'size' => $data['size'],
                'quantity' => $data['quantity'],
                'import_price' => $data['import_price'],
                'type' => $data['type'] ?? 'import',
                'note' => $data['note'] ?? null,
                'user_id' => auth()->id(),
                'imported_at' => now()
            ]);

            // Lấy hoặc tạo ProductSize
            $productSize = ProductSize::firstOrCreate(
                [
                    'product_id' => $data['product_id'],
                    'size' => $data['size']
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
            $newQuantity = $data['quantity'];
            $newPrice = $data['import_price'];

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

            // Cập nhật WAC cho Product (lấy trung bình của tất cả sizes)
            $product = Product::find($data['product_id']);
            $allSizes = ProductSize::where('product_id', $data['product_id'])
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

            return $transaction;
        });
    }

    /**
     * Lấy danh sách nhập hàng theo mã phiếu
     */
    public static function getByImportCode($importCode)
    {
        return static::where('import_code', $importCode)
            ->with(['product', 'user'])
            ->get();
    }

    /**
     * Tính tổng giá trị nhập hàng theo mã phiếu
     */
    public static function getTotalByImportCode($importCode)
    {
        return static::where('import_code', $importCode)->sum('total_cost');
    }
}
