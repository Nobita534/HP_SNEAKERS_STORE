<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HP Sneakers - Cửa hàng giày thể thao chính hãng')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <!-- Header -->
    @include('layouts.partials.header')
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-20 right-4 z-50 animate-fade-in" id="flash-message">
            <div class="bg-green-600 text-white shadow-xl rounded-lg overflow-hidden max-w-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-20 right-4 z-50 animate-fade-in" id="flash-message">
            <div class="bg-red-600 text-white shadow-xl rounded-lg overflow-hidden max-w-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="fixed top-20 right-4 z-50 animate-fade-in" id="flash-message">
            <div class="bg-blue-600 text-white shadow-xl rounded-lg overflow-hidden max-w-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-xl mr-3"></i>
                        <span class="font-medium">{{ session('info') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="fixed top-20 right-4 z-50 animate-fade-in" id="flash-message">
            <div class="bg-yellow-500 text-white shadow-xl rounded-lg overflow-hidden max-w-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <span class="font-medium">{{ session('warning') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('layouts.partials.footer')
    
    <!-- Auto-hide Flash Messages -->
    <script>
        setTimeout(function() {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.transition = 'opacity 0.5s';
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500);
            }
        }, 3000);
    </script>
    
    @stack('scripts')
</body>
</html>
