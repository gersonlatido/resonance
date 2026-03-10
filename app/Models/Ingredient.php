<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock_qty',
        'reorder_level',
        'overstock_level',
    ];

    protected static function booted()
    {
        static::saved(function ($ingredient) {
            $menuIds = \App\Models\Recipe::where('ingredient_id', $ingredient->id)
                ->pluck('menu_id')
                ->unique();

            foreach ($menuIds as $menuId) {
                $recipes = \App\Models\Recipe::with('ingredient')
                    ->where('menu_id', $menuId)
                    ->get();

                if ($recipes->isEmpty()) {
                    \App\Models\MenuItem::where('menu_id', $menuId)->update([
                        'available_servings' => 0,
                        'is_available' => 0,
                    ]);
                    continue;
                }

                $servingsPossible = [];
                $available = true;

                foreach ($recipes as $recipe) {
                    $ing = $recipe->ingredient;

                    if (!$ing) {
                        $available = false;
                        $servingsPossible[] = 0;
                        continue;
                    }

                    $need = (float) ($recipe->qty_needed ?? 0);
                    $stock = (float) ($ing->stock_qty ?? 0);

                    if ($need > 0) {
                        $servingsPossible[] = floor($stock / $need);
                    }

                    if ($stock <= 0 || ($need > 0 && $stock < $need)) {
                        $available = false;
                    }
                }

                $availableServings = count($servingsPossible) > 0 ? min($servingsPossible) : 0;

                \App\Models\MenuItem::where('menu_id', $menuId)->update([
                    'available_servings' => $available ? $availableServings : 0,
                    'is_available' => $available ? 1 : 0,
                ]);
            }
        });
    }

    public function recipes()
    {
        return $this->hasMany(\App\Models\Recipe::class, 'ingredient_id', 'id');
    }

    public function movements()
    {
        return $this->hasMany(\App\Models\StockMovement::class);
    }
}