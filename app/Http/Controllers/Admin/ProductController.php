<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'productSizes'])->orderBy('id', 'asc')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'required|in:Nike,Adidas,Puma,Converse,Vans',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_new' => 'required|in:0,1',
        ]);

        $validated['is_featured'] = $request->has('is_featured') ? (bool)$request->is_featured : false;

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        // Lấy thông tin category để tạo folder
        $category = Category::find($request->category_id);
        $categorySlug = $category->slug ?? 'other';

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $validated['slug'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            // Tạo đường dẫn theo category
            $categoryPath = public_path('images/products/' . $categorySlug);
            
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists($categoryPath)) {
                mkdir($categoryPath, 0755, true);
            }
            
            // Di chuyển file vào thư mục
            $image->move($categoryPath, $imageName);
            
            // Lưu đường dẫn tương đối vào database
            $validated['image'] = 'images/products/' . $categorySlug . '/' . $imageName;
        }

        // Tạo sản phẩm
        $product = Product::create($validated);

        // Tự động tạo các size từ 35 đến 45 với số lượng = 0
        for ($size = 35; $size <= 45; $size++) {
            $product->productSizes()->create([
                'size' => (string)$size,
                'quantity' => 0
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công! Các size 35-45 đã được tạo với số lượng 0.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('productSizes')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'required|in:Nike,Adidas,Puma,Converse,Vans',
            'price' => 'required|numeric|min:0',
            'sizes' => 'required|array|min:1',
            'sizes.*.size' => 'required|string|max:10',
            'sizes.*.quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured') ? (bool)$request->is_featured : false;
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        // Xử lý upload ảnh mới nếu có
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            
            // Upload ảnh mới
            $category = Category::find($request->category_id);
            $categorySlug = $category->slug ?? 'other';
            
            $image = $request->file('image');
            $imageName = $validated['slug'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            
            $categoryPath = public_path('images/products/' . $categorySlug);
            
            if (!file_exists($categoryPath)) {
                mkdir($categoryPath, 0755, true);
            }
            
            $image->move($categoryPath, $imageName);
            $validated['image'] = 'images/products/' . $categorySlug . '/' . $imageName;
        }

        $product->update($validated);

        // Sync sizes: Xóa tất cả size cũ và tạo mới
        $product->productSizes()->delete();
        foreach ($request->sizes as $sizeData) {
            $product->productSizes()->create([
                'size' => trim($sizeData['size']),
                'quantity' => $sizeData['quantity']
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Xóa file ảnh nếu tồn tại
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        
        // Xóa sản phẩm khỏi database
        $product->delete();
        
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
}
