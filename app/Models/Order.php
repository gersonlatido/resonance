<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'external_id',
        'table_number',
        'status',
        'eta_minutes',
        'payment_status',
        'total',
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}
