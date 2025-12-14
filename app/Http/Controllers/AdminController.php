<?php
namespace App\Http\Controllers;

use App\Models\Order;  // Assuming you have an Order model to fetch orders
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin dashboard page
    public function index()
    {
        // Fetch active orders, pending orders, and served orders from the database
        $activeOrders = Order::where('status', 'active')->get();
        $pendingOrders = Order::where('status', 'pending')->get();
        $servedOrders = Order::where('status', 'served')->get();

        // Return the view with the order data
        return view('admin.dashboard', compact('activeOrders', 'pendingOrders', 'servedOrders'));
    }
}
