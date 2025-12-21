<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(string $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.product', 'address', 'user'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Request $request, string $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái pending
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng này!');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500',
            'other_reason' => 'nullable|string|max:500',
        ]);

        // Nếu chọn "Khác" thì lấy lý do từ other_reason
        $cancelReason = $request->cancel_reason;
        if ($cancelReason === 'other' && $request->other_reason) {
            $cancelReason = $request->other_reason;
        }

        $order->status = 'cancelled';
        $order->cancelled_at = now();
        $order->cancel_reason = $cancelReason;
        $order->save();

        // Hoàn trả tồn kho
        foreach ($order->items as $item) {
            $productSize = ProductSize::where('product_id', $item->product_id)
                ->where('size', $item->size)
                ->first();

            if ($productSize) {
                $productSize->increaseQuantity($item->quantity);
            }
        }

        return redirect()->route('orders.index')->with('success', 'Đã hủy đơn hàng thành công!');
    }
}
