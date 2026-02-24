<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * STRICT:
     * - If no recipe => cannot make
     * - If any ingredient stock < qty_needed*qty => cannot make
     */
    private function canMakeMenu(string $menuId, int $qty = 1): bool
    {
        $lines = Recipe::with('ingredient')
            ->where('menu_id', $menuId)
            ->get();

        if ($lines->isEmpty()) return false;

        foreach ($lines as $line) {
            $need  = (float) $line->qty_needed * $qty;
            $stock = (float) ($line->ingredient->stock_qty ?? 0);

            if ($stock < $need) return false;
        }

        return true;
    }

    /**
     * LOW STOCK RULE:
     * If ANY ingredient stock <= reorder_level => low stock
     * STRICT:
     * - If no recipe => treat as low stock (so button disabled)
     */
    private function isLowStockMenu(string $menuId): bool
    {
        $lines = Recipe::with('ingredient')
            ->where('menu_id', $menuId)
            ->get();

        if ($lines->isEmpty()) return true;

        foreach ($lines as $line) {
            $stock   = (float) ($line->ingredient->stock_qty ?? 0);
            $reorder = (float) ($line->ingredient->reorder_level ?? 0);

            if ($reorder > 0 && $stock <= $reorder) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add can_make + low_stock flags into items (Blade + API)
     */
    private function attachAvailability($items)
    {
        return $items->map(function ($item) {
            $item->can_make  = $this->canMakeMenu($item->menu_id, 1);
            $item->low_stock = $this->isLowStockMenu($item->menu_id);

            return $item;
        });
    }

    // ===== Customer menu pages (Blade) =====

    public function breakfast()
    {
        $items = MenuItem::where('category', 'All Day Breakfast')->get();
        $items = $this->attachAvailability($items);
        return view('all-day-breakfast-menu', compact('items'));
    }

    public function mainCourses()
    {
        $items = MenuItem::where('category', 'main-courses')->get();
        $items = $this->attachAvailability($items);
        return view('main-courses-menu', compact('items'));
    }

    public function pasta()
    {
        $items = MenuItem::where('category', 'pasta')->get();
        $items = $this->attachAvailability($items);
        return view('pasta-menu', compact('items'));
    }

    public function chicken()
    {
        $items = MenuItem::where('category', 'chicken-wings')->get();
        $items = $this->attachAvailability($items);
        return view('chicken-menu', compact('items'));
    }

    public function drinks()
    {
        $items = MenuItem::whereIn('category', [
            'frappuccino',
            'coffee-based',
            'milk-based'
        ])->get();

        $items = $this->attachAvailability($items);
        return view('drinks-menu', compact('items'));
    }

    public function pizza()
    {
        $items = MenuItem::where('category', 'overload-premium')->get();
        $items = $this->attachAvailability($items);
        return view('pizza-menu', compact('items'));
    }

    public function snacks()
    {
        $items = MenuItem::where('category', 'snacks')->get();
        $items = $this->attachAvailability($items);
        return view('snacks-menu', compact('items'));
    }

    // ===== API: GET /api/menu =====
    public function index()
    {
        $items = MenuItem::orderBy('created_at', 'desc')->get();
        $items = $this->attachAvailability($items);

        return response()->json($items);
    }

    // ===== API: POST /api/menu =====
    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_id' => 'required|string|unique:menu_items,menu_id',
            'name' => 'required|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $item = MenuItem::create($data);
        $item->can_make  = $this->canMakeMenu($item->menu_id, 1);
        $item->low_stock = $this->isLowStockMenu($item->menu_id);

        return response()->json($item, 201);
    }

    // ===== API: PUT /api/menu/{menu_id} =====
    public function update(Request $request, $menu_id)
    {
        $item = MenuItem::where('menu_id', $menu_id)->firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'image' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric',
            'category' => 'sometimes|required|string',
        ]);

        $item->update($data);

        $item->can_make  = $this->canMakeMenu($item->menu_id, 1);
        $item->low_stock = $this->isLowStockMenu($item->menu_id);

        return response()->json($item);
    }

    // ===== API: DELETE /api/menu/{menu_id} =====
    public function destroy($menu_id)
    {
        $item = MenuItem::where('menu_id', $menu_id)->firstOrFail();
        $item->delete();

        return response()->json(['message' => 'Deleted']);
    }
}