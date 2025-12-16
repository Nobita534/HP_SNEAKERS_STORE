<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nam',
                'slug' => 'giay-nam',
                'description' => 'Bộ sưu tập giày thể thao dành cho nam',
                'icon' => 'fa-mars',
                'is_active' => 1,
            ],
            [
                'name' => 'Nữ',
                'slug' => 'giay-nu',
                'description' => 'Bộ sưu tập giày thể thao dành cho nữ',
                'icon' => 'fa-venus',
                'is_active' => 1,
            ],
            [
                'name' => 'Trẻ Em',
                'slug' => 'giay-tre-em',
                'description' => 'Giày thể thao cho trẻ em',
                'icon' => 'fa-child',
                'is_active' => 1,
            ],
            [
                'name' => 'Sale',
                'slug' => 'giay-sale',
                'description' => 'Sản phẩm giảm giá',
                'icon' => 'fa-fire',
                'is_active' => 1,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
