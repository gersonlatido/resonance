<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentSuccessController extends Controller
{
    public function show(Request $request)
    {
        return view('payment-success');
    }
}
