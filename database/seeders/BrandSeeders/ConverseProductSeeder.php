<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ConverseProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 2,
                'name' => 'Converse Chuck Taylor All Star',
                'slug' => 'converse-chuck-taylor-all-star',
                'description' => 'Giày Converse Chuck Taylor classic không bao giờ lỗi mốt',
                'price' => 1500000,
                'sale_price' => 1200000,
                'brand' => 'Converse',
                'image' => 'image/converse/150.jpg',
                'images' => json_encode(['image/converse/150.jpg', 'image/converse/151.jpg', 'image/converse/152.jpg']),
                'sizes' => json_encode(['35', '36', '37', '38', '39', '40']),
                'color' => 'Trắng',
                'stock' => 100,
                'is_featured' => true,
                'is_new' => false,
                'rating' => 4.8,
                'reviews_count' => 567,
            ],
            [
                'category_id' => 1,
                'name' => 'Converse Chuck 70 High',
                'slug' => 'converse-chuck-70-high',
                'description' => 'Converse Chuck 70 phiên bản cao cổ premium',
                'price' => 1800000,
                'sale_price' => 1440000,
                'brand' => 'Converse',
                'image' => 'image/converse/153.jpg',
                'images' => json_encode(['image/converse/153.jpg', 'image/converse/154.jpg', 'image/converse/155.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Đen',
                'stock' => 65,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.6,
                'reviews_count' => 234,
            ],
            [
                'category_id' => 2,
                'name' => 'Converse One Star',
                'slug' => 'converse-one-star',
                'description' => 'Converse One Star với thiết kế tối giản',
                'price' => 1600000,
                'sale_price' => 1280000,
                'brand' => 'Converse',
                'image' => 'image/converse/156.jpg',
                'images' => json_encode(['image/converse/156.jpg', 'image/converse/157.jpg', 'image/converse/158.jpg']),
                'sizes' => json_encode(['35', '36', '37', '38', '39']),
                'color' => 'Hồng',
                'stock' => 55,
                'is_featured' => false,
                'is_new' => false,
                'rating' => 4.4,
                'reviews_count' => 178,
            ],
            [
                'category_id' => 3,
                'name' => 'Converse Chuck Taylor Trẻ Em',
                'slug' => 'converse-chuck-taylor-tre-em',
                'description' => 'Converse Chuck Taylor dành cho trẻ em',
                'price' => 1200000,
                'sale_price' => 960000,
                'brand' => 'Converse',
                'image' => 'image/converse/159.jpg',
                'images' => json_encode(['image/converse/159.jpg', 'image/converse/160.jpg', 'image/converse/161.jpg']),
                'sizes' => json_encode(['28', '29', '30', '31', '32', '33']),
                'color' => 'Xanh dương',
                'stock' => 45,
                'is_featured' => false,
                'is_new' => true,
                'rating' => 4.5,
                'reviews_count' => 89,
            ],
            [
                'category_id' => 1,
                'name' => 'Converse Run Star Hike',
                'slug' => 'converse-run-star-hike',
                'description' => 'Converse Run Star Hike với đế răng cưa độc đáo',
                'price' => 2100000,
                'sale_price' => 1680000,
                'brand' => 'Converse',
                'image' => 'image/converse/162.jpg',
                'images' => json_encode(['image/converse/162.jpg', 'image/converse/163.jpg', 'image/converse/164.jpg', 'image/converse/165.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'color' => 'Đen trắng',
                'stock' => 40,
                'is_featured' => true,
                'is_new' => true,
                'rating' => 4.7,
                'reviews_count' => 145,
            ],
            ['category_id' => 1, 'name' => 'Converse Pro Leather', 'slug' => 'converse-pro-leather', 'description' => 'Converse Pro Leather với phong cách basketball vintage', 'price' => 1900000, 'sale_price' => 1520000, 'brand' => 'Converse', 'image' => 'image/converse/166.jpg', 'images' => json_encode(['image/converse/166.jpg', 'image/converse/167.jpg', 'image/converse/168.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Trắng đen', 'stock' => 67, 'is_featured' => false, 'is_new' => true, 'rating' => 4.4, 'reviews_count' => 123],
            ['category_id' => 2, 'name' => 'Converse Chuck 70 Low', 'slug' => 'converse-chuck-70-low', 'description' => 'Converse Chuck 70 Low phiên bản thấp cổ', 'price' => 1700000, 'sale_price' => 1360000, 'brand' => 'Converse', 'image' => 'image/converse/169.jpg', 'images' => json_encode(['image/converse/169.jpg', 'image/converse/170.jpg', 'image/converse/171.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39', '40']), 'color' => 'Xám nhạt', 'stock' => 73, 'is_featured' => false, 'is_new' => false, 'rating' => 4.6, 'reviews_count' => 256],
            ['category_id' => 1, 'name' => 'Converse All Star BB Evo', 'slug' => 'converse-all-star-bb-evo', 'description' => 'Converse All Star BB Evo giày bóng rổ hiện đại', 'price' => 2800000, 'sale_price' => 2240000, 'brand' => 'Converse', 'image' => 'image/converse/172.jpg', 'images' => json_encode(['image/converse/172.jpg', 'image/converse/173.jpg', 'image/converse/174.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43', '44']), 'color' => 'Đen xanh', 'stock' => 38, 'is_featured' => true, 'is_new' => true, 'rating' => 4.7, 'reviews_count' => 189],
            ['category_id' => 2, 'name' => 'Converse Platform Chuck Taylor', 'slug' => 'converse-platform-chuck-taylor', 'description' => 'Converse Platform Chuck Taylor với đế cao', 'price' => 1800000, 'sale_price' => 1440000, 'brand' => 'Converse', 'image' => 'image/converse/175.jpg', 'images' => json_encode(['image/converse/175.jpg', 'image/converse/176.jpg', 'image/converse/177.jpg', 'image/converse/178.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39']), 'color' => 'Trắng', 'stock' => 61, 'is_featured' => true, 'is_new' => true, 'rating' => 4.5, 'reviews_count' => 298],
            ['category_id' => 1, 'name' => 'Converse Weapon CX', 'slug' => 'converse-weapon-cx', 'description' => 'Converse Weapon CX phong cách basketball retro', 'price' => 2200000, 'sale_price' => 1760000, 'brand' => 'Converse', 'image' => 'image/converse/179.jpg', 'images' => json_encode(['image/converse/179.jpg', 'image/converse/180.jpg', 'image/converse/181.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Đỏ trắng', 'stock' => 44, 'is_featured' => false, 'is_new' => false, 'rating' => 4.3, 'reviews_count' => 167],
            ['category_id' => 2, 'name' => 'Converse Jack Purcell', 'slug' => 'converse-jack-purcell', 'description' => 'Converse Jack Purcell với nụ cười đặc trưng', 'price' => 1650000, 'sale_price' => 1320000, 'brand' => 'Converse', 'image' => 'image/converse/182.jpg', 'images' => json_encode(['image/converse/182.jpg', 'image/converse/183.jpg', 'image/converse/184.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39', '40']), 'color' => 'Xanh nhạt', 'stock' => 52, 'is_featured' => false, 'is_new' => false, 'rating' => 4.4, 'reviews_count' => 145],
            ['category_id' => 1, 'name' => 'Converse ERX 260', 'slug' => 'converse-erx-260', 'description' => 'Converse ERX 260 với thiết kế mid-top năng động', 'price' => 2100000, 'sale_price' => 1680000, 'brand' => 'Converse', 'image' => 'image/converse/185.jpg', 'images' => json_encode(['image/converse/185.jpg', 'image/converse/186.jpg', 'image/converse/187.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Đen vàng', 'stock' => 48, 'is_featured' => false, 'is_new' => true, 'rating' => 4.5, 'reviews_count' => 178],
            ['category_id' => 3, 'name' => 'Converse Simple Slip Kids', 'slug' => 'converse-simple-slip-kids', 'description' => 'Converse Simple Slip dành cho trẻ em', 'price' => 1100000, 'sale_price' => 880000, 'brand' => 'Converse', 'image' => 'image/converse/188.jpg', 'images' => json_encode(['image/converse/188.jpg', 'image/converse/189.jpg', 'image/converse/190.jpg']), 'sizes' => json_encode(['28', '29', '30', '31', '32', '33', '34']), 'color' => 'Hồng', 'stock' => 82, 'is_featured' => false, 'is_new' => true, 'rating' => 4.6, 'reviews_count' => 201],
            ['category_id' => 2, 'name' => 'Converse Renew Cotton', 'slug' => 'converse-renew-cotton', 'description' => 'Converse Renew Cotton với chất liệu thân thiện môi trường', 'price' => 1750000, 'sale_price' => 1400000, 'brand' => 'Converse', 'image' => 'image/converse/191.jpg', 'images' => json_encode(['image/converse/191.jpg', 'image/converse/192.jpg', 'image/converse/193.jpg']), 'sizes' => json_encode(['35', '36', '37', '38', '39']), 'color' => 'Be tự nhiên', 'stock' => 56, 'is_featured' => true, 'is_new' => true, 'rating' => 4.7, 'reviews_count' => 234],
            ['category_id' => 1, 'name' => 'Converse Skidgrip', 'slug' => 'converse-skidgrip', 'description' => 'Converse Skidgrip slip-on classic vintage', 'price' => 1550000, 'sale_price' => 1240000, 'brand' => 'Converse', 'image' => 'image/converse/194.jpg', 'images' => json_encode(['image/converse/194.jpg', 'image/converse/195.jpg', 'image/converse/196.jpg']), 'sizes' => json_encode(['39', '40', '41', '42', '43']), 'color' => 'Đen', 'stock' => 69, 'is_featured' => false, 'is_new' => false, 'rating' => 4.2, 'reviews_count' => 112],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
