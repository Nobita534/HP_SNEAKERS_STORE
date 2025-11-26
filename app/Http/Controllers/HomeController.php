<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $newProducts = Product::where('is_new', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->take(3)
            ->get();

        $categories = Category::where('is_active', true)->get();

        return view('home', compact('newProducts', 'featuredProducts', 'categories'));
    }
}
