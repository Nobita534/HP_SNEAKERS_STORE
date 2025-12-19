@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng - Admin')
@section('page-title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Order Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Sản phẩm đặt hàng</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Đơn giá</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if($item->product_image)
                                            <img src="{{ asset('storage/' . $item->product_image) }}" 
                                                 alt="{{ $item->product_name }}" 
                                                 class="w-16 h-16 object-cover rounded mr-3">
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                            @if($item->color)
                                                <p class="text-sm text-gray-500">Màu: {{ $item->color }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $item->size }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-800">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td class="px-4 py-4 text-sm text-right font-semibold text-gray-800">{{ number_format($item->total, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">Tạm tính:</td>
                            <td class="px-4 py-3 text-right text-gray-800">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">Phí vận chuyển:</td>
                            <td class="px-4 py-3 text-right text-gray-800">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($order->discount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">Giảm giá:</td>
                                <td class="px-4 py-3 text-right text-red-600">-{{ number_format($order->discount, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif
                        <tr class="border-t-2">
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-800">Tổng cộng:</td>
                            <td class="px-4 py-3 text-right font-bold text-blue-600 text-lg">{{ number_format($order->total, 0, ',', '.') }}đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Thông tin khách hàng & Giao hàng</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Thông tin khách hàng</h4>
                    <p class="text-sm text-gray-600"><strong>Tên:</strong> {{ $order->user->name }}</p>
                    <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p class="text-sm text-gray-600"><strong>SĐT:</strong> {{ $order->user->phone ?? 'Chưa cập nhật' }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Địa chỉ giao hàng</h4>
                    <p class="text-sm text-gray-600"><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                    <p class="text-sm text-gray-600"><strong>SĐT:</strong> {{ $order->shipping_phone }}</p>
                    <p class="text-sm text-gray-600"><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                </div>
            </div>
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
                    <i class="fas fa-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <p class="font-medium text-gray-700">Đơn hàng đã đặt</p>
                        <p class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($order->confirmed_at)
                    <div class="flex items-start">
                        <i class="fas fa-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-700">Đã xác nhận</p>
                            <p class="text-gray-500">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->shipped_at)
                    <div class="flex items-start">
                        <i class="fas fa-circle text-purple-500 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-700">Đang giao hàng</p>
                            <p class="text-gray-500">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->completed_at)
                    <div class="flex items-start">
                        <i class="fas fa-circle text-green-500 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-700">Hoàn thành</p>
                            <p class="text-gray-500">{{ $order->completed_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->cancelled_at)
                    <div class="flex items-start">
                        <i class="fas fa-circle text-red-500 mt-1 mr-3"></i>
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

            <!-- Status Update Form -->
            @if(!in_array($order->status, ['completed', 'delivered', 'cancelled']))
                <div class="mt-6 pt-6 border-t">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cập nhật trạng thái:</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-3">
                            @if($order->status === 'pending')
                                <option value="processing">Xác nhận đơn hàng</option>
                                <option value="cancelled">Hủy đơn hàng</option>
                            @elseif($order->status === 'processing')
                                <option value="shipping">Chuyển đang giao</option>
                                <option value="cancelled">Hủy đơn hàng</option>
                            @elseif($order->status === 'shipping')
                                <option value="completed">Hoàn thành đơn hàng</option>
                            @endif
                        </select>

                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
                                onclick="return confirm('Xác nhận thay đổi trạng thái?')">
                            <i class="fas fa-save mr-2"></i>Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Thông tin thanh toán</h3>
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
@endsection
