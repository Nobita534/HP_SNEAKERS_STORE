<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = [
        'product_id', 
        'size', 
        'quantity',
        'total_imported',
        'total_sold',
        'average_cost_per_size'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_imported' => 'integer',
        'total_sold' => 'integer',
        'average_cost_per_size' => 'decimal:2'
    ];

    /**
     * Relationship: ProductSize belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if this size is available for requested quantity
     * 
     * @param int $requestedQuantity
     * @return bool
     */
    public function isAvailable($requestedQuantity = 1)
    {
        return $this->quantity >= $requestedQuantity;
    }

    /**
     * Decrease quantity (for orders)
     * 
     * @param int $amount
     * @return void
     */
    public function decreaseQuantity($amount)
    {
        if ($amount > $this->quantity) {
            throw new \Exception("Cannot decrease quantity by {$amount}. Only {$this->quantity} available.");
        }
        $this->decrement('quantity', $amount);
    }

    /**
     * Increase quantity (for restocking/cancellations)
     * 
     * @param int $amount
     * @return void
     */
    public function increaseQuantity($amount)
    {
        $this->increment('quantity', $amount);
    }

    /**
     * Check if stock is low (below threshold)
     * 
     * @param int $threshold
     * @return bool
     */
    public function isLowStock($threshold = 5)
    {
        return $this->quantity > 0 && $this->quantity <= $threshold;
    }
}
