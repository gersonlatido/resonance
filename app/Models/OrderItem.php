<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'menu_id',
        'name',
        'unit_price',
        'quantity',
        'image',
        'subtotal',   // ✅ required by your DB
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
