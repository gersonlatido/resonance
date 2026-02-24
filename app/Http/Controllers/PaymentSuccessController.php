<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;

class PaymentSuccessController extends Controller
{
    public function show(Request $request)
    {
        $orderCode = $request->query('order_code');

        if (!$orderCode) {
            abort(400, 'Missing order_code');
        }

        $order = Order::where('order_code', $orderCode)->firstOrFail();

        // ✅ store table in session so /feedback is clean
        session([
            'table_number' => (int) $order->table_number,
            'order_code'   => (string) $order->order_code,
        ]);

        // ✅ mark paid
        $order->update(['payment_status' => 'paid']);

        // ✅ IMPORTANT: auto mark table unavailable AFTER payment
        Table::where('number', (int) $order->table_number)
            ->update(['is_available' => false]);

        // ✅ go to receipt
        return redirect()->route('payment.receit');
    }
}
