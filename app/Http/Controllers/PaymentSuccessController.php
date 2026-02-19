<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

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


        // ✅ optional: mark paid (if you want)
        $order->update(['payment_status' => 'paid']);

        // ✅ go to receipt (with order_code)
        // return redirect()->route('payment.receit', ['order_code' => $order->order_code]);
        return redirect()->route('payment.receit');

    }
}
