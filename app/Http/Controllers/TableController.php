<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    public function enter(Request $request, $table)
    {
        if (!is_numeric($table) || (int)$table < 1 || (int)$table > 10) {
            abort(404);
        }

        $tableNo = (int) $table;

        // ✅ block if table is unavailable
        $t = Table::where('number', $tableNo)->first();
        if (!$t) {
            abort(404, 'Table not found in DB.');
        }
        if (!$t->is_available) {
            abort(403, 'This table is currently unavailable.');
        }

        session(['table_number' => $tableNo]);

        return redirect('/');
    }
}
