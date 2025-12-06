@extends('layouts.app')

@section('title', 'Trang chủ - HP Sneakers')

@section('content')
<!-- Hero Slider -->
<section class="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white overflow-hidden">
    <div class="container mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="z-10">
                <h1 class="text-5xl md:text-6xl font-bold mb-4 animate-fade-in">
                    Ghé thăm<br>tất cả sản phẩm
                </h1>
                <p class="text-xl mb-6 text-blue-100">Bộ sưu tập giày thể thao chính hãng từ các thương hiệu hàng đầu</p>
                <div class="flex gap-4">
                    <a href="{{ route('products.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition transform hover:scale-105">
                        Mua Ngay
                    </a>
                </div>
            </div>
            {{-- Ở đây load top 5 sản phầm còn số lượng tồn kho nhiều nhất lên slider --}}
            <!-- Image Slider -->
            <div class="relative hidden md:block">
                <div class="slider-container relative h-96 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="slider-wrapper flex transition-transform duration-500 ease-in-out h-full">
                        @foreach($topStockProducts as $product)
                        <div class="slide min-w-full h-full relative">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                            <div class="absolute bottom-6 left-6 text-white">
                                <h3 class="text-2xl font-bold mb-2">{{ $product->name }}</h3>
                                <p class="text-sm mb-1">{{ $product->brand }}</p>
                                <p class="text-lg font-semibold">{{ number_format($product->price, 0, ',', '.') }}đ</p>
                                <span class="inline-block mt-2 bg-green-500 text-white text-xs px-3 py-1 rounded-full">
                                    Còn {{ $product->getTotalStock() }} sản phẩm
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Navigation Buttons -->
                    <button class="slider-btn prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 transition shadow-lg z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button class="slider-btn next absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 transition shadow-lg z-10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Dots Indicator -->
                    <div class="slider-dots absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                        @foreach($topStockProducts as $index => $product)
                        <button class="dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brand Logos Section -->
<section class="bg-white py-12 border-t border-b border-gray-200">
    <div class="container mx-auto px-4">
        <h3 class="text-center text-gray-600 text-sm font-semibold mb-8 uppercase tracking-wider">Thương hiệu hàng đầu</h3>
        <div class="flex flex-wrap items-center justify-center gap-12 md:gap-16">
            <a href="{{ url('/thuong-hieu/nike') }}" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                <img src="{{ asset('images/brands/nike-logo.png') }}" alt="Nike" class="h-12 md:h-16 object-contain">
            </a>
            <a href="{{ url('/thuong-hieu/adidas') }}" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                <img src="{{ asset('images/brands/adidas-logo.png') }}" alt="Adidas" class="h-12 md:h-16 object-contain">
            </a>
            <a href="{{ url('/thuong-hieu/puma') }}" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                <img src="{{ asset('images/brands/puma-logo.png') }}" alt="Puma" class="h-12 md:h-16 object-contain">
            </a>
            <a href="{{ url('/thuong-hieu/converse') }}" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                <img src="{{ asset('images/brands/converse-logo.png') }}" alt="Converse" class="h-12 md:h-16 object-contain">
            </a>
            <a href="{{ url('/thuong-hieu/vans') }}" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                <img src="{{ asset('images/brands/vans-logo.png') }}" alt="Vans" class="h-12 md:h-16 object-contain">
            </a>
        </div>
    </div>
</section>

{{-- <!-- Featured Products --}
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
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <h3 class="text-2xl font-bold mb-2">{{ $product->name }}</h3>
                    <p class="text-sm mb-2">{{ Str::limit($product->description, 60) }}</p>
                    <div class="flex justify-between items-center">
                        <div>
                            @if($product->sale_price)
                            <span class="text-2xl font-bold">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                            <span class="text-sm line-through opacity-75 ml-2">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @else
                            <span class="text-2xl font-bold">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <a href="{{ route('products.show', $product->id) }}" class="bg-white text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section> --}}

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderWrapper = document.querySelector('.slider-wrapper');
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.slider-btn.prev');
        const nextBtn = document.querySelector('.slider-btn.next');
        const dots = document.querySelectorAll('.dot');

        let currentIndex = 0;
        const totalSlides = slides.length;

        function updateSlider() {
            sliderWrapper.style.transform = `translateX(-${currentIndex * 100}%)`;

            // Update dots
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.add('bg-white');
                    dot.classList.remove('bg-white/50');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white/50');
                }
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSlider();
        }

        // Event listeners
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                updateSlider();
            });
        });

        // Auto play
        let autoPlay = setInterval(nextSlide, 4000);

        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => clearInterval(autoPlay));
        sliderContainer.addEventListener('mouseleave', () => {
            autoPlay = setInterval(nextSlide, 4000);
        });

        // Initialize
        updateSlider();
    });
</script>
@endpush