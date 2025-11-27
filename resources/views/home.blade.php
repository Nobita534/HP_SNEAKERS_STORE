@extends('layouts.app')

@section('title', 'Trang chủ - HP Sneakers')

@section('content')
<!-- Hero Slider -->
<section class="relative bg-blue-600 text-white">
    <div class="container mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    Ghé thăm<br>tất cả sản phẩm
                </h1>
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
                <img src="{{ asset('images/banners/Shoes.jpg') }}" alt="Sale Banner" class="w-full rounded-lg shadow-2xl">
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
                <h3 class="font-bold text-lg uppercase">{{ $category->name }}</h3>
                <p class="text-sm text-gray-600">{{ $category->products->count() }}+ sản phẩm</p>
            </div>
        </a>
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

@endsection

@push('scripts')
<script>
    // Add any custom JavaScript here
    console.log('HP Sneakers - Home Page Loaded');
</script>
@endpush