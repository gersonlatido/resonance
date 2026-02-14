<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{


    // ✅ Admin: update status + eta
   public function updateStatus(Request $request, $order_code)
{
    $order = Order::where('order_code', $order_code)->firstOrFail();

    $data = $request->validate([
        'status' => 'sometimes|required|string|in:preparing,serving,served,cancelled',
        'eta_minutes' => 'sometimes|nullable|integer|min:0|max:999',
    ]);

    $order->update($data);

    return response()->json([
        'message' => 'Updated',
        'order' => $order
    ]);
}



 public function track($order_code)
{
    $order = Order::where('order_code', $order_code)
        ->with('items')
        ->firstOrFail();

    return response()->json($order);
}



    // ✅ Mark order as paid (called by payment-success page)
public function markPaid(Request $request)
{
    $data = $request->validate([
        'order_code' => 'required|string',
        'external_id' => 'required|string',
    ]);

    $order = \App\Models\Order::where('order_code', $data['order_code'])
        ->where('external_id', $data['external_id'])
        ->firstOrFail();

    $order->update(['payment_status' => 'paid']);

    return response()->json(['message' => 'Order marked as paid']);
}


    
}
