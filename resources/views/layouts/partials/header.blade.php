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
                <form action="{{ route('products.search') }}" method="GET" class="relative w-full">
                    <input type="text" 
                           id="search-input"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Tìm kiếm giày, thương hiệu..." 
                           class="w-full px-4 py-3 pr-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                           autocomplete="off">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <!-- Search Suggestions Dropdown -->
                    <div id="search-suggestions" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50 max-h-96 overflow-y-auto">
                        <!-- Suggestions will be populated by JavaScript -->
                    </div>
                </form>
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
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 font-semibold">
                                    <i class="fas fa-chart-line mr-2"></i>Admin Dashboard
                                </a>
                                <hr class="my-2">
                            @endif
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Tài khoản
                            </a>
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-box mr-2"></i>Đơn hàng
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

                <!-- Cart -->
                <div class="flex flex-col items-center text-gray-700 hover:text-blue-600 transition relative group">
                    <a href="{{ route('cart.index') }}" class="flex flex-col items-center">
                        <i class="fas fa-shopping-cart text-xl mb-1"></i>
                        <span class="text-xs">Giỏ hàng</span>
                    </a>
                    @php
                        $cart = null;
                        if (Auth::check()) {
                            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                        } else {
                            $cart = \App\Models\Cart::where('session_id', Session::getId())->first();
                        }
                        $cartCount = $cart ? $cart->getTotalItems() : 0;
                        $cartItems = $cart ? $cart->items()->with('product')->limit(4)->get() : collect();
                    @endphp
                    @if($cartCount > 0)
                    <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                    @endif

                    <!-- Invisible bridge area để giữ hover state -->
                    <div class="absolute left-0 right-0 top-full h-2 opacity-0 group-hover:opacity-0"></div>

                    <!-- Cart dropdown -->
                    <div class="absolute right-0 top-full pt-2 w-80 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none group-hover:pointer-events-auto">
                        <div class="bg-white rounded-lg shadow-xl border border-gray-200">
                        @if($cartCount > 0)
                            <!-- Cart Items -->
                            <div class="p-4">
                                <h3 class="text-sm font-semibold text-gray-800 mb-3">Sản phẩm mới thêm</h3>
                                <div class="space-y-3 max-h-64 overflow-y-auto">
                                    @foreach($cartItems as $item)
                                    <div class="flex gap-3 items-start hover:bg-gray-50 p-2 rounded-lg transition">
                                        <img src="{{ asset($item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-800 truncate">{{ $item->product->name }}</h4>
                                            <p class="text-xs text-gray-500">Size: {{ $item->size }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-600">SL: {{ $item->quantity }}</span>
                                                <span class="text-sm font-semibold text-blue-600">{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Cart Footer -->
                            <div class="border-t border-gray-200 p-4 bg-gray-50 rounded-b-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-sm font-medium text-gray-700">Tổng cộng ({{ $cartCount }} sản phẩm):</span>
                                    <span class="text-lg font-bold text-blue-600">{{ number_format($cart->getTotal(), 0, ',', '.') }}đ</span>
                                </div>
                                <a href="{{ route('cart.index') }}" class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                    Xem giỏ hàng
                                </a>
                            </div>
                        @else
                            <!-- Empty Cart -->
                            <div class="p-8 text-center">
                                <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-sm">Chưa có hàng nào trong giỏ!</p>
                                <a href="{{ route('products.index') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Mua sắm ngay →
                                </a>
                            </div>
                        @endif
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="md:hidden mt-4">
            <form action="{{ route('products.search') }}" method="GET" class="relative">
                <input type="text" 
                       id="mobile-search-input"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Tìm kiếm..." 
                       class="w-full px-4 py-2 pr-12 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       autocomplete="off">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-blue-600">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Mobile Search Suggestions -->
                <div id="mobile-search-suggestions" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50 max-h-80 overflow-y-auto">
                    <!-- Suggestions will be populated by JavaScript -->
                </div>
            </form>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="bg-gray-100 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <ul class="flex items-center justify-center gap-8 py-3 text-sm font-medium">
                <li><a href="/" class="text-gray-700 hover:text-blue-600 transition">TRANG CHỦ</a></li>
                
                <!-- Giới tính/Độ tuổi Dropdown -->
                <li class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 transition flex items-center gap-1">
                        GIỚI TÍNH/ĐỘ TUỔI
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ url('/gioi-tinh/nam') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Nam</a>
                        <a href="{{ url('/gioi-tinh/nu') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Nữ</a>
                        <a href="{{ url('/gioi-tinh/tre-em') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Trẻ em</a>
                    </div>
                </li>
                
                <!-- Thương hiệu Dropdown -->
                <li class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 transition flex items-center gap-1">
                        THƯƠNG HIỆU
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-40 bg-white rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ url('/thuong-hieu/nike') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Nike</a>
                        <a href="{{ url('/thuong-hieu/adidas') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Adidas</a>
                        <a href="{{ url('/thuong-hieu/puma') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Puma</a>
                        <a href="{{ url('/thuong-hieu/converse') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Converse</a>
                        <a href="{{ url('/thuong-hieu/vans') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Vans</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
