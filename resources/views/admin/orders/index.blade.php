@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng - Admin')
@section('page-title', 'Quản lý đơn hàng')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã đơn</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khách hàng</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng tiền</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày đặt</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-blue-600">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ number_format($order->total, 0, ',', '.') }}đ</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipping') bg-purple-100 text-purple-800
                            @elseif(in_array($order->status, ['completed', 'delivered'])) bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($order->status === 'pending') Chờ xử lý
                            @elseif($order->status === 'processing') Đang xử lý
                            @elseif($order->status === 'shipping') Đang giao
                            @elseif($order->status === 'completed') Hoàn thành
                            @elseif($order->status === 'delivered') Đã giao
                            @elseif($order->status === 'cancelled') Đã hủy
                            @else {{ ucfirst($order->status) }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="text-blue-600 hover:text-blue-800" 
                               title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($order->status === 'pending')
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline status-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="processing">
                                    <button type="button" 
                                            class="text-green-600 hover:text-green-800 status-btn" 
                                            title="Xác nhận đơn hàng"
                                            data-message="Xác nhận đơn hàng này?">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'processing')
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline status-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="shipping">
                                    <button type="button" 
                                            class="text-purple-600 hover:text-purple-800 status-btn" 
                                            title="Chuyển đang giao"
                                            data-message="Chuyển trạng thái sang đang giao?">
                                        <i class="fas fa-shipping-fast"></i>
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'shipping')
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline status-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="button" 
                                            class="text-green-600 hover:text-green-800 status-btn" 
                                            title="Hoàn thành đơn"
                                            data-message="Đánh dấu đơn hàng đã hoàn thành?">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['pending', 'processing']))
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline status-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-800 status-btn" 
                                            title="Hủy đơn hàng"
                                            data-message="Hủy đơn hàng này? Tồn kho sẽ được hoàn trả.">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        Chưa có đơn hàng nào
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status update confirmation
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.status-form');
            const message = this.dataset.message;
            showConfirm(
                'Thông báo',
                message,
                () => {
                    form.submit();
                }
            );
        });
    });
});
</script>
@endpush
