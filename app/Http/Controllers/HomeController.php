<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Sản phẩm mới nhất (30 ngày gần đây)
        $newProducts = Product::where('is_active', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->take(3)
            ->get();

        // Top 5 sản phẩm có tồn kho cao nhất
        $topStockProducts = Product::where('is_active', true)
            ->withSum('productSizes', 'quantity')
            ->orderBy('product_sizes_sum_quantity', 'desc')
            ->take(5)
            ->get();

        $categories = Category::where('is_active', true)->get();

        return view('home', compact('newProducts', 'featuredProducts', 'topStockProducts', 'categories'));
    }
}
