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
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-600 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold">HP Sneakers</h1>
                <p class="text-blue-200 text-sm">Admin Panel</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.products.*') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-box w-5"></i>
                    <span class="ml-3">Sản phẩm</span>
                </a>
                
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.orders.*') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Đơn hàng</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Người dùng</span>
                </a>
                
                <div class="border-t border-blue-500 my-4"></div>
                
                <a href="{{ route('home') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Về trang chủ</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}" class="px-6 py-3">
                    @csrf
                    <button type="submit" class="flex items-center w-full hover:text-blue-200 transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Đăng xuất</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">Xin chào, <strong>{{ auth()->user()->name }}</strong></span>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-6 mt-4">
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
                <div class="mx-6 mt-4">
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
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
