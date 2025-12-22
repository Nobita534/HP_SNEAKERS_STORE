<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'average_cost',
        'profit_margin',
        'brand',
        'image',
        'images',
        'color',
        'is_featured',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'rating' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Product has many ProductSizes
     */
    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    /**
     * Get total stock across all sizes
     * 
     * @return int
     */
    public function getTotalStock()
    {
        return $this->productSizes()->sum('quantity');
    }

    /**
     * Get quantity for a specific size
     * 
     * @param string $size
     * @return int
     */
    public function getSizeQuantity($size)
    {
        $productSize = $this->productSizes()->where('size', $size)->first();
        return $productSize ? $productSize->quantity : 0;
    }

    /**
     * Get all available sizes (with stock > 0)
     * 
     * @return array
     */
    public function getAvailableSizes()
    {
        return $this->productSizes()->where('quantity', '>', 0)->pluck('size')->toArray();
    }

    /**
     * Check if product has a specific size
     * 
     * @param string $size
     * @return bool
     */
    public function hasSize($size)
    {
        return $this->productSizes()->where('size', $size)->exists();
    }

    /**
     * Check if a size is available with requested quantity
     * 
     * @param string $size
     * @param int $quantity
     * @return bool
     */
    public function isSizeAvailable($size, $quantity = 1)
    {
        $productSize = $this->productSizes()->where('size', $size)->first();
        return $productSize && $productSize->quantity >= $quantity;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->sale_price && $this->price > $this->sale_price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }
}
