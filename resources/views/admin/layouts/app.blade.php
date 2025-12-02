<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - HP Sneakers')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    <!-- Top Navigation -->
    <header class="bg-blue-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold">HP Sneakers</h1>
                    <span class="ml-3 px-3 py-1 bg-blue-500 rounded-full text-xs font-semibold">ADMIN</span>
                </div>
                
                <!-- Navigation Menu -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-chart-line mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-box mr-2"></i>
                        <span>Sản phẩm</span>
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('admin.orders.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        <span>Đơn hàng</span>
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg hover:bg-blue-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-700' : '' }}">
                        <i class="fas fa-users mr-2"></i>
                        <span>Người dùng</span>
                    </a>
                </nav>
                
                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    <span class="hidden md:block text-blue-100 text-sm">{{ auth()->user()->name }}</span>
                    
                    <a href="{{ route('home') }}" class="px-3 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition text-sm font-medium">
                        <i class="fas fa-home mr-1"></i>
                        Về trang chủ
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-blue-700 hover:bg-blue-800 rounded-lg transition text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="min-h-screen">
        <!-- Page Title Bar -->
        <div class="bg-white border-b border-gray-200">
            <div class="container mx-auto px-4 py-4">
                <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <div class="container mx-auto px-4 py-6">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
