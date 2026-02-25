<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Recipe;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Inventory page
     * - mode=ingredient (default): show/search ingredients
     * - mode=menu: show/search menu items and show their ingredients (recipes)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $mode = (string) $request->query('mode', 'ingredient'); // ingredient | menu

        // ✅ Always load ingredient stats for the dashboard boxes
        $ingredientsAll = Ingredient::orderBy('name')->get();

        $total = $ingredientsAll->count();
        $outOfStock = $ingredientsAll->where('stock_qty', '<=', 0)->count();
        $lowStock = $ingredientsAll->filter(fn ($i) => $i->stock_qty > 0 && $i->stock_qty <= $i->reorder_level)->count();

        $lowItems = $ingredientsAll->filter(fn ($i) => $i->stock_qty <= 0 || $i->stock_qty <= $i->reorder_level)->values();

        $recentMovements = StockMovement::with('ingredient')
            ->latest()
            ->take(10)
            ->get();

        // ✅ Variables for view (only one is used depending on mode)
        $ingredients = collect();
        $menuItems = collect();

        if ($mode === 'menu') {
            // Search menu items by name or menu_id
            $menuItems = MenuItem::query()
                ->when($q !== '', function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('menu_id', 'like', "%{$q}%");
                })
                // Load ingredients through recipes
                ->with(['recipes.ingredient'])
                ->orderBy('name')
                ->get();
        } else {
            // Default: ingredient search/list
            $ingredients = Ingredient::query()
                ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->get();
        }

        return view('admin.inventory', compact(
            'mode',
            'q',
            'ingredients',
            'menuItems',
            'total',
            'lowStock',
            'outOfStock',
            'lowItems',
            'recentMovements'
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

        // ✅ UPDATE AFFECTED MENUS (call ONCE)
        $this->recomputeMenusForIngredient($ingredient->id);

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

        // ✅ UPDATE AFFECTED MENUS
        $this->recomputeMenusForIngredient($ingredient->id);

        return back()->with('success', 'Stock deducted ✅');
    }

    /**
     * ✅ Recompute menu availability for all menus that use this ingredient.
     * Assumes recipes.menu_id (string) matches menu_items.menu_id
     */
    private function recomputeMenusForIngredient(int $ingredientId): void
    {
        $menuIds = Recipe::where('ingredient_id', $ingredientId)
            ->pluck('menu_id')
            ->unique()
            ->values();

        foreach ($menuIds as $menuId) {
            $this->recomputeOneMenuAvailability((string) $menuId);
        }
    }

    private function recomputeOneMenuAvailability(string $menuId): void
    {
        $menu = MenuItem::where('menu_id', $menuId)->first();
        if (!$menu) return;

        $recipes = Recipe::with('ingredient')->where('menu_id', $menuId)->get();

        $available = true;

        foreach ($recipes as $r) {
            $ing = $r->ingredient;
            if (!$ing) { $available = false; break; }

            // ✅ Keep your existing logic (uses Recipe::qty_needed accessor)
            $need   = (float) $r->qty_needed;
            $stock  = (float) $ing->stock_qty;
            $reorder = (float) $ing->reorder_level;

            // ✅ LOW/OUT disables menu
            if ($stock <= 0 || $stock <= $reorder || $stock < $need) {
                $available = false;
                break;
            }
        }

        $menu->update(['is_available' => $available ? 1 : 0]);
    }
}