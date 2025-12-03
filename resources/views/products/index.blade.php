@extends('layouts.app')

@section('title', 'Tất cả sản phẩm - HP Sneakers')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-100 py-4">
    <div class="container mx-auto px-4">
        <nav class="flex items-center text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">
                <i class="fas fa-home mr-1"></i>
                Trang chủ
            </a>
            <i class="fas fa-chevron-right mx-3 text-xs"></i>
            <span class="text-gray-800 font-medium">Tất cả sản phẩm</span>
        </nav>
    </div>
</div>

<!-- Page Header -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-2">Tất Cả Sản Phẩm</h1>
                <p class="text-blue-100 text-lg">
                    <i class="fas fa-box mr-2"></i>
                    {{ $totalProducts }} sản phẩm có sẵn
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-shoe-prints text-6xl opacity-30"></i>
            </div>
        </div>
    </div>
</section>

<!-- Brand Filter -->
<section class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Lọc theo thương hiệu</h2>
        <div class="flex flex-wrap justify-center items-center gap-4">
            <a href="{{ route('products.index') }}"
                class="bg-white rounded-lg px-8 py-4 flex items-center justify-center hover:bg-blue-50 hover:shadow-md transition ring-2 ring-blue-600 bg-blue-50">
                <span class="text-lg font-bold text-blue-600">
                    Tất cả
                </span>
            </a>
            @foreach(['Nike', 'Adidas', 'Puma', 'Converse', 'Vans'] as $brand)
            <a href="{{ route('products.by-brand', Str::slug($brand)) }}"
                class="bg-white rounded-lg px-8 py-4 flex items-center justify-center hover:bg-blue-50 hover:shadow-md transition">
                <span class="text-lg font-bold text-gray-600">
                    {{ $brand }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="container mx-auto px-4 py-12">
    @if($products->isEmpty())
    <!-- Empty State -->
    <div class="text-center py-16">
        <div class="inline-block p-8 bg-gray-100 rounded-full mb-6">
            <i class="fas fa-shopping-bag text-6xl text-gray-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Không tìm thấy sản phẩm</h2>
        <p class="text-gray-600 mb-8">
            Hiện tại chúng tôi chưa có sản phẩm nào.
        </p>
        <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Quay về trang chủ
        </a>
    </div>
    @else
    <!-- Sort & Filter Bar -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div class="text-gray-600">
            Hiển thị <span class="font-semibold text-gray-800">{{ $products->firstItem() }}-{{ $products->lastItem() }}</span>
            trong tổng số <span class="font-semibold text-gray-800">{{ $products->total() }}</span> sản phẩm
        </div>

        <!-- Sort Options -->
        <div class="flex items-center gap-2">
            <label for="sort" class="text-gray-600 text-sm">Sắp xếp:</label>
            <select id="sort" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="newest">Mới nhất</option>
                <option value="price_asc">Giá tăng dần</option>
                <option value="price_desc">Giá giảm dần</option>
                <option value="name">Tên A-Z</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300">
            <div class="relative overflow-hidden">
                <!-- Product Image -->
                <a href="{{ route('products.show', $product->slug) }}">
                    <img
                        src="{{ asset($product->image) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-300">
                </a>

                <!-- Quick Actions -->
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button class="bg-white p-2 rounded-full shadow-lg hover:bg-blue-600 hover:text-white transition mb-2">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>

                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <!-- Product Info Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                    <h3 class="text-xl font-bold mb-1">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-200 mb-3 line-clamp-2">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Product Details -->
            <div class="p-5">
                <div class="mb-3">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <h3 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-blue-600 transition">
                            {{ $product->name }}
                        </h3>
                    </a>
                    <p class="text-sm text-gray-600">{{ $product->brand }}</p>
                </div>

                <!-- Price -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-2xl font-bold text-gray-800">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </span>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="mb-4">
                    @if($product->stock > 10)
                    <span class="text-xs text-green-600 font-medium">
                        <i class="fas fa-check-circle mr-1"></i>
                        Còn hàng
                    </span>
                    @elseif($product->stock > 0)
                    <span class="text-xs text-orange-600 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Chỉ còn {{ $product->stock }} sản phẩm
                    </span>
                    @else
                    <span class="text-xs text-red-600 font-medium">
                        <i class="fas fa-times-circle mr-1"></i>
                        Hết hàng
                    </span>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Thêm vào giỏ
                    </button>
                    <a href="{{ route('products.show', $product->slug) }}" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg font-semibold hover:bg-gray-200 transition flex items-center justify-center">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        @if($products->hasPages())
        <nav class="flex items-center gap-2" aria-label="Pagination">
            {{-- Pagination Numbers --}}
            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
            @if ($page == $products->currentPage())
            {{-- Active Page --}}
            <span class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white font-semibold rounded">
                {{ $page }}
            </span>
            @else
            {{-- Other Pages --}}
            <a href="{{ $url }}"
                class="w-10 h-10 flex items-center justify-center bg-white text-gray-700 font-semibold rounded hover:bg-gray-100 transition border border-gray-300">
                {{ $page }}
            </a>
            @endif
            @endforeach

            {{-- Next Page Arrow --}}
            @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}"
                class="w-10 h-10 flex items-center justify-center bg-white text-gray-700 rounded hover:bg-gray-100 transition border border-gray-300"
                aria-label="Trang sau">
                <i class="fas fa-arrow-right"></i>
            </a>
            @endif
        </nav>
        @else
        {{-- Display when only 1 page --}}
        <span class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white font-semibold rounded">
            1
        </span>
        @endif
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
    // Sort functionality
    document.getElementById('sort').addEventListener('change', function() {
        console.log('Sort by:', this.value);
    });
</script>
@endpush