<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function deductIngredientsForOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {

            // lock order
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            // already deducted
            if ($order->inventory_deducted_at) {
                Log::info('[INV] skip already deducted', ['order_code' => $order->order_code]);
                return;
            }

            // must be paid
            if (($order->payment_status ?? null) !== 'paid') {
                Log::info('[INV] skip not paid', [
                    'order_code' => $order->order_code,
                    'payment_status' => $order->payment_status
                ]);
                return;
            }

            $order->load('items');

            // Ingredient PK name (usually id)
            $ingredientPk = (new Ingredient())->getKeyName();

            $deductMap = []; // [ingredient_id => qty_to_deduct]
            $menusMatched = [];

            foreach ($order->items as $item) {
                $menuId = (string) ($item->menu_id ?? '');
                $qtyOrdered = (int) ($item->qty ?? 1);

                if ($menuId === '' || $qtyOrdered < 1) continue;

                // ✅ recipes: menu_id + ingredient_id + qty_needed
                $recipes = Recipe::where('menu_id', $menuId)->get();

                if ($recipes->isEmpty()) {
                    Log::warning('[INV] NO RECIPE ROWS FOUND', [
                        'order_code' => $order->order_code,
                        'menu_id' => $menuId
                    ]);
                    continue;
                }

                $menusMatched[] = $menuId;

                foreach ($recipes as $r) {
                    $ingredientId = $r->ingredient_id ?? null;
                    $perItemQty   = (float) ($r->qty_needed ?? 0);

                    if (!$ingredientId || $perItemQty <= 0) continue;

                    $need = $perItemQty * $qtyOrdered;
                    $deductMap[$ingredientId] = ($deductMap[$ingredientId] ?? 0) + $need;
                }
            }

            if (empty($deductMap)) {
                Log::error('[INV] deductMap EMPTY (no matched recipes or qty_needed=0)', [
                    'order_code' => $order->order_code,
                    'items_count' => $order->items->count(),
                    'menus_matched' => $menusMatched,
                ]);
                return;
            }

            $ingredientIds = array_keys($deductMap);

            $ingredients = Ingredient::whereIn($ingredientPk, $ingredientIds)
                ->lockForUpdate()
                ->get()
                ->keyBy($ingredientPk);

            if ($ingredients->isEmpty()) {
                Log::error('[INV] NO INGREDIENTS MATCHED', [
                    'order_code' => $order->order_code,
                    'ingredient_pk' => $ingredientPk,
                    'ingredient_ids_from_recipes' => $ingredientIds,
                ]);
                return;
            }

            // ✅ validate stock using stock_qty
            foreach ($deductMap as $ingredientId => $need) {
                $ing = $ingredients->get($ingredientId);
                if (!$ing) {
                    throw new \Exception("Ingredient not found: {$ingredientId}");
                }

                $current = (float) ($ing->stock_qty ?? 0);

                if ($current < $need) {
                    throw new \Exception("Not enough stock for {$ing->name}. Need {$need}, have {$current}");
                }
            }

            // ✅ apply deduction using stock_qty + log stock_movements.qty
            foreach ($deductMap as $ingredientId => $need) {
                $ing = $ingredients->get($ingredientId);

                $ing->stock_qty = (float) $ing->stock_qty - (float) $need;
                $ing->save();

                StockMovement::create([
                    'ingredient_id' => $ingredientId,
                    'type'          => 'out',
                    'qty'           => $need,
                    'reason'        => 'Order consumption',
                ]);
            }

            $order->update(['inventory_deducted_at' => now()]);

            Log::info('[INV] deduction SUCCESS', [
                'order_code' => $order->order_code,
                'deduct_count' => count($deductMap),
                'menus_matched' => array_values(array_unique($menusMatched)),
            ]);
        });
    }
}