<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name','unit','stock_qty','reorder_level'];

    public function recipes()
    {
        return $this->hasMany(\App\Models\Recipe::class, 'ingredient_id', 'id');
    }

    public function movements()
    {
        return $this->hasMany(\App\Models\StockMovement::class);
    }
}

