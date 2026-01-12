<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Show payment page
     */
    public function show()
    {
        return view('payment');
    }
       public function showFeedback()
    {
        return view('feedback');
    }

    /**
     * Initiate PayMongo payment (GCash)
     */
  public function initiate(Request $request)
{
    $data = $request->validate([
        'cart' => 'required|array',
    ]);

    $paymongoKey = env('PAYMONGO_SECRET_KEY');

    if (!$paymongoKey) {
        return response()->json([
            'error' => 'PayMongo secret key not found'
        ], 500);
    }

    // Calculate total
    $total = 0;
    foreach ($data['cart'] as $item) {
        $price = (float) ($item['price'] ?? 0);
        $qty   = (int) ($item['qty'] ?? 1);
        $total += $price * $qty;
    }

    $amount = max(100, (int) round($total * 100)); // centavos

    try {
        // Create Payment Intent
        $response = Http::withBasicAuth($paymongoKey, '')
            ->acceptJson()
            ->withOptions(['verify' => false]) // local only
            ->post('https://api.paymongo.com/v1/payment_intents', [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'currency' => 'PHP',
                        'payment_method_allowed' => ['gcash'],
                        'description' => 'Order Payment',
                    ],
                ],
            ]);

        Log::debug('PayMongo PaymentIntent', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (!$response->ok()) {
            return response()->json([
                'error' => 'PayMongo API error',
                'details' => $response->json()
            ], 400);
        }

        $intentId = $response['data']['id'];

        // Create Payment Method (GCash)
        $method = Http::withBasicAuth($paymongoKey, '')
            ->acceptJson()
            ->withOptions(['verify' => false])
            ->post('https://api.paymongo.com/v1/payment_methods', [
                'data' => [
                    'attributes' => [
                        'type' => 'gcash',
                    ],
                ],
            ]);

        $methodId = $method['data']['id'];

        // Attach Payment Method
        $attach = Http::withBasicAuth($paymongoKey, '')
            ->acceptJson()
            ->withOptions(['verify' => false])
            ->post("https://api.paymongo.com/v1/payment_intents/{$intentId}/attach", [
                'data' => [
                    'attributes' => [
                        'payment_method' => $methodId,
                        'return_url' => url('/payment-success'),
                    ],
                ],
            ]);

        $checkoutUrl =
            $attach['data']['attributes']['next_action']['redirect']['url'] ?? null;

        if (!$checkoutUrl) {
            return response()->json([
                'error' => 'Checkout URL not generated'
            ], 500);
        }

        return response()->json([
            'redirect' => $checkoutUrl
        ]);

    } catch (\Throwable $e) {
        Log::error('PayMongo Exception', [
            'message' => $e->getMessage()
        ]);

        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

}
