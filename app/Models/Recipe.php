<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['menu_id','ingredient_id','qty_needed'];

    public function menuItem()
    {
        return $this->belongsTo(\App\Models\MenuItem::class, 'menu_id', 'menu_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(\App\Models\Ingredient::class, 'ingredient_id', 'id');
    }
}

