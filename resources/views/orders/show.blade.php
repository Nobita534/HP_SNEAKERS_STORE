@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách đơn hàng
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-6">Chi tiết đơn hàng #{{ $order->order_number }}</h1>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Sản phẩm đặt hàng</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-4 pb-4 border-b last:border-b-0">
                            @if($item->product_image)
                            <img src="{{ asset('storage/' . $item->product_image) }}" 
                                 alt="{{ $item->product_name }}" 
                                 class="w-20 h-20 object-cover rounded">
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">{{ $item->product_name }}</p>
                                @if($item->color)
                                <p class="text-sm text-gray-500">Màu: {{ $item->color }}</p>
                                @endif
                                <p class="text-sm text-gray-500">Size: {{ $item->size }}</p>
                                <p class="text-sm text-gray-500">Số lượng: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ number_format($item->price, 0, ',', '.') }}đ</p>
                                <p class="font-semibold text-gray-800">{{ number_format($item->total, 0, ',', '.') }}đ</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="mt-6 pt-4 border-t space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí vận chuyển:</span>
                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Giảm giá:</span>
                            <span>-{{ number_format($order->discount, 0, ',', '.') }}đ</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-xl font-bold text-blue-600 pt-2 border-t">
                            <span>Tổng cộng:</span>
                            <span>{{ number_format($order->total, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Địa chỉ giao hàng</h3>
                    @if($order->address)
                        <p class="text-gray-800"><strong>Người nhận:</strong> {{ $order->address->name }}</p>
                        <p class="text-gray-600"><strong>SĐT:</strong> {{ $order->address->phone }}</p>
                        <p class="text-gray-600"><strong>Địa chỉ:</strong> {{ $order->address->address }}</p>
                        <p class="text-gray-600">{{ $order->address->ward }}, {{ $order->address->district }}, {{ $order->address->city }}</p>
                    @else
                        <p class="text-gray-800"><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                        <p class="text-gray-600"><strong>SĐT:</strong> {{ $order->shipping_phone }}</p>
                        <p class="text-gray-600"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        <p class="text-gray-600">{{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: Order Status & Actions -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Trạng thái đơn hàng</h3>
                    
                    <div class="mb-4">
                        <span class="px-4 py-2 text-sm font-semibold rounded-full inline-block
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

                    <!-- Timeline -->
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-circle text-blue-500 mt-1 mr-3 text-xs"></i>
                            <div>
                                <p class="font-medium text-gray-700">Đơn hàng đã đặt</p>
                                <p class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($order->confirmed_at)
                        <div class="flex items-start">
                            <i class="fas fa-circle text-blue-500 mt-1 mr-3 text-xs"></i>
                            <div>
                                <p class="font-medium text-gray-700">Đã xác nhận</p>
                                <p class="text-gray-500">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->shipped_at)
                        <div class="flex items-start">
                            <i class="fas fa-circle text-purple-500 mt-1 mr-3 text-xs"></i>
                            <div>
                                <p class="font-medium text-gray-700">Đang giao hàng</p>
                                <p class="text-gray-500">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->completed_at)
                        <div class="flex items-start">
                            <i class="fas fa-circle text-green-500 mt-1 mr-3 text-xs"></i>
                            <div>
                                <p class="font-medium text-gray-700">Hoàn thành</p>
                                <p class="text-gray-500">{{ $order->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($order->cancelled_at)
                        <div class="flex items-start">
                            <i class="fas fa-circle text-red-500 mt-1 mr-3 text-xs"></i>
                            <div>
                                <p class="font-medium text-gray-700">Đã hủy</p>
                                <p class="text-gray-500">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                @if($order->cancel_reason)
                                <p class="text-red-600 text-xs mt-1">Lý do: {{ $order->cancel_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Cancel Button -->
                    @if($order->status === 'pending')
                    <div class="mt-6 pt-6 border-t">
                        <button onclick="showCancelModal()" 
                                class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                            <i class="fas fa-times mr-2"></i>Hủy đơn hàng
                        </button>
                    </div>
                    @elseif($order->status !== 'cancelled')
                    <div class="mt-6 pt-6 border-t">
                        <button disabled 
                                class="w-full px-6 py-3 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-semibold">
                            <i class="fas fa-lock mr-2"></i>Không thể hủy đơn
                        </button>
                        <p class="text-xs text-gray-500 mt-2 text-center">Đơn hàng đã được xác nhận</p>
                    </div>
                    @endif
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Thanh toán</h3>
                    <div class="space-y-2 text-sm">
                        <p class="text-gray-600"><strong>Phương thức:</strong> 
                            <span class="uppercase">{{ $order->payment_method }}</span>
                        </p>
                        <p class="text-gray-600"><strong>Trạng thái:</strong> 
                            <span class="px-2 py-1 rounded {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </p>
                        @if($order->paid_at)
                        <p class="text-gray-600"><strong>Thời gian:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                @if($order->note)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Ghi chú</h3>
                    <p class="text-sm text-gray-600">{{ $order->note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancel-modal" class="hidden fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Xác nhận hủy đơn hàng</h3>
            <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do hủy đơn <span class="text-red-500">*</span></label>
                    <select name="cancel_reason" id="cancel-reason-show" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            onchange="toggleOtherReasonShow()">
                        <option value="">Chọn lý do...</option>
                        <option value="Muốn đổi size/sản phẩm khác">Muốn đổi size/sản phẩm khác</option>
                        <option value="Muốn thay đổi địa chỉ giao hàng">Muốn thay đổi địa chỉ giao hàng</option>
                        <option value="Không muốn mua nữa">Không muốn mua nữa</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div id="other-reason-container-show" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vui lòng nhập lý do cụ thể</label>
                    <textarea name="other_reason" id="other-reason-show" rows="3" 
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
function showCancelModal() {
    const modal = document.getElementById('cancel-modal');
    document.getElementById('cancel-reason-show').value = '';
    document.getElementById('other-reason-container-show').classList.add('hidden');
    document.getElementById('other-reason-show').value = '';
    modal.classList.remove('hidden');
}

function closeCancelModal() {
    const modal = document.getElementById('cancel-modal');
    modal.classList.add('hidden');
}

function toggleOtherReasonShow() {
    const select = document.getElementById('cancel-reason-show');
    const container = document.getElementById('other-reason-container-show');
    const textarea = document.getElementById('other-reason-show');
    
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
