<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';

    // ✅ primary key is menu_id (ex: MENU001)
    protected $primaryKey = 'menu_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'menu_id',
        'name',
        'image',
        'description',
        'price',
        'category',
        'is_available',
    ];

    // ✅ If recipes table uses menu_id too
    public function recipes()
    {
        return $this->hasMany(\App\Models\Recipe::class, 'menu_id', 'menu_id');
    }
}