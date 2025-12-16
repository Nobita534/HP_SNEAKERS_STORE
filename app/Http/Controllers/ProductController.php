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
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Xử lý sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(9)->appends(['sort' => $sort]);

        // Lấy tổng số sản phẩm
        $totalProducts = Product::where('is_active', true)->count();

        return view('products.index', compact('products', 'totalProducts', 'sort'));
    }

    /**
     * Hiển thị danh sách sản phẩm theo thương hiệu với phân trang
     * 
     * @param string $brand Tên thương hiệu (có thể là slug hoặc tên gốc)
     * @return \Illuminate\View\View
     */
    public function byBrand(Request $request, $brand)
    {
        // Chuyển đổi slug về tên thương hiệu gốc
        $brandName = $this->convertSlugToBrandName($brand);

        $query = Product::where('brand', 'LIKE', $brandName)
            ->where('is_active', true);

        // Xử lý sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(6)->appends(['sort' => $sort]);

        // Kiểm tra xem có sản phẩm nào không
        if ($products->isEmpty() && $products->currentPage() === 1) {
            // Thử tìm kiếm không phân biệt hoa thường
            $query = Product::whereRaw('LOWER(brand) = ?', [strtolower($brandName)])
                ->where('is_active', true);

            // Áp dụng lại sắp xếp
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $products = $query->paginate(6)->appends(['sort' => $sort]);
        }

        // Lấy tổng số sản phẩm của thương hiệu
        $totalProducts = Product::where('brand', 'LIKE', $brandName)
            ->where('is_active', true)
            ->count();

        return view('products.by-brand', compact('products', 'brandName', 'totalProducts', 'sort'));
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
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $product = Product::with('productSizes')
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        // Lấy sản phẩm liên quan cùng thương hiệu
        $relatedProducts = Product::with('productSizes')
            ->where('brand', $product->brand)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Hiển thị danh sách sản phẩm theo giới tính/độ tuổi
     * 
     * @param string $gender
     * @return \Illuminate\View\View
     */
    public function byGender(Request $request, $gender)
    {
        // Map gender từ URL sang category name trong DB
        $categoryMap = [
            'nam' => 'Nam',
            'nu' => 'Nữ',
            'tre-em' => 'Trẻ Em'
        ];

        if (!isset($categoryMap[$gender])) {
            abort(404);
        }

        $categoryName = $categoryMap[$gender];

        // Tìm category theo name
        $category = \App\Models\Category::where('name', $categoryName)->first();

        if (!$category) {
            abort(404);
        }

        $query = Product::where('category_id', $category->id)
            ->where('is_active', true);

        // Xử lý sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(6)->appends(['sort' => $sort]);

        // Tổng số sản phẩm
        $totalProducts = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->count();

        return view('products.by-gender', compact('products', 'categoryName', 'totalProducts', 'gender', 'sort'));
    }

    /**
     * Tìm kiếm sản phẩm theo tên hoặc thương hiệu
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        $query = Product::where('is_active', true);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('brand', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Xử lý sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(9)->appends(['q' => $keyword, 'sort' => $sort]);
        $totalProducts = $query->count();

        return view('products.search', compact('products', 'keyword', 'totalProducts', 'sort'));
    }

    /**
     * API: Gợi ý tìm kiếm (AJAX autocomplete)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchSuggestions(Request $request)
    {
        $keyword = $request->get('q', '');

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        // Tìm sản phẩm theo tên hoặc mô tả
        $products = Product::where('name', 'like', "%{$keyword}%")
            ->orWhere('brand', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%")
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'price' => number_format($product->price, 0, ',', '.'),
                    'image' => asset($product->image), // ← Thêm asset() ở đây
                    'url' => route('products.show', $product->id)
                ];
            });

        // Tìm thương hiệu phù hợp
        $brands = Product::select('brand')
            ->where('brand', 'like', "%{$keyword}%")
            ->groupBy('brand')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->brand,
                    'url' => route('products.by-brand', \Illuminate\Support\Str::slug($item->brand))
                ];
            });

        return response()->json([
            'products' => $products,
            'brands' => $brands
        ]);
    }
}
