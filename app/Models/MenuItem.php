<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';

    protected $primaryKey = 'menu_id';
    public $incrementing = false;     // ✅ STRING KEY, NOT AUTO INT
    protected $keyType = 'string';

    
    protected $fillable = [
        'menu_id',
        'name',
        'image',
        'description',
        'price',
        'category'
    ];

    // ✅ AUTO GENERATE MENU001, MENU002, etc.
    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $lastItem = self::orderBy('menu_id', 'desc')->first(); // <- change here

        if (!$lastItem) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr($lastItem->menu_id, 4);
            $nextNumber = $lastNumber + 1;
        }

        $model->menu_id = 'MENU' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // <- also use menu_id
    });
}

}
