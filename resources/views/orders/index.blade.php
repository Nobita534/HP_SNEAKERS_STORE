@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Đơn hàng của tôi</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Đơn hàng #{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipping') bg-purple-100 text-purple-800
                                @elseif(in_array($order->status, ['completed', 'delivered'])) bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                @if($order->status === 'pending') Chờ xử lý
                                @elseif($order->status === 'processing') Đang xử lý
                                @elseif($order->status === 'shipping') Đang giao
                                @elseif($order->status === 'completed') Hoàn thành
                                @elseif($order->status === 'delivered') Đã giao
                                @elseif($order->status === 'cancelled') Đã hủy
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Order Items Summary -->
                    <div class="border-t border-b py-4 my-4">
                        <div class="space-y-2">
                            @foreach($order->items->take(2) as $item)
                            <div class="flex items-center gap-3">
                                @if($item->product_image)
                                <img src="{{ asset('storage/' . $item->product_image) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">Size: {{ $item->size }} | SL: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-gray-800">{{ number_format($item->total, 0, ',', '.') }}đ</p>
                            </div>
                            @endforeach
                            
                            @if($order->items->count() > 2)
                            <p class="text-sm text-gray-500 italic">Và {{ $order->items->count() - 2 }} sản phẩm khác...</p>
                            @endif
                        </div>
                    </div>

                    <!-- Order Total & Actions -->
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Tổng tiền:</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($order->total, 0, ',', '.') }}đ</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('orders.show', $order->id) }}" 
                               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-eye mr-2"></i>Xem chi tiết
                            </a>
                            @if($order->status === 'pending')
                            <button onclick="showCancelModal({{ $order->id }})" 
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times mr-2"></i>Hủy đơn
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl text-gray-600 mb-4">Bạn chưa có đơn hàng nào</p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Mua sắm ngay
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancel-modal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Xác nhận hủy đơn hàng</h3>
            <form id="cancel-form" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do hủy đơn <span class="text-red-500">*</span></label>
                    <select name="cancel_reason" id="cancel-reason" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="toggleOtherReason()">
                        <option value="">Chọn lý do...</option>
                        <option value="Muốn đổi size/sản phẩm khác">Muốn đổi size/sản phẩm khác</option>
                        <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
                        <option value="Không muốn mua nữa">Không muốn mua nữa</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div id="other-reason-container" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vui lòng nhập lý do cụ thể</label>
                    <textarea name="other_reason" id="other-reason" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()" 
                            class="flex-1 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Đóng
                    </button>
                    <button type="submit" 
                            class="flex-1 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        Xác nhận hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCancelModal(orderId) {
    const modal = document.getElementById('cancel-modal');
    const form = document.getElementById('cancel-form');
    form.action = `/don-hang/${orderId}/huy`;
    document.getElementById('cancel-reason').value = '';
    document.getElementById('other-reason-container').classList.add('hidden');
    document.getElementById('other-reason').value = '';
    modal.classList.remove('hidden');
}

function closeCancelModal() {
    const modal = document.getElementById('cancel-modal');
    modal.classList.add('hidden');
}

function toggleOtherReason() {
    const select = document.getElementById('cancel-reason');
    const container = document.getElementById('other-reason-container');
    const textarea = document.getElementById('other-reason');
    
    if (select.value === 'other') {
        container.classList.remove('hidden');
        textarea.required = true;
    } else {
        container.classList.add('hidden');
        textarea.required = false;
        textarea.value = '';
    }
}
</script>
@endpush
