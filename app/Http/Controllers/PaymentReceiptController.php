<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentReceiptController extends Controller
{
    public function show(Request $request)
    {
        // ✅ get order_code from query OR route OR session
        $orderCode =
            $request->query('order_code')
            ?? $request->route('order_code')
            ?? session('order_code');

        if (!$orderCode) {
            abort(400, 'Missing order_code');
        }

        $order = Order::with('items')
            ->where('order_code', $orderCode)
            ->firstOrFail();

        // ✅ store for feedback + tracking (session-based track-order)
        session([
            'order_code'   => (string) $order->order_code,
            'table_number' => (int) $order->table_number,
        ]);

        return view('payment-receipt', compact('order'));
    }
}