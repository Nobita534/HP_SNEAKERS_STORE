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
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-24 right-4 z-[60] space-y-2"></div>
    
    <!-- Custom Confirm Dialog -->
    <div id="confirm-dialog" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[70]">
        <div class="bg-white rounded-lg shadow-2xl border-2 border-gray-200 max-w-md w-full mx-4 animate-fade-in">
            <div class="p-6">
                <h3 id="confirm-title" class="text-xl font-bold text-gray-800 mb-2"></h3>
                <p id="confirm-message" class="text-gray-600 mb-6"></p>
                <div class="flex gap-3 justify-end">
                    <button id="confirm-cancel" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Hủy
                    </button>
                    <button id="confirm-ok" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    
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

    <!-- Search Autocomplete Script -->
    <script>
        // Desktop search
        const searchInput = document.getElementById('search-input');
        const searchSuggestions = document.getElementById('search-suggestions');
        
        // Mobile search
        const mobileSearchInput = document.getElementById('mobile-search-input');
        const mobileSearchSuggestions = document.getElementById('mobile-search-suggestions');
        
        let searchTimeout;

        function setupSearchAutocomplete(inputElement, suggestionsElement) {
            if (!inputElement) return;

            inputElement.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const keyword = this.value.trim();

                if (keyword.length < 2) {
                    suggestionsElement.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`/api/search-suggestions?q=${encodeURIComponent(keyword)}`)
                        .then(response => response.json())
                        .then(data => {
                            displaySuggestions(data, suggestionsElement);
                        })
                        .catch(error => console.error('Search error:', error));
                }, 300);
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!inputElement.contains(e.target) && !suggestionsElement.contains(e.target)) {
                    suggestionsElement.classList.add('hidden');
                }
            });
        }

        function displaySuggestions(data, suggestionsElement) {
            if (!data.products || data.products.length === 0) {
                suggestionsElement.classList.add('hidden');
                return;
            }

            let html = '';

            // Display brands
            if (data.brands && data.brands.length > 0) {
                html += '<div class="p-3 border-b border-gray-200">';
                html += '<div class="text-xs font-semibold text-gray-500 mb-2">THƯƠNG HIỆU</div>';
                data.brands.forEach(brand => {
                    html += `
                        <a href="${brand.url}" class="block px-3 py-2 hover:bg-gray-100 rounded-lg transition">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tag text-blue-600"></i>
                                <span class="font-medium">${brand.name}</span>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
            }

            // Display products
            html += '<div class="p-3">';
            html += '<div class="text-xs font-semibold text-gray-500 mb-2">SẢN PHẨM</div>';
            data.products.forEach(product => {
                html += `
                    <a href="${product.url}" class="flex items-center gap-3 px-3 py-2 hover:bg-gray-100 rounded-lg transition">
                        <img src="${product.image}" alt="${product.name}" class="w-12 h-12 object-cover rounded">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-800 line-clamp-1">${product.name}</div>
                            <div class="text-xs text-gray-500">${product.brand}</div>
                        </div>
                        <div class="text-sm font-bold text-blue-600">${product.price}đ</div>
                    </a>
                `;
            });
            html += '</div>';

            suggestionsElement.innerHTML = html;
            suggestionsElement.classList.remove('hidden');
        }

        // Initialize autocomplete for both desktop and mobile
        setupSearchAutocomplete(searchInput, searchSuggestions);
        setupSearchAutocomplete(mobileSearchInput, mobileSearchSuggestions);
    </script>
    
    <script>
    // Toast Notification System
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = 'animate-fade-in';
        
        const colors = {
            success: 'bg-green-600',
            error: 'bg-red-600',
            info: 'bg-blue-600',
            warning: 'bg-orange-600'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-triangle'
        };
        
        toast.innerHTML = `
            <div class="${colors[type]} text-white shadow-xl rounded-lg overflow-hidden max-w-md">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <i class="fas ${icons[type]} text-xl mr-3"></i>
                        <span class="font-medium">${message}</span>
                    </div>
                    <button onclick="this.closest('.animate-fade-in').remove()" class="ml-4 text-white hover:text-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.5s ease-out';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    };
    
    // Custom Confirm Dialog
    window.showConfirm = function(title, message, onConfirm, onCancel) {
        const dialog = document.getElementById('confirm-dialog');
        const titleEl = document.getElementById('confirm-title');
        const messageEl = document.getElementById('confirm-message');
        const okBtn = document.getElementById('confirm-ok');
        const cancelBtn = document.getElementById('confirm-cancel');
        
        titleEl.textContent = title;
        messageEl.textContent = message;
        dialog.classList.remove('hidden');
        
        // Remove old listeners
        const newOkBtn = okBtn.cloneNode(true);
        const newCancelBtn = cancelBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        
        // Add new listeners
        newOkBtn.addEventListener('click', () => {
            dialog.classList.add('hidden');
            if (onConfirm) onConfirm();
        });
        
        newCancelBtn.addEventListener('click', () => {
            dialog.classList.add('hidden');
            if (onCancel) onCancel();
        });
    };
    
    // Format price to Vietnamese currency format
    window.formatPrice = function(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    };
    
    // Update Cart UI dynamically
    window.updateCartUI = function(data) {
        // Update cart count badge
        const badge = document.querySelector('.cart-count');
        if (badge) {
            badge.textContent = data.cart_count;
        } else if (data.cart_count > 0) {
            // Create badge if doesn't exist
            const cartLink = document.querySelector('a[href="{{ route("cart.index") }}"]');
            if (cartLink) {
                const newBadge = document.createElement('span');
                newBadge.className = 'cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center';
                newBadge.textContent = data.cart_count;
                cartLink.appendChild(newBadge);
            }
        }
        
        // Check if cart was empty before
        const emptyState = document.querySelector('.cart-empty-state');
        const dropdownContent = document.getElementById('cart-dropdown-content');
        
        if (emptyState && data.cart_items && data.cart_items.length > 0) {
            // Cart was empty, now has items - rebuild entire dropdown
            let html = `
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Sản phẩm mới thêm</h3>
                    <div class="cart-items-container space-y-3 max-h-64 overflow-y-auto">
            `;
            
            data.cart_items.forEach(item => {
                html += `
                    <div class="flex gap-3 items-start hover:bg-gray-50 p-2 rounded-lg transition">
                        <img src="${item.product_image}" 
                             alt="${item.product_name}" 
                             class="w-16 h-16 object-cover rounded-lg">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-800 truncate">${item.product_name}</h4>
                            <p class="text-xs text-gray-500">Size: ${item.size}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-gray-600">SL: ${item.quantity}</span>
                                <span class="text-sm font-semibold text-blue-600">${formatPrice(item.subtotal)}đ</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
                <div class="border-t border-gray-200 p-4 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-medium text-gray-700">Tổng cộng (<span class="cart-count-text">${data.cart_count}</span> sản phẩm):</span>
                        <span class="cart-total text-lg font-bold text-blue-600">${formatPrice(data.cart_total)}đ</span>
                    </div>
                    <a href="{{ route('cart.index') }}" class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        Xem giỏ hàng
                    </a>
                </div>
            `;
            
            dropdownContent.innerHTML = html;
        } else {
            // Cart already has items, just update the existing elements
            const itemsContainer = document.querySelector('.cart-items-container');
            if (itemsContainer && data.cart_items && data.cart_items.length > 0) {
                let html = '';
                data.cart_items.forEach(item => {
                    html += `
                        <div class="flex gap-3 items-start hover:bg-gray-50 p-2 rounded-lg transition">
                            <img src="${item.product_image}" 
                                 alt="${item.product_name}" 
                                 class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-800 truncate">${item.product_name}</h4>
                                <p class="text-xs text-gray-500">Size: ${item.size}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-600">SL: ${item.quantity}</span>
                                    <span class="text-sm font-semibold text-blue-600">${formatPrice(item.subtotal)}đ</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                itemsContainer.innerHTML = html;
                
                // Update total price
                const totalElement = document.querySelector('.cart-total');
                if (totalElement && data.cart_total) {
                    totalElement.textContent = formatPrice(data.cart_total) + 'đ';
                }
                
                // Update count text in footer
                const countText = document.querySelector('.cart-count-text');
                if (countText) {
                    countText.textContent = data.cart_count;
                }
            }
        }
    };
    </script>
    
    @stack('scripts')
</body>
</html>
