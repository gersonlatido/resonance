<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentReceiptController extends Controller
{
   public function show(Request $request)
{
    // ✅ get order_code from URL OR session
    $orderCode = $request->query('order_code') ?? session('order_code');

    if (!$orderCode) {
        abort(400, 'Missing order_code');
    }

    $order = \App\Models\Order::with('items')
        ->where('order_code', $orderCode)
        ->firstOrFail();

    // ✅ store for feedback + tracking (clean URLs)
session([
    'order_code'   => (string) $order->order_code,
    'table_number' => (int) $order->table_number,
]);



    return view('payment-receit', compact('order'));
}

}
