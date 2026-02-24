<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('name')->get();

        $total = $ingredients->count();
        $outOfStock = $ingredients->where('stock_qty', '<=', 0)->count();
        $lowStock = $ingredients->filter(fn($i) => $i->stock_qty > 0 && $i->stock_qty <= $i->reorder_level)->count();

        $lowItems = $ingredients->filter(fn($i) => $i->stock_qty <= 0 || $i->stock_qty <= $i->reorder_level)->values();

        $recentMovements = StockMovement::with('ingredient')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.inventory', compact(
        'ingredients','total','lowStock','outOfStock','lowItems','recentMovements'
        ));
    }

    public function storeIngredient(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:ingredients,name',
            'unit' => 'required|in:g,ml,pcs',
            'stock_qty' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
        ]);

        Ingredient::create($data);

        return redirect()->route('admin.inventory')->with('success', 'Ingredient added ✅');
    }

    public function stockIn(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $ingredient->stock_qty = $ingredient->stock_qty + $data['qty'];
        $ingredient->save();

        StockMovement::create([
            'ingredient_id' => $ingredient->id,
            'type' => 'in',
            'qty' => $data['qty'],
            'reason' => $data['reason'] ?? 'Restock',
        ]);

        return back()->with('success', 'Stock added ✅');
    }

    public function stockOut(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($ingredient->stock_qty < $data['qty']) {
            return back()->with('error', 'Not enough stock ❌');
        }

        $ingredient->stock_qty = $ingredient->stock_qty - $data['qty'];
        $ingredient->save();

        StockMovement::create([
            'ingredient_id' => $ingredient->id,
            'type' => 'out',
            'qty' => $data['qty'],
            'reason' => $data['reason'] ?? 'Manual deduction',
        ]);

        return back()->with('success', 'Stock deducted ✅');
    }
}
