<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Recipe;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $mode = (string) $request->query('mode', 'ingredient');
        $status = trim((string) $request->query('status', ''));
        $movementDate = trim((string) $request->query('movement_date', ''));

        $total = Ingredient::count();

        $outOfStock = Ingredient::where('stock_qty', '<=', 0)->count();

        $lowStock = Ingredient::where('stock_qty', '>', 0)
            ->whereColumn('stock_qty', '<=', 'reorder_level')
            ->count();

        $overstock = Ingredient::whereColumn('stock_qty', '>', 'overstock_level')
            ->where('overstock_level', '>', 0)
            ->count();

        $movementsQuery = StockMovement::with('ingredient')->latest();

        if ($movementDate !== '') {
            $movementsQuery->whereDate('created_at', $movementDate);
        }

        $recentMovements = $movementsQuery
            ->paginate(10, ['*'], 'move_page')
            ->withQueryString();

        $ingredients = null;
        $menuItems = null;

        if ($mode === 'ingredient') {
            $lowItems = Ingredient::query()
                ->where(function ($qq) {
                    $qq->where('stock_qty', '<=', 0)
                        ->orWhereColumn('stock_qty', '<=', 'reorder_level');
                })
                ->orderBy('name')
                ->paginate(10, ['*'], 'low_page')
                ->withQueryString();
        } else {
            $lowItems = collect();
        }

        if ($mode === 'menu') {
            $menuItems = MenuItem::query()
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")
                            ->orWhere('menu_id', 'like', "%{$q}%");
                    });
                })
                ->with(['recipes.ingredient'])
                ->orderBy('name')
                ->paginate(10, ['*'], 'menu_page')
                ->withQueryString();
        } else {
            $ingredients = Ingredient::query()
                ->when($q !== '', function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->when($status !== '', function ($query) use ($status) {
                    if ($status === 'healthy') {
                        $query->whereColumn('stock_qty', '>', 'reorder_level')
                            ->where(function ($qq) {
                                $qq->where('overstock_level', '<=', 0)
                                    ->orWhereColumn('stock_qty', '<=', 'overstock_level');
                            });
                    } elseif ($status === 'low') {
                        $query->where('stock_qty', '>', 0)
                            ->whereColumn('stock_qty', '<=', 'reorder_level');
                    } elseif ($status === 'out') {
                        $query->where('stock_qty', '<=', 0);
                    } elseif ($status === 'overstock') {
                        $query->where('overstock_level', '>', 0)
                            ->whereColumn('stock_qty', '>', 'overstock_level');
                    }
                })
                ->orderBy('name')
                ->paginate(10, ['*'], 'ing_page')
                ->withQueryString();
        }

        return view('admin.inventory', compact(
            'mode',
            'q',
            'status',
            'movementDate',
            'ingredients',
            'menuItems',
            'total',
            'lowStock',
            'outOfStock',
            'overstock',
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
            'overstock_level' => 'required|numeric|min:0|gte:reorder_level',
        ]);

        $ingredient = Ingredient::create($data);

        $this->recomputeMenusForIngredient($ingredient->id);

        return redirect()->route('admin.inventory')->with('success', 'Ingredient added ✅');
    }

    public function updateIngredient(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:ingredients,name,' . $ingredient->id,
            'unit' => 'required|in:g,ml,pcs',
            'reorder_level' => 'required|numeric|min:0',
            'overstock_level' => 'required|numeric|min:0|gte:reorder_level',
        ]);

        $ingredient->update($data);

        $this->recomputeMenusForIngredient($ingredient->id);

        return redirect()->route('admin.inventory')->with('success', 'Ingredient updated ✅');
    }

    public function stockIn(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $ingredient->stock_qty = (float) $ingredient->stock_qty + (float) $data['qty'];
        $ingredient->save();

        StockMovement::create([
            'ingredient_id' => $ingredient->id,
            'type' => 'in',
            'qty' => $data['qty'],
            'reason' => $data['reason'] ?? 'Restock',
        ]);

        $this->recomputeMenusForIngredient($ingredient->id);

        return back()->with('success', 'Stock added ✅');
    }

    public function stockOut(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'qty' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        if ((float) $ingredient->stock_qty < (float) $data['qty']) {
            return back()->with('error', 'Not enough stock ❌');
        }

        $ingredient->stock_qty = (float) $ingredient->stock_qty - (float) $data['qty'];
        $ingredient->save();

        StockMovement::create([
            'ingredient_id' => $ingredient->id,
            'type' => 'out',
            'qty' => $data['qty'],
            'reason' => $data['reason'] ?? 'Manual deduction',
        ]);

        $this->recomputeMenusForIngredient($ingredient->id);

        return back()->with('success', 'Stock deducted ✅');
    }

    public function recomputeAllMenusAvailability(): void
    {
        $menuIds = Recipe::query()
            ->pluck('menu_id')
            ->unique()
            ->values();

        foreach ($menuIds as $menuId) {
            $this->recomputeOneMenuAvailability((string) $menuId);
        }
    }

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

        if (!$menu) {
            return;
        }

        $recipes = Recipe::with('ingredient')
            ->where('menu_id', $menuId)
            ->get();

        if ($recipes->isEmpty()) {
            $menu->update(['is_available' => 0]);
            return;
        }

        $available = true;

        foreach ($recipes as $recipe) {
            $ingredient = $recipe->ingredient;

            if (!$ingredient) {
                $available = false;
                break;
            }

            $need = (float) ($recipe->qty_needed ?? 0);
            $stock = (float) ($ingredient->stock_qty ?? 0);
            $reorder = (float) ($ingredient->reorder_level ?? 0);

            $isOutOfStock = $stock <= 0;
            $isLowStock = $stock <= $reorder;
            $notEnoughForRecipe = $need > 0 && $stock < $need;

            if ($isOutOfStock || $isLowStock || $notEnoughForRecipe) {
                $available = false;
                break;
            }
        }

        $menu->update([
            'is_available' => $available ? 1 : 0,
        ]);
    }
}