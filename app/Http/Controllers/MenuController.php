<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // ===== Customer menu pages (DB → Blade) =====
// ===== Customer menu pages =====

public function breakfast()
{
    $items = MenuItem::where('category', 'All Day Breakfast')->get();
    return view('all-day-breakfast-menu', compact('items'));
}

public function mainCourses()
{
    $items = MenuItem::where('category', 'main-courses')->get();
    return view('main-courses-menu', compact('items'));
}

public function pasta()
{
    $items = MenuItem::where('category', 'pasta')->get();
    return view('pasta-menu', compact('items'));
}

public function chicken()
{
    $items = MenuItem::where('category', 'chicken-wings')->get();
    return view('chicken-menu', compact('items'));
}

public function drinks()
{
    $items = MenuItem::whereIn('category', [
        'frappuccino',
        'coffee-based',
        'milk-based'
    ])->get();

    return view('drinks-menu', compact('items'));
}

public function pizza()
{
    $items = MenuItem::where('category', 'overload-premium')->get();
    return view('pizza-menu', compact('items'));
}

public function snacks()
{
    $items = MenuItem::where('category', 'snacks')->get();
    return view('snacks-menu', compact('items'));
}


    // ===== API: GET /api/menu =====
    public function index()
    {
        return response()->json(MenuItem::orderBy('created_at', 'desc')->get());
    }

    // ===== API: POST /api/menu =====
    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_id' => 'required|string|unique:menu_items,menu_id',
            'name' => 'required|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $item = MenuItem::create($data);

        return response()->json($item, 201);
    }

    // ===== API: PUT /api/menu/{menu_id} =====
    public function update(Request $request, $menu_id)
    {
        $item = MenuItem::where('menu_id', $menu_id)->firstOrFail();

        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'image' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric',
            'category' => 'sometimes|required|string',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    // ===== API: DELETE /api/menu/{menu_id} =====
    public function destroy($menu_id)
    {
        $item = MenuItem::where('menu_id', $menu_id)->firstOrFail();
        $item->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
