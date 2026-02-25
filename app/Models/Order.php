<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'external_id',
        'table_number',
        'status',
        'eta_minutes',
        'payment_status',
        'inventory_deducted_at',
        'total',
    ];

    protected $casts = [
        'inventory_deducted_at' => 'datetime',
    ];

    /**
     * Auto-generate external_id if missing.
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->external_id)) {
                $order->external_id = 'order_' . now()->timestamp . '_' . Str::lower(Str::random(12));
            }
        });
    }

    /**
     * Main relationship used by inventory deduction.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * ✅ Alias relationship for existing code that calls $order->items or with('items')
     * Fixes: "undefined relationship [items]"
     */
    public function items()
    {
        return $this->orderItems();
    }
}