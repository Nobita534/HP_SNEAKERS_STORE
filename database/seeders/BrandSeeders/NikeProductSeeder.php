<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class NikeProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Nike Air Max 2024',
                'slug' => 'nike-air-max-2024',
                'description' => 'Giày Nike Air Max mới nhất với công nghệ đệm khí tiên tiến',
                'price' => 3500000,
                'sale_price' => 2800000,
                'brand' => 'Nike',
                'image' => 'image/nike/100.jpg',
                'images' => json_encode(['image/nike/100.jpg', 'image/nike/101.jpg', 'image/nike/102.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Đen trắng',
                'stock' => 50,
                'is_featured' => true,
                'is_new' => true,
                'rating' => 4.8,
                'reviews_count' => 156,
            ],
            [
                'category_id' => 1,
                'name' => 'Nike Air Force 1',
                'slug' => 'nike-air-force-1',
                'description' => 'Thiết kế cổ điển Nike Air Force 1 không bao giờ lỗi thời',
                'price' => 2900000,
                'sale_price' => 2320000,
                'brand' => 'Nike',
                'image' => 'image/nike/103.jpg',
                'images' => json_encode(['image/nike/103.jpg', 'image/nike/104.jpg', 'image/nike/105.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43', '44']),
                'color' => 'Trắng',
                'stock' => 75,
                'is_featured' => true,
                'is_new' => false,
                'rating' => 4.9,
                'reviews_count' => 342,
            ],
            [
                'category_id' => 1,
                'name' => 'Nike React Infinity',
                'slug' => 'nike-react-infinity',
                'description' => 'Giày chạy bộ Nike React với độ êm ái tối ưu',
                'price' => 3200000,
                'sale_price' => 2560000,
                'brand' => 'Nike',
                'image' => 'image/nike/106.jpg',
                'images' => json_encode(['image/nike/106.jpg', 'image/nike/107.jpg', 'image/nike/108.jpg', 'image/nike/109.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Xanh dương',
                'stock' => 45,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.7,
                'reviews_count' => 98,
            ],
            [
                'category_id' => 2,
                'name' => 'Nike Cortez Nữ',
                'slug' => 'nike-cortez-nu',
                'description' => 'Nike Cortez phong cách retro dành cho nữ',
                'price' => 2400000,
                'sale_price' => 1920000,
                'brand' => 'Nike',
                'image' => 'image/nike/110.jpg',
                'images' => json_encode(['image/nike/110.jpg', 'image/nike/111.jpg', 'image/nike/112.jpg']),
                'sizes' => json_encode(['35', '36', '37', '38', '39', '40']),
                'color' => 'Hồng trắng',
                'stock' => 60,
                'is_featured' => true,
                'is_new' => true,
                'rating' => 4.6,
                'reviews_count' => 187,
            ],
            [
                'category_id' => 1,
                'name' => 'Nike Pegasus 40',
                'slug' => 'nike-pegasus-40',
                'description' => 'Nike Pegasus 40 - giày chạy bộ đa năng',
                'price' => 3100000,
                'sale_price' => 2790000,
                'brand' => 'Nike',
                'image' => 'image/nike/113.jpg',
                'images' => json_encode(['image/nike/113.jpg', 'image/nike/114.jpg', 'image/nike/115.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Xám đen',
                'stock' => 55,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.5,
                'reviews_count' => 123,
            ],
            ['category_id' => 1, 'name' => 'Nike Zoom Fly 5', 'slug' => 'nike-zoom-fly-5', 'description' => 'Giày chạy bộ Nike Zoom Fly 5 với đệm ZoomX responsive', 'price' => 3800000, 'sale_price' => 3040000, 'brand' => 'Nike', 'image' => 'image/nike/116.jpg', 'images' => json_encode(['image/nike/116.jpg', 'image/nike/117.jpg', 'image/nike/118.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Xanh neon', 'stock' => 35, 'is_featured' => false, 'is_new' => true, 'rating' => 4.6, 'reviews_count' => 87],
            ['category_id' => 2, 'name' => 'Nike Air Max 97', 'slug' => 'nike-air-max-97', 'description' => 'Nike Air Max 97 với thiết kế sóng nước đặc trưng', 'price' => 4200000, 'sale_price' => 3360000, 'brand' => 'Nike', 'image' => 'image/nike/119.jpg', 'images' => json_encode(['image/nike/119.jpg', 'image/nike/120.jpg', 'image/nike/121.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39']), 'color' => 'Bạc đen', 'stock' => 42, 'is_featured' => true, 'is_new' => false, 'rating' => 4.8, 'reviews_count' => 234],
            ['category_id' => 1, 'name' => 'Nike Revolution 6', 'slug' => 'nike-revolution-6', 'description' => 'Giày chạy bộ Nike Revolution 6 giá cả phải chăng', 'price' => 1800000, 'sale_price' => 1440000, 'brand' => 'Nike', 'image' => 'image/nike/122.jpg', 'images' => json_encode(['image/nike/122.jpg', 'image/nike/123.jpg', 'image/nike/124.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43', '44']), 'color' => 'Xám trắng', 'stock' => 88, 'is_featured' => false, 'is_new' => false, 'rating' => 4.3, 'reviews_count' => 156],
            ['category_id' => 1, 'name' => 'Nike Blazer Mid 77', 'slug' => 'nike-blazer-mid-77', 'description' => 'Nike Blazer Mid 77 phong cách vintage basketball', 'price' => 2800000, 'sale_price' => 2240000, 'brand' => 'Nike', 'image' => 'image/nike/125.jpg', 'images' => json_encode(['image/nike/125.jpg', 'image/nike/126.jpg', 'image/nike/127.jpg', 'image/nike/128.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Trắng đen', 'stock' => 52, 'is_featured' => false, 'is_new' => true, 'rating' => 4.5, 'reviews_count' => 178],
            ['category_id' => 2, 'name' => 'Nike Dunk Low Retro', 'slug' => 'nike-dunk-low-retro', 'description' => 'Nike Dunk Low Retro phiên bản nữ màu sắc trendy', 'price' => 3200000, 'sale_price' => 2560000, 'brand' => 'Nike', 'image' => 'image/nike/129.jpg', 'images' => json_encode(['image/nike/129.jpg', 'image/nike/130.jpg', 'image/nike/131.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39', '40']), 'color' => 'Hồng pastel', 'stock' => 38, 'is_featured' => true, 'is_new' => true, 'rating' => 4.7, 'reviews_count' => 289],
            ['category_id' => 1, 'name' => 'Nike Air Presto', 'slug' => 'nike-air-presto', 'description' => 'Nike Air Presto với thiết kế slip-on thoải mái', 'price' => 2600000, 'sale_price' => 2080000, 'brand' => 'Nike', 'image' => 'image/nike/132.jpg', 'images' => json_encode(['image/nike/132.jpg', 'image/nike/133.jpg', 'image/nike/134.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Đen xám', 'stock' => 45, 'is_featured' => false, 'is_new' => false, 'rating' => 4.4, 'reviews_count' => 123],
            ['category_id' => 1, 'name' => 'Nike ZoomX Vaporfly', 'slug' => 'nike-zoomx-vaporfly', 'description' => 'Nike ZoomX Vaporfly giày marathon chuyên nghiệp', 'price' => 5200000, 'sale_price' => 4160000, 'brand' => 'Nike', 'image' => 'image/nike/135.jpg', 'images' => json_encode(['image/nike/135.jpg', 'image/nike/136.jpg', 'image/nike/137.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Xanh vàng', 'stock' => 25, 'is_featured' => true, 'is_new' => true, 'rating' => 4.9, 'reviews_count' => 412],
            ['category_id' => 2, 'name' => 'Nike Waffle Debut', 'slug' => 'nike-waffle-debut', 'description' => 'Nike Waffle Debut với đế waffle retro', 'price' => 2100000, 'sale_price' => 1680000, 'brand' => 'Nike', 'image' => 'image/nike/138.jpg', 'images' => json_encode(['image/nike/138.jpg', 'image/nike/139.jpg', 'image/nike/140.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39']), 'color' => 'Kem be', 'stock' => 58, 'is_featured' => false, 'is_new' => false, 'rating' => 4.2, 'reviews_count' => 94],
            ['category_id' => 1, 'name' => 'Nike Air Max 90', 'slug' => 'nike-air-max-90', 'description' => 'Nike Air Max 90 classic với cửa sổ khí iconic', 'price' => 3400000, 'sale_price' => 2720000, 'brand' => 'Nike', 'image' => 'image/nike/141.jpg', 'images' => json_encode(['image/nike/141.jpg', 'image/nike/142.jpg', 'image/nike/143.jpg', 'image/nike/144.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43', '44']), 'color' => 'Trắng đỏ', 'stock' => 67, 'is_featured' => true, 'is_new' => false, 'rating' => 4.8, 'reviews_count' => 345],
            ['category_id' => 3, 'name' => 'Nike Dynamo Go', 'slug' => 'nike-dynamo-go', 'description' => 'Nike Dynamo Go dành cho trẻ em năng động', 'price' => 1600000, 'sale_price' => 1280000, 'brand' => 'Nike', 'image' => 'image/nike/145.jpg', 'images' => json_encode(['image/nike/145.jpg', 'image/nike/146.jpg', 'image/nike/147.jpg']), 'sizes' => json_encode(['28', '29', '30', '31', '32', '33']), 'color' => 'Xanh hồng', 'stock' => 72, 'is_featured' => false, 'is_new' => true, 'rating' => 4.6, 'reviews_count' => 128],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
