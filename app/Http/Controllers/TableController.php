<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TableController extends Controller
{
    public function enter(Request $request, $table)
    {
        // allow 1–10 only (you said 10 tables)
        if (!is_numeric($table) || (int)$table < 1 || (int)$table > 10) {
            abort(404);
        }

        // store table in session
        session(['table_number' => (int) $table]);

        // redirect customer to your customer home page
        return redirect('/');
    }
}
