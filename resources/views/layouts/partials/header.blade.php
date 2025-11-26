<header class="bg-white shadow-md sticky top-0 z-50">
    <!-- Main Header -->
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <div class="text-3xl font-bold text-blue-600">
                    HP SNEAKERS
                </div>
            </a>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                <div class="relative w-full">
                    <input type="text" 
                           placeholder="Tìm kiếm giày, thương hiệu..." 
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition">
                    <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- User Actions -->
            <div class="flex items-center gap-6">
                @auth
                    <!-- User Dropdown -->
                    <div class="relative group">
                        <button class="hidden md:flex flex-col items-center text-gray-700 hover:text-blue-600 transition">
                            <i class="fas fa-user text-xl mb-1"></i>
                            <span class="text-xs">{{ Auth::user()->name }}</span>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Tài khoản
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-box mr-2"></i>Đơn hàng
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-heart mr-2"></i>Yêu thích
                            </a>
                            <hr class="my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:flex flex-col items-center text-gray-700 hover:text-blue-600 transition">
                        <i class="fas fa-user text-xl mb-1"></i>
                        <span class="text-xs">Đăng nhập</span>
                    </a>
                @endauth
                
                <a href="#" class="hidden md:flex flex-col items-center text-gray-700 hover:text-blue-600 transition">
                    <i class="fas fa-heart text-xl mb-1"></i>
                    <span class="text-xs">Yêu thích</span>
                </a>
                <a href="#" class="flex flex-col items-center text-gray-700 hover:text-blue-600 transition relative">
                    <i class="fas fa-shopping-cart text-xl mb-1"></i>
                    <span class="text-xs">Giỏ hàng</span>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </a>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="md:hidden mt-4">
            <div class="relative">
                <input type="text" 
                       placeholder="Tìm kiếm..." 
                       class="w-full px-4 py-2 pr-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <button class="absolute right-2 top-1/2 -translate-y-1/2 text-blue-600">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="bg-gray-100 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <ul class="flex items-center justify-center gap-8 py-3 text-sm font-medium">
                <li><a href="/" class="text-gray-700 hover:text-blue-600 transition">TRANG CHỦ</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600 transition">NAM</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600 transition">NỮ</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600 transition">TRẺ EM</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600 transition">THƯƠNG HIỆU</a></li>
                <li><a href="#" class="text-red-600 hover:text-red-700 transition font-bold">🔥 SALE</a></li>
                <li><a href="#" class="text-gray-700 hover:text-blue-600 transition">LIÊN HỆ</a></li>
            </ul>
        </div>
    </nav>
</header>
