<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VnpayTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_ref',
        'amount',
        'order_info',
        'bank_code',
        'status',
        'vnpay_transaction_no',
        'response_code',
        'response_data',
        'ip_address',
        'paid_at',
        'order_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'response_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
