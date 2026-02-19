<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'table_number'  => ['required', 'integer', 'min:1', 'max:50'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:2000'],
        ]);

        Feedback::create([
            'customer_name' => $data['customer_name'],
            'table_number'  => $data['table_number'],
            'rating'        => $data['rating'],
            'comment'       => $data['comment'] ?? null,
            'is_reviewed'   => 0,
        ]);

        return redirect()->back()->with('success', 'Feedback submitted!');
    }
}
