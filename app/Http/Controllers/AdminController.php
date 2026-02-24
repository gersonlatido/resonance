<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;

class AdminController extends Controller
{
    // Admin dashboard page
    public function index()
    {
        $orders = Order::with('items')
            ->where('payment_status', 'paid')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.dashboard', compact('orders'));
    }

    public function tableManagement()
    {
        // ✅ IMPORTANT: pass $tables to the blade
        $tables = Table::orderBy('number')->get()->keyBy('number');

        return view('admin.table-management', compact('tables'));
    }

    // ✅ Toggle table availability (AJAX)
    public function toggleTable(Request $request, $number)
    {
        $n = (int) $number;

        if ($n < 1 || $n > 10) {
            return response()->json(['error' => 'Invalid table number'], 422);
        }

        $table = Table::where('number', $n)->firstOrFail();
        $table->is_available = !$table->is_available;
        $table->save();

        return response()->json([
            'number'       => $table->number,
            'is_available' => $table->is_available,
            'status_text'  => $table->is_available ? 'Available' : 'Unavailable',
        ]);
    }

    // Handle receiving orders data from localStorage (AJAX POST request)
    public function storeOrdersData(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.customer_name' => 'required|string',
            'orders.*.items' => 'required|array',
            'orders.*.total' => 'required|numeric',
        ]);

        \Log::info('Received orders data:', $validated['orders']);

        return response()->json(['message' => 'Orders data received successfully', 'status' => true]);
    }
}
