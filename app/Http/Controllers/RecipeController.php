<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(MenuItem $menu)
    {
        return $menu->recipes()->with('ingredient')->get();
    }

    public function store(Request $request, MenuItem $menu)
    {
        $data = $request->validate([
            'ingredient_name' => 'required|string',
            'unit' => 'required|in:g,ml,pcs',
            'qty_needed' => 'required|numeric|min:0.01',
        ]);

        // ✅ auto-create ingredient if missing
        $ingredient = Ingredient::firstOrCreate(
            ['name' => $data['ingredient_name']],
            ['unit' => $data['unit'], 'stock_qty' => 0, 'reorder_level' => 0]
        );

        $recipe = Recipe::updateOrCreate(
            ['menu_item_id' => $menu->id, 'ingredient_id' => $ingredient->id],
            ['qty_needed' => $data['qty_needed']]
        );

        return response()->json($recipe->load('ingredient'));
    }

    public function destroy(MenuItem $menu, Ingredient $ingredient)
    {
        Recipe::where('menu_item_id', $menu->id)
            ->where('ingredient_id', $ingredient->id)
            ->delete();

        return response()->json(['message' => 'Removed']);
    }
}

