<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'menu_item_id',
        'ingredient_id',
        'qty',
    ];

public function ingredient()
{
    return $this->belongsTo(\App\Models\Ingredient::class);
}

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}