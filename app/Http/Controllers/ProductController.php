<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Hiển thị tất cả sản phẩm với phân trang
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Lấy tất cả sản phẩm với phân trang 9 sản phẩm/trang
        $products = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        // Lấy tổng số sản phẩm
        $totalProducts = Product::where('is_active', true)->count();

        return view('products.index', compact('products', 'totalProducts'));
    }

    /**
     * Hiển thị danh sách sản phẩm theo thương hiệu với phân trang
     * 
     * @param string $brand Tên thương hiệu (có thể là slug hoặc tên gốc)
     * @return \Illuminate\View\View
     */
    public function byBrand($brand)
    {
        // Chuyển đổi slug về tên thương hiệu gốc
        // Ví dụ: 'new-balance' -> 'New Balance'
        $brandName = $this->convertSlugToBrandName($brand);

        // Lấy danh sách sản phẩm theo thương hiệu với phân trang 6 sản phẩm/trang
        $products = Product::where('brand', 'LIKE', $brandName)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        // Kiểm tra xem có sản phẩm nào không
        if ($products->isEmpty() && $products->currentPage() === 1) {
            // Thử tìm kiếm không phân biệt hoa thường
            $products = Product::whereRaw('LOWER(brand) = ?', [strtolower($brandName)])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(6);
        }

        // Lấy tổng số sản phẩm của thương hiệu
        $totalProducts = Product::where('brand', 'LIKE', $brandName)
            ->where('is_active', true)
            ->count();

        return view('products.by-brand', compact('products', 'brandName', 'totalProducts'));
    }

    /**
     * Chuyển đổi slug thành tên thương hiệu gốc
     * 
     * @param string $slug
     * @return string
     */
    private function convertSlugToBrandName($slug)
    {
        // Danh sách mapping từ slug sang tên thương hiệu chính xác
        $brandMapping = [
            'nike' => 'Nike',
            'adidas' => 'Adidas',
            'puma' => 'Puma',
            'converse' => 'Converse',
            'vans' => 'Vans',
            'new-balance' => 'New Balance',
            'reebok' => 'Reebok',
            'asics' => 'Asics',
        ];

        $slugLower = strtolower($slug);

        // Nếu có trong mapping, trả về tên chính xác
        if (isset($brandMapping[$slugLower])) {
            return $brandMapping[$slugLower];
        }

        // Nếu không có trong mapping, chuyển đổi slug thành title case
        // Ví dụ: 'new-balance' -> 'New Balance'
        return ucwords(str_replace('-', ' ', $slug));
    }

    /**
     * Hiển thị chi tiết sản phẩm
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Lấy sản phẩm liên quan cùng thương hiệu
        $relatedProducts = Product::where('brand', $product->brand)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
