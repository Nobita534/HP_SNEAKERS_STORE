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
        'average_cost_per_size',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'total_imported' => 'integer',
        'total_sold' => 'integer',
        'average_cost_per_size' => 'decimal:2',
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
     * @param  int  $requestedQuantity
     * @return bool
     */
    public function isAvailable($requestedQuantity = 1)
    {
        return $this->quantity >= $requestedQuantity;
    }

    /**
     * Trừ kho trên số tồn hiện tại trong database, không dùng số đã đọc vào model.
     * Nơi gọi phải dùng transaction nếu cần cập nhật cả Order và Inventory.
     */
    public function decreaseQuantity(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Số lượng mua phải lớn hơn 0.');
        }

        // Điều kiện đủ tồn và phép trừ nằm trong cùng một câu UPDATE.
        $affectedRows = $this->newQuery()
            ->whereKey($this->getKey())
            ->where('quantity', '>=', $amount)
            ->decrement('quantity', $amount);

        if ($affectedRows === 0) {
            throw new \RuntimeException('Không đủ tồn kho hoặc variant không còn tồn tại.');
        }

        $this->refresh();
    }

    /**
     * Increase quantity (for restocking/cancellations)
     *
     * @param  int  $amount
     * @return void
     */
    public function increaseQuantity($amount)
    {
        $this->increment('quantity', $amount);
    }

    /**
     * Check if stock is low (below threshold)
     *
     * @param  int  $threshold
     * @return bool
     */
    public function isLowStock($threshold = 5)
    {
        return $this->quantity > 0 && $this->quantity <= $threshold;
    }
}
