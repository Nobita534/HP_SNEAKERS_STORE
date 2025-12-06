<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * Get the user that owns the cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate cart total
     */
    public function getTotal()
    {
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Get total items count
     */
    public function getTotalItems()
    {
        return $this->items->sum('quantity');
    }
}
