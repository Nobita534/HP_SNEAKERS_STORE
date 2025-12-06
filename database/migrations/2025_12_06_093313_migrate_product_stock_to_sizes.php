<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductSize;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing product stock and sizes data to product_sizes table
        $products = Product::whereNotNull('sizes')->get();
        
        foreach ($products as $product) {
            $sizes = json_decode($product->sizes, true);
            
            // If no sizes defined, skip
            if (empty($sizes) || !is_array($sizes)) {
                continue;
            }
            
            // Calculate stock per size (distribute evenly)
            $totalStock = $product->stock ?? 0;
            $sizeCount = count($sizes);
            $stockPerSize = $sizeCount > 0 ? floor($totalStock / $sizeCount) : 0;
            $remainder = $sizeCount > 0 ? $totalStock % $sizeCount : 0;
            
            foreach ($sizes as $index => $size) {
                // Give remainder to first size
                $quantity = $stockPerSize + ($index === 0 ? $remainder : 0);
                
                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => trim($size),
                    'quantity' => $quantity
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all product sizes data
        ProductSize::truncate();
    }
};
