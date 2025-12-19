<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSize;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['user', 'items.product', 'address'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $request->status;

        // Cập nhật thời gian tương ứng
        if ($request->status == 'processing' && !$order->confirmed_at) {
            $order->confirmed_at = now();
        } elseif ($request->status == 'shipping' && !$order->shipped_at) {
            $order->shipped_at = now();
        } elseif (in_array($request->status, ['completed', 'delivered']) && !$order->completed_at) {
            $order->completed_at = now();
        } elseif ($request->status == 'cancelled' && !$order->cancelled_at) {
            $order->cancelled_at = now();
            $order->cancel_reason = $request->cancel_reason;

            // Hoàn trả tồn kho khi hủy đơn
            foreach ($order->items as $item) {
                $productSize = ProductSize::where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->first();

                if ($productSize) {
                    $productSize->increaseQuantity($item->quantity);
                }
            }
        }

        $order->save();

        Log::info('Order Status Updated:', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
