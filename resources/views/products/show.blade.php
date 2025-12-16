@extends('layouts.app')

@section('title', $product->name . ' - HP Sneakers')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-blue-600">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </nav>

        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 gap-8 p-8">
                <!-- Product Image -->
                <div>
                    <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 mb-4">
                        <img src="{{ asset($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                        @if($product->sale_price)
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </div>
                        @endif
                    </div>

                    <!-- Additional Images (if available) -->
                    @if($product->images)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(json_decode($product->images) as $img)
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 cursor-pointer hover:opacity-75 transition">
                            <img src="{{ asset($img) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <!-- Brand -->
                    <div class="mb-2">
                        <a href="{{ route('products.by-brand', \Illuminate\Support\Str::slug($product->brand)) }}" 
                           class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            {{ $product->brand }}
                        </a>
                    </div>

                    <!-- Product Name -->
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                        {{ $product->name }}
                    </h1>

                    <!-- Price -->
                    <div class="mb-6">
                        @if($product->sale_price)
                        <div class="flex items-center gap-3">
                            <span class="text-4xl font-bold text-red-600">
                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                            </span>
                            <span class="text-2xl text-gray-400 line-through">
                                {{ number_format($product->price, 0, ',', '.') }}đ
                            </span>
                        </div>
                        @else
                        <span class="text-4xl font-bold text-gray-800">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </span>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Mô tả sản phẩm</h3>
                        <p class="text-gray-600 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    </div>

                    <!-- Sizes Selection -->
                    @if($product->productSizes->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Chọn kích thước</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($product->productSizes->sortBy('size') as $productSize)
                            <label class="relative">
                                <input type="radio" name="size" value="{{ $productSize->size }}" 
                                       class="peer sr-only size-radio" required 
                                       data-quantity="{{ $productSize->quantity }}"
                                       {{ $productSize->quantity <= 0 ? 'disabled' : '' }}>
                                <div class="px-6 py-3 border-2 rounded-lg cursor-pointer text-center min-w-[80px]
                                            peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600
                                            {{ $productSize->quantity <= 0 ? 'opacity-40 cursor-not-allowed bg-gray-100' : 'hover:border-blue-400' }}">
                                    <span class="font-semibold block">{{ $productSize->size }}</span>
                                    @if($productSize->quantity <= 5 && $productSize->quantity > 0)
                                    <span class="block text-xs text-orange-600 mt-1">Còn {{ $productSize->quantity }}</span>
                                    @elseif($productSize->quantity <= 0)
                                    <span class="block text-xs text-red-600 mt-1">Hết hàng</span>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Vui lòng chọn kích thước</p>
                    </div>
                    @endif

                    <!-- Color -->
                    @if($product->color)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Màu sắc</h3>
                        <span class="inline-block bg-gray-100 px-4 py-2 rounded-lg">{{ $product->color }}</span>
                    </div>
                    @endif

                    <!-- Stock Status -->
                    <div class="mb-6">
                        @php
                            $totalStock = $product->getTotalStock();
                            $availableSizes = $product->getAvailableSizes();
                        @endphp
                        
                        @if($totalStock > 10)
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="font-medium">Còn hàng ({{ count($availableSizes) }} sizes có sẵn)</span>
                        </div>
                        @elseif($totalStock > 0)
                        <div class="flex items-center text-orange-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="font-medium">Còn {{ $totalStock }} sản phẩm ({{ count($availableSizes) }} sizes)</span>
                        </div>
                        @else
                        <div class="flex items-center text-red-600">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span class="font-medium">Hết hàng</span>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 mb-6">
                        <button id="add-to-cart-btn" 
                                class="flex-1 bg-blue-600 text-white px-8 py-4 rounded-lg font-bold hover:bg-blue-700 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed" 
                                {{ $totalStock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <a href="{{ route('products.show', $relatedProduct->id) }}">
                        <div class="relative overflow-hidden aspect-square">
                            <img src="{{ asset($relatedProduct->image) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            @if($relatedProduct->sale_price)
                            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                -{{ round((($relatedProduct->price - $relatedProduct->sale_price) / $relatedProduct->price) * 100) }}%
                            </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="text-xs text-gray-500 mb-1">{{ $relatedProduct->brand }}</div>
                            <h3 class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition">
                                {{ $relatedProduct->name }}
                            </h3>
                            <div>
                                @if($relatedProduct->sale_price)
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-red-600">
                                        {{ number_format($relatedProduct->sale_price, 0, ',', '.') }}đ
                                    </span>
                                    <span class="text-xs text-gray-400 line-through">
                                        {{ number_format($relatedProduct->price, 0, ',', '.') }}đ
                                    </span>
                                </div>
                                @else
                                <span class="text-lg font-bold text-gray-800">
                                    {{ number_format($relatedProduct->price, 0, ',', '.') }}đ
                                </span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const sizeRadios = document.querySelectorAll('input[name="size"]');
    
    addToCartBtn.addEventListener('click', function() {
        const selectedSize = document.querySelector('input[name="size"]:checked');
        
        if (!selectedSize) {
            showToast('Vui lòng chọn kích thước', 'warning');
            return;
        }

        const productId = {{ $product->id }};
        const size = selectedSize.value;
        const quantity = 1;

        // Disable button
        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang thêm...';

        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                size: size,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification
                showToast(data.message, 'success');
                
                // Update cart count in header
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    // Update existing badge
                    cartCount.textContent = data.cart_count;
                } else if (data.cart_count > 0) {
                    // Create new badge if it doesn't exist
                    const cartLink = document.querySelector('a[href="{{ route("cart.index") }}"]');
                    if (cartLink) {
                        const badge = document.createElement('span');
                        badge.className = 'cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center';
                        badge.textContent = data.cart_count;
                        cartLink.appendChild(badge);
                    }
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
        })
        .finally(() => {
            // Re-enable button
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Thêm vào giỏ hàng';
        });
    });
});
</script>
@endsection
