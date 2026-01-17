<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin dashboard page
    public function index()
    {
        // Return the view with no orders data passed from the server
        return view('admin.dashboard');
    }
    public function tableManagement()
    {
        // Return the view for table management
        return view('admin.table-management');
    }

    // Handle receiving orders data from localStorage (AJAX POST request)
    public function storeOrdersData(Request $request)
    {
        // Validate incoming data (order data from the frontend)
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer',
            'orders.*.customer_name' => 'required|string',
            'orders.*.items' => 'required|array',
            'orders.*.total' => 'required|numeric',
        ]);

        // Process the data or store it in the database if needed
        // In this example, we'll just log it for demonstration purposes
        // You can modify this part to actually store the data in a database or perform other actions
        \Log::info('Received orders data:', $validated['orders']);

        // Respond with success message
        return response()->json(['message' => 'Orders data received successfully', 'status' => true]);
    }
}
