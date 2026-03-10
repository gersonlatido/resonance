<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function deductIngredientsForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {

            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($order->inventory_deducted_at) {
                Log::info('[INV] skip already deducted', [
                    'order_code' => $order->order_code,
                ]);
                return;
            }

            if (($order->payment_status ?? null) !== 'paid') {
                Log::info('[INV] skip not paid', [
                    'order_code' => $order->order_code,
                    'payment_status' => $order->payment_status,
                ]);
                return;
            }

            $order->load('items');

            $ingredientPk = (new Ingredient())->getKeyName();

            $deductMap = [];
            $menusMatched = [];

            foreach ($order->items as $item) {
                $menuId = (string) ($item->menu_id ?? '');
                $qtyOrdered = (int) ($item->qty ?? 1);

                if ($menuId === '' || $qtyOrdered < 1) {
                    continue;
                }

                $recipes = Recipe::where('menu_id', $menuId)->get();

                if ($recipes->isEmpty()) {
                    Log::warning('[INV] NO RECIPE ROWS FOUND', [
                        'order_code' => $order->order_code,
                        'menu_id' => $menuId,
                    ]);
                    continue;
                }

                $menusMatched[] = $menuId;

                foreach ($recipes as $recipe) {
                    $ingredientId = $recipe->ingredient_id ?? null;
                    $perItemQty = (float) ($recipe->qty_needed ?? 0);

                    if (!$ingredientId || $perItemQty <= 0) {
                        continue;
                    }

                    $need = $perItemQty * $qtyOrdered;
                    $deductMap[$ingredientId] = ($deductMap[$ingredientId] ?? 0) + $need;
                }
            }

            if (empty($deductMap)) {
                Log::error('[INV] deductMap EMPTY', [
                    'order_code' => $order->order_code,
                ]);
                return;
            }

            $ingredientIds = array_keys($deductMap);

            $ingredients = Ingredient::whereIn($ingredientPk, $ingredientIds)
                ->lockForUpdate()
                ->get()
                ->keyBy($ingredientPk);

            foreach ($deductMap as $ingredientId => $need) {
                $ingredient = $ingredients->get($ingredientId);

                if (!$ingredient) {
                    throw new \Exception("Ingredient not found: {$ingredientId}");
                }

                $current = (float) ($ingredient->stock_qty ?? 0);

                if ($current < $need) {
                    throw new \Exception("Not enough stock for {$ingredient->name}. Need {$need}, have {$current}");
                }
            }

            foreach ($deductMap as $ingredientId => $need) {
                $ingredient = $ingredients->get($ingredientId);

                $ingredient->stock_qty = (float) $ingredient->stock_qty - (float) $need;
                $ingredient->save();

                StockMovement::create([
                    'ingredient_id' => $ingredientId,
                    'type' => 'out',
                    'qty' => $need,
                    'reason' => 'Order consumption',
                ]);
            }

            $this->recomputeMenus(array_values(array_unique($menusMatched)));

            $order->update([
                'inventory_deducted_at' => now(),
            ]);

            Log::info('[INV] deduction SUCCESS', [
                'order_code' => $order->order_code,
                'menus_recomputed' => array_values(array_unique($menusMatched)),
            ]);
        });
    }

    private function recomputeMenus(array $menuIds): void
    {
        foreach ($menuIds as $menuId) {
            $menu = MenuItem::where('menu_id', $menuId)->first();

            if (!$menu) {
                continue;
            }

            $recipes = Recipe::with('ingredient')
                ->where('menu_id', $menuId)
                ->get();

            if ($recipes->isEmpty()) {
                $menu->update([
                    'is_available' => 0,
                    'available_servings' => 0,
                ]);
                continue;
            }

            $servingsPossible = [];
            $available = true;

            foreach ($recipes as $recipe) {
                $ingredient = $recipe->ingredient;

                if (!$ingredient) {
                    $available = false;
                    $servingsPossible[] = 0;
                    continue;
                }

                $need = (float) ($recipe->qty_needed ?? 0);
                $stock = (float) ($ingredient->stock_qty ?? 0);

                if ($need > 0) {
                    $servingsPossible[] = floor($stock / $need);
                }

                // Available as long as at least 1 full serving can still be made
                if ($stock <= 0 || ($need > 0 && $stock < $need)) {
                    $available = false;
                }
            }

            $availableServings = count($servingsPossible) > 0 ? min($servingsPossible) : 0;

            $menu->update([
                'available_servings' => $available ? $availableServings : 0,
                'is_available' => $available ? 1 : 0,
            ]);
        }
    }
}