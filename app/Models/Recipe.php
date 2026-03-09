<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'recipes';

    protected $fillable = [
        'menu_id',
        'ingredient_id',
        'qty_needed',
        'unit', 
    ];

    /**
     * Recipe belongs to one Ingredient
     */
    public function ingredient()
    {
        return $this->belongsTo(\App\Models\Ingredient::class, 'ingredient_id');
    }

    /**
     * Recipe belongs to one MenuItem (menu_id is string PK)
     */
    public function menuItem()
    {
        return $this->belongsTo(\App\Models\MenuItem::class, 'menu_id', 'menu_id');
    }
}