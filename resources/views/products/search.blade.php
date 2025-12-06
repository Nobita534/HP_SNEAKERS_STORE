@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm - HP Sneakers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            Kết quả tìm kiếm
            @if($keyword)
                <span class="text-blue-600">"{{ $keyword }}"</span>
            @endif
        </h1>
        <p class="text-gray-600">Tìm thấy {{ $totalProducts }} sản phẩm</p>
    </div>

    <!-- Filter and Sort Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex justify-between items-center">
        <div class="text-gray-600">
            <span class="font-semibold">{{ $products->total() }}</span> sản phẩm
        </div>

        <!-- Sort Options -->
        <div class="flex items-center gap-2">
            <label for="sort" class="text-gray-600 text-sm">Sắp xếp:</label>
            <select id="sort" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="window.location.href='{{ route('products.search') }}?q={{ urlencode($keyword) }}&sort=' + this.value">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Tên A-Z</option>
            </select>
        </div>
    </div>

    @if($products->isEmpty())
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-search text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Không tìm thấy sản phẩm</h2>
            <p class="text-gray-600 mb-6">Vui lòng thử lại với từ khóa khác</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Xem tất cả sản phẩm
            </a>
        </div>
    @else
        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-8">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                <a href="{{ route('products.show', $product->id) }}" class="block">
                    <div class="relative overflow-hidden aspect-square">
                        <img src="{{ asset($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @if($product->sale_price)
                        <div class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">{{ $product->brand }}</div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($product->sale_price)
                                <div class="flex items-center gap-2">
                                    <span class="text-xl font-bold text-red-600">
                                        {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                    </span>
                                    <span class="text-sm text-gray-400 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </span>
                                </div>
                                @else
                                <span class="text-xl font-bold text-gray-800">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
