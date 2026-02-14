<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderApiController extends Controller
{
    // GET /admin/api/orders?table=1
    public function index(Request $request)
    {
        $table = $request->query('table');

        $q = Order::with('items')->orderBy('created_at', 'desc');
        if ($table) $q->where('table_number', (int)$table);

        return response()->json($q->get());
    }

    // PUT /admin/api/orders/{order_code}
    public function update(Request $request, $order_code)
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        $data = $request->validate([
            'status' => 'required|in:preparing,serving,served,cancelled',
            'eta_minutes' => 'nullable|integer|min:0|max:999',
        ]);

        $order->update($data);

        return response()->json([
            'message' => 'Updated',
            'order' => $order->load('items')
        ]);
    }
}
