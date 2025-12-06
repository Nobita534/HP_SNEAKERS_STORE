@extends('layouts.app')

@section('title', 'Giỏ hàng - HP Sneakers')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Giỏ hàng của bạn</h1>

        @if($cartItems->count() > 0)
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    @foreach($cartItems as $item)
                    <div class="flex gap-4 py-4 border-b last:border-b-0" data-item-id="{{ $item->id }}">
                        <img src="{{ asset($item->product->image) }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-24 h-24 object-cover rounded">
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-600">Size: {{ $item->size }}</p>
                            <p class="text-sm text-gray-600">
                                {{ number_format($item->price, 0, ',', '.') }}đ
                            </p>
                            
                            <div class="flex items-center gap-3 mt-2">
                                <input type="number" 
                                       value="{{ $item->quantity }}" 
                                       min="1" 
                                       class="quantity-input w-20 px-2 py-1 border rounded"
                                       data-item-id="{{ $item->id }}">
                                <button class="remove-btn text-red-600 hover:text-red-700" 
                                        data-item-id="{{ $item->id }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-bold text-gray-800 item-subtotal">
                                {{ number_format($item->getSubtotal(), 0, ',', '.') }}đ
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Tổng đơn hàng</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính</span>
                            <span class="font-semibold cart-total">
                                {{ number_format($cart->getTotal(), 0, ',', '.') }}đ
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí vận chuyển</span>
                            <span class="font-semibold">Miễn phí</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <span class="text-lg font-bold">Tổng cộng</span>
                            <span class="text-lg font-bold text-blue-600 cart-total">
                                {{ number_format($cart->getTotal(), 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>

                    <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                        Tiến hành thanh toán
                    </button>
                    
                    <a href="{{ route('products.index') }}" 
                       class="block text-center mt-4 text-blue-600 hover:text-blue-700">
                        ← Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-16 bg-white rounded-lg shadow-md">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Giỏ hàng trống</h2>
            <p class="text-gray-600 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700">
                Khám phá sản phẩm
            </a>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const quantity = this.value;
            
            updateCartItem(itemId, quantity);
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            showConfirm(
                'Thông báo',
                'Bạn có chắc muốn xóa sản phẩm này?',
                () => {
                    removeCartItem(itemId);
                }
            );
        });
    });
});

function updateCartItem(itemId, quantity) {
    fetch(`/gio-hang/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message, 'error');
        }
    });
}

function removeCartItem(itemId) {
    fetch(`/gio-hang/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Đã xóa sản phẩm thành công', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    });
}
</script>
@endsection
