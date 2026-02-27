<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Log;

class PaymentSuccessController extends Controller
{
    public function show(Request $request, InventoryService $inventory)
    {
        $orderCode = $request->query('order_code');

        if (!$orderCode) {
            abort(400, 'Missing order_code');
        }

        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // store session for track/feedback
        session([
            'order_code'   => (string) $order->order_code,
            'table_number' => (int) $order->table_number,
        ]);

        // mark paid
        if (($order->payment_status ?? null) !== 'paid') {
            $order->update(['payment_status' => 'paid']);
        }

        // ✅ DEDUCT INVENTORY (LOGS)
        try {
            $inventory->deductIngredientsForOrder($order);
        } catch (\Throwable $e) {
            Log::error('Inventory deduction failed', [
                'order_code' => $order->order_code,
                'error' => $e->getMessage(),
            ]);
        }

        // mark table unavailable
// ✅ mark table(s) unavailable AFTER payment

$tablesToBlock = $order->table_numbers;

if (!is_array($tablesToBlock) || count($tablesToBlock) === 0) {
    $tablesToBlock = [$order->table_number];
}

Table::whereIn('number', $tablesToBlock)
    ->update(['is_available' => false]);

        return redirect()->route('payment.receipt', ['order_code' => $order->order_code]);
    }
}