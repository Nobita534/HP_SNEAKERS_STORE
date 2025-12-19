<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route hiển thị tất cả sản phẩm
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');

// Route tìm kiếm sản phẩm
Route::get('/tim-kiem', [ProductController::class, 'search'])->name('products.search');

// Route API gợi ý tìm kiếm (AJAX)
Route::get('/api/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('products.search-suggestions');

// Route hiển thị sản phẩm theo thương hiệu
Route::get('/thuong-hieu/{brand}', [ProductController::class, 'byBrand'])->name('products.by-brand');

// Route hiển thị sản phẩm theo giới tính/độ tuổi
Route::get('/gioi-tinh/{gender}', [ProductController::class, 'byGender'])->name('products.by-gender');

// Route hiển thị chi tiết sản phẩm
Route::get('/san-pham/{id}', [ProductController::class, 'show'])->name('products.show');

// Cart Routes
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::put('/gio-hang/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/gio-hang/xoa-tat-ca', [CartController::class, 'clear'])->name('cart.clear');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/tai-khoan/chinh-sua', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/tai-khoan/cap-nhat', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/tai-khoan/doi-mat-khau', [ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::put('/tai-khoan/cap-nhat-mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Product Management
    Route::resource('products', AdminProductController::class);

    // User Management
    Route::resource('users', AdminUserController::class);

    // Order Management
    Route::resource('orders', AdminOrderController::class);
    Route::put('/orders/{id}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});

// VNPay Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/vnpay/payment', [PaymentController::class, 'vnpay_payment'])
        ->name('vnpayment');
});

// Return URL không cần auth vì VNPay gọi callback
Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])
    ->name('vnpay.return');
