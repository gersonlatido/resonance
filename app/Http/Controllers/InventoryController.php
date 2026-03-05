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
        $mode = (string) $request->query('mode', 'ingredient'); // ingredient | menu

        // ✅ NEW: movement date filter (YYYY-MM-DD)
        $movementDate = trim((string) $request->query('movement_date', ''));

        // ✅ Dashboard stats (fast)
        $total = Ingredient::count();
        $outOfStock = Ingredient::where('stock_qty', '<=', 0)->count();
        $lowStock = Ingredient::where('stock_qty', '>', 0)
            ->whereColumn('stock_qty', '<=', 'reorder_level')
            ->count();

        // ✅ Recent movements (NOW PAGINATED + FILTERABLE BY DATE)
        $movementsQuery = StockMovement::with('ingredient')->latest();

        if ($movementDate !== '') {
            $movementsQuery->whereDate('created_at', $movementDate);
        }

        $recentMovements = $movementsQuery
            ->paginate(10, ['*'], 'move_page')
            ->withQueryString();

        // ✅ Variables for view
        $ingredients = null;
        $menuItems = null;

        // ✅ Low stock alerts (paginated, separate page name to avoid conflict)
        $lowItems = null;
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
            $lowItems = collect(); // not used in menu mode
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
                ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->paginate(10, ['*'], 'ing_page')
                ->withQueryString();
        }

        return view('admin.inventory', compact(
            'mode',
            'q',
            'movementDate',
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

        $this->recomputeMenusForIngredient($ingredient->id);

        return back()->with('success', 'Stock deducted ✅');
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
        if (!$menu) return;

        $recipes = Recipe::with('ingredient')->where('menu_id', $menuId)->get();

        $available = true;

        foreach ($recipes as $r) {
            $ing = $r->ingredient;
            if (!$ing) { $available = false; break; }

            $need   = (float) $r->qty_needed;
            $stock  = (float) $ing->stock_qty;
            $reorder = (float) $ing->reorder_level;

            if ($stock <= 0 || $stock <= $reorder || $stock < $need) {
                $available = false;
                break;
            }
        }

        $menu->update(['is_available' => $available ? 1 : 0]);
    }
}