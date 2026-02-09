<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // CUSTOMER: Save cart to DB (pending order)
public function storeFromCart(Request $request)
{
    $data = $request->validate([
        'cart' => ['required', 'array', 'min:1'],
        'cart.*.id' => ['required'],
        'cart.*.name' => ['required', 'string'],
        'cart.*.price' => ['required', 'numeric'],
        'cart.*.qty' => ['required', 'integer', 'min:1'],
        'cart.*.image' => ['nullable', 'string'],
    ]);

    $cart = $data['cart'];

    $orderCode = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

    $order = \App\Models\Order::create([
        'order_code' => $orderCode,
        'status' => 'pending',
        'total' => 0,
    ]);

    $total = 0;

    foreach ($cart as $item) {
        $unitPrice = (float) $item['price'];
        $quantity  = (int) $item['qty'];
        $subtotal  = $unitPrice * $quantity;

        $total += $subtotal;

        \App\Models\OrderItem::create([
            'order_id'   => $order->id,
            'menu_id'    => (string) $item['id'],
            'name'       => $item['name'],
            'unit_price' => $unitPrice,
            'quantity'   => $quantity,
            'image'      => $item['image'] ?? null,

            // ✅ DB expects subtotal
            'subtotal'   => $subtotal,
        ]);
    }

    $order->update(['total' => $total]);

    return response()->json([
        'message' => 'Order saved as pending',
        'order_id' => $order->id,
        'order_code' => $order->order_code,
        'total' => $order->total,
    ]);
}





    // ADMIN: Get orders JSON for dashboard
   public function adminOrdersJson()
{
    $orders = \App\Models\Order::with('items')
        ->orderByDesc('id')
        ->get(); // ✅ no limit

    return response()->json($orders);
}

    public function updateStatus(Request $request)
{
    $data = $request->validate([
        'order_id' => ['required', 'integer', 'exists:orders,id'],
        'status' => ['required', 'in:pending,paid,cancelled,served'],
    ]);

    $order = \App\Models\Order::findOrFail($data['order_id']);
    $order->status = $data['status'];
    $order->save();

    return response()->json(['message' => 'Status updated']);
}
public function markPaid(Request $request)
{
    $data = $request->validate([
        'order_id' => ['required', 'integer', 'exists:orders,id'],
    ]);

    $order = \App\Models\Order::findOrFail($data['order_id']);
    $order->status = 'paid';
    $order->save();

    return response()->json(['message' => 'Order marked as paid']);
}

}

