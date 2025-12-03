<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route hiển thị tất cả sản phẩm
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');

// Route hiển thị sản phẩm theo thương hiệu
Route::get('/thuong-hieu/{brand}', [ProductController::class, 'byBrand'])->name('products.by-brand');

// Route hiển thị chi tiết sản phẩm
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Product Management
    Route::resource('products', AdminProductController::class);

    // User Management
    Route::resource('users', AdminUserController::class);

    // Order Management
    Route::resource('orders', AdminOrderController::class);
});
