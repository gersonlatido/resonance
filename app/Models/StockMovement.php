<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['ingredient_id','type','qty','reason'];

    public function ingredient()
    {
        return $this->belongsTo(\App\Models\Ingredient::class);
    }
}

