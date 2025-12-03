<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class AdidasProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Adidas Ultraboost 23',
                'slug' => 'adidas-ultraboost-23',
                'description' => 'Adidas Ultraboost với công nghệ Boost đỉnh cao',
                'price' => 4200000,
                'sale_price' => 3360000,
                'brand' => 'Adidas',
                'image' => 'image/adidas/120.jpg',
                'images' => json_encode(['image/adidas/120.jpg', 'image/adidas/121.jpg', 'image/adidas/122.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43', '44']),
                'color' => 'Trắng xám',
                'stock' => 40,
                'is_featured' => true,
                'is_new' => true,
                'rating' => 4.8,
                'reviews_count' => 276,
            ],
            [
                'category_id' => 1,
                'name' => 'Adidas Stan Smith',
                'slug' => 'adidas-stan-smith',
                'description' => 'Adidas Stan Smith classic không thể thiếu',
                'price' => 2500000,
                'sale_price' => 2000000,
                'brand' => 'Adidas',
                'image' => 'image/adidas/123.jpg',
                'images' => json_encode(['image/adidas/123.jpg', 'image/adidas/124.jpg', 'image/adidas/125.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Trắng xanh',
                'stock' => 80,
                'is_featured' => true,
                'is_new' => false,
                'rating' => 4.7,
                'reviews_count' => 412,
            ],
            [
                'category_id' => 1,
                'name' => 'Adidas NMD R1',
                'slug' => 'adidas-nmd-r1',
                'description' => 'Adidas NMD R1 phong cách streetwear',
                'price' => 3300000,
                'sale_price' => 2640000,
                'brand' => 'Adidas',
                'image' => 'image/adidas/126.jpg',
                'images' => json_encode(['image/adidas/126.jpg', 'image/adidas/127.jpg', 'image/adidas/128.jpg', 'image/adidas/129.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Đen đỏ',
                'stock' => 45,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.6,
                'reviews_count' => 189,
            ],
            [
                'category_id' => 2,
                'name' => 'Adidas Superstar Nữ',
                'slug' => 'adidas-superstar-nu',
                'description' => 'Adidas Superstar dành cho nữ, phong cách hip-hop',
                'price' => 2300000,
                'sale_price' => 1840000,
                'brand' => 'Adidas',
                'image' => 'image/adidas/130.jpg',
                'images' => json_encode(['image/adidas/130.jpg', 'image/adidas/131.jpg', 'image/adidas/132.jpg']),
                'sizes' => json_encode(['35', '36', '37', '38', '39', '40']),
                'color' => 'Trắng đen',
                'stock' => 70,
                'is_featured' => true,
                'is_new' => false,
                'rating' => 4.5,
                'reviews_count' => 298,
            ],
            [
                'category_id' => 1,
                'name' => 'Adidas Samba OG',
                'slug' => 'adidas-samba-og',
                'description' => 'Adidas Samba OG với thiết kế bóng đá cổ điển',
                'price' => 2700000,
                'sale_price' => 2160000,
                'brand' => 'Adidas',
                'image' => 'image/adidas/133.jpg',
                'images' => json_encode(['image/adidas/133.jpg', 'image/adidas/134.jpg', 'image/adidas/135.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Đen trắng',
                'stock' => 50,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.7,
                'reviews_count' => 165,
            ],
            ['category_id' => 1, 'name' => 'Adidas Forum Low', 'slug' => 'adidas-forum-low', 'description' => 'Adidas Forum Low với thiết kế basketball heritage', 'price' => 2800000, 'sale_price' => 2240000, 'brand' => 'Adidas', 'image' => 'image/adidas/136.jpg', 'images' => json_encode(['image/adidas/136.jpg', 'image/adidas/137.jpg', 'image/adidas/138.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Trắng vàng', 'stock' => 58, 'is_featured' => false, 'is_new' => true, 'rating' => 4.5, 'reviews_count' => 145],
            ['category_id' => 2, 'name' => 'Adidas Gazelle', 'slug' => 'adidas-gazelle', 'description' => 'Adidas Gazelle phong cách retro suede', 'price' => 2400000, 'sale_price' => 1920000, 'brand' => 'Adidas', 'image' => 'image/adidas/139.jpg', 'images' => json_encode(['image/adidas/139.jpg', 'image/adidas/140.jpg', 'image/adidas/141.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39', '40']), 'color' => 'Xanh navy', 'stock' => 64, 'is_featured' => true, 'is_new' => false, 'rating' => 4.7, 'reviews_count' => 298],
            ['category_id' => 1, 'name' => 'Adidas Yeezy Boost 350', 'slug' => 'adidas-yeezy-boost-350', 'description' => 'Adidas Yeezy Boost 350 V2 limited edition', 'price' => 5500000, 'sale_price' => 4400000, 'brand' => 'Adidas', 'image' => 'image/adidas/142.jpg', 'images' => json_encode(['image/adidas/142.jpg', 'image/adidas/143.jpg', 'image/adidas/144.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Xám zebra', 'stock' => 22, 'is_featured' => true, 'is_new' => true, 'rating' => 4.9, 'reviews_count' => 456],
            ['category_id' => 1, 'name' => 'Adidas ZX 2K Boost', 'slug' => 'adidas-zx-2k-boost', 'description' => 'Adidas ZX 2K Boost với công nghệ Boost 2.0', 'price' => 3100000, 'sale_price' => 2480000, 'brand' => 'Adidas', 'image' => 'image/adidas/145.jpg', 'images' => json_encode(['image/adidas/145.jpg', 'image/adidas/146.jpg', 'image/adidas/147.jpg', 'image/adidas/148.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43', '44']), 'color' => 'Đen cam', 'stock' => 47, 'is_featured' => false, 'is_new' => true, 'rating' => 4.6, 'reviews_count' => 187],
            ['category_id' => 2, 'name' => 'Adidas Continental 80', 'slug' => 'adidas-continental-80', 'description' => 'Adidas Continental 80 phong cách tennis vintage', 'price' => 2600000, 'sale_price' => 2080000, 'brand' => 'Adidas', 'image' => 'image/adidas/149.jpg', 'images' => json_encode(['image/adidas/149.jpg', 'image/adidas/150.jpg', 'image/adidas/151.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39']), 'color' => 'Trắng hồng', 'stock' => 55, 'is_featured' => false, 'is_new' => false, 'rating' => 4.4, 'reviews_count' => 213],
            ['category_id' => 1, 'name' => 'Adidas Predator Edge', 'slug' => 'adidas-predator-edge', 'description' => 'Adidas Predator Edge giày đá bóng chuyên nghiệp', 'price' => 3800000, 'sale_price' => 3040000, 'brand' => 'Adidas', 'image' => 'image/adidas/152.jpg', 'images' => json_encode(['image/adidas/152.jpg', 'image/adidas/153.jpg', 'image/adidas/154.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Đỏ đen', 'stock' => 36, 'is_featured' => true, 'is_new' => true, 'rating' => 4.8, 'reviews_count' => 276],
            ['category_id' => 1, 'name' => 'Adidas Terrex Swift', 'slug' => 'adidas-terrex-swift', 'description' => 'Adidas Terrex Swift giày leo núi outdoor', 'price' => 3400000, 'sale_price' => 2720000, 'brand' => 'Adidas', 'image' => 'image/adidas/155.jpg', 'images' => json_encode(['image/adidas/155.jpg', 'image/adidas/156.jpg', 'image/adidas/157.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43', '44']), 'color' => 'Xám xanh', 'stock' => 41, 'is_featured' => false, 'is_new' => true, 'rating' => 4.7, 'reviews_count' => 165],
            ['category_id' => 2, 'name' => 'Adidas Falcon', 'slug' => 'adidas-falcon', 'description' => 'Adidas Falcon với thiết kế chunky 90s', 'price' => 2900000, 'sale_price' => 2320000, 'brand' => 'Adidas', 'image' => 'image/adidas/158.jpg', 'images' => json_encode(['image/adidas/158.jpg', 'image/adidas/159.jpg', 'image/adidas/160.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39', '40']), 'color' => 'Hồng đa sắc', 'stock' => 49, 'is_featured' => false, 'is_new' => false, 'rating' => 4.3, 'reviews_count' => 192],
            ['category_id' => 3, 'name' => 'Adidas RapidaRun', 'slug' => 'adidas-rapidarun', 'description' => 'Adidas RapidaRun dành cho trẻ em năng động', 'price' => 1500000, 'sale_price' => 1200000, 'brand' => 'Adidas', 'image' => 'image/adidas/161.jpg', 'images' => json_encode(['image/adidas/161.jpg', 'image/adidas/162.jpg', 'image/adidas/163.jpg']), 'sizes' => json_encode(['28', '29', '30', '31', '32', '33']), 'color' => 'Xanh dương', 'stock' => 78, 'is_featured' => false, 'is_new' => true, 'rating' => 4.5, 'reviews_count' => 134],
            ['category_id' => 1, 'name' => 'Adidas X Speedflow', 'slug' => 'adidas-x-speedflow', 'description' => 'Adidas X Speedflow giày bóng đá tốc độ', 'price' => 4000000, 'sale_price' => 3200000, 'brand' => 'Adidas', 'image' => 'image/adidas/164.jpg', 'images' => json_encode(['image/adidas/164.jpg', 'image/adidas/165.jpg', 'image/adidas/166.jpg', 'image/adidas/167.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Vàng cam', 'stock' => 32, 'is_featured' => true, 'is_new' => true, 'rating' => 4.8, 'reviews_count' => 289],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
