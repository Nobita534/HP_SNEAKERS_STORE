@extends('layouts.app')

@section('title', 'Trang chủ - HP Sneakers')

@section('content')
<!-- Hero Slider -->
<section class="relative bg-blue-600 text-white">
    <div class="container mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    Bộ Sưu Tập<br>Giày Thể Thao 2025
                </h1>
                <p class="text-xl mb-8 text-gray-100">
                    Khám phá những mẫu giày mới nhất từ các thương hiệu hàng đầu thế giới
                </p>
                <div class="flex gap-4">
                    <a href="#" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        Mua Ngay
                    </a>
                    <a href="#" class="border-2 border-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-600 transition">
                        Xem Thêm
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <img src="https://via.placeholder.com/500x400" alt="Hero Sneaker" class="w-full rounded-lg shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Category Cards -->
<section class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($categories as $category)
        <a href="#" class="group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
            <div class="aspect-square bg-blue-100 flex items-center justify-center">
                <i class="fas {{ $category->icon }} text-6xl text-blue-600 group-hover:scale-110 transition"></i>
            </div>
            <div class="p-4 text-center">
                <h3 class="font-bold text-lg">{{ strtoupper($category->name) }}</h3>
                <p class="text-sm text-gray-600">{{ $category->products->count() }}+ sản phẩm</p>
            </div>
        </a>
        @endforeach
    </div>
</section>

<!-- New Arrivals -->
<section class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Sản Phẩm Mới Nhất</h2>
            <p class="text-gray-600">Những đôi giày hot nhất hiện nay</p>
        </div>
        <a href="#" class="text-blue-600 font-semibold hover:text-blue-700 transition">
            Xem tất cả <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($newProducts as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition">
            <div class="relative overflow-hidden">
                <img src="https://via.placeholder.com/300x300/3B82F6/FFFFFF?text={{ urlencode($product->brand) }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover group-hover:scale-110 transition duration-300">
                @if($product->discount_percent > 0)
                <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">
                    -{{ $product->discount_percent }}%
                </div>
                @endif
                <button class="absolute top-2 left-2 bg-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-red-500 hover:text-white">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-1 truncate">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mb-2">{{ $product->brand }}</p>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->rating))
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                        @else
                            <i class="fas fa-star text-gray-300 text-xs"></i>
                        @endif
                    @endfor
                    <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    @if($product->sale_price)
                        <span class="text-lg font-bold text-red-600">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                        <span class="text-sm text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    @else
                        <span class="text-lg font-bold text-blue-600">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                    @endif
                </div>
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-shopping-cart mr-2"></i>Thêm vào giỏ
                </button>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Brands -->
<section class="bg-white py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Thương Hiệu Nổi Bật</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-8">
            @foreach(['Nike', 'Adidas', 'Puma', 'Converse', 'Vans', 'New Balance'] as $brand)
            <a href="#" class="bg-gray-100 rounded-lg p-6 flex items-center justify-center hover:bg-gray-200 transition">
                <span class="text-xl font-bold text-gray-600">{{ $brand }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Sản Phẩm Nổi Bật</h2>
            <p class="text-gray-600">Được yêu thích nhất</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredProducts as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition">
            <div class="relative">
                <img src="https://via.placeholder.com/400x300/3B82F6/FFFFFF?text={{ urlencode($product->brand) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <h3 class="text-2xl font-bold mb-2">{{ $product->name }}</h3>
                    <p class="text-sm mb-2">{{ $product->description }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold">{{ number_format($product->sale_price ?? $product->price, 0, ',', '.') }}đ</span>
                        <button class="bg-white text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Chi tiết
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Why Choose Us -->
<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">GIAO HÀNG NHANH</h3>
                <p class="text-sm text-gray-600">Miễn phí ship đơn từ 500k</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">100% CHÍNH HÃNG</h3>
                <p class="text-sm text-gray-600">Cam kết hàng auth</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-undo text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">ĐỔI TRẢ DỄ DÀNG</h3>
                <p class="text-sm text-gray-600">Đổi trả trong 7 ngày</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl text-white"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">HỖ TRỢ 24/7</h3>
                <p class="text-sm text-gray-600">Tư vấn nhiệt tình</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="bg-blue-600 py-12">
    <div class="container mx-auto px-4 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Đăng Ký Nhận Ưu Đãi</h2>
        <p class="mb-6">Nhận thông tin sản phẩm mới và khuyến mãi hấp dẫn</p>
        <div class="max-w-md mx-auto flex gap-2">
            <input type="email" 
                   placeholder="Nhập email của bạn..." 
                   class="flex-1 px-4 py-3 rounded-lg text-gray-800 focus:outline-none">
            <button class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                Đăng ký
            </button>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Add any custom JavaScript here
    console.log('HP Sneakers - Home Page Loaded');
</script>
@endpush