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
     * Initiate PayMongo payment (GCash / Maya)
     * Expects JSON: { cart: [...], method: "gcash"|"maya" }
     */
  public function initiate(Request $request)
    {
        $data = $request->validate([
            'cart' => 'required|array|min:1',
        ]);

        $paymongoKey = env('PAYMONGO_SECRET_KEY');
        if (!$paymongoKey) {
            return response()->json(['error' => 'PayMongo secret key not found'], 500);
        }

        // Compute total (PHP)
        $total = 0;
        foreach ($data['cart'] as $item) {
            $price = (float) ($item['price'] ?? 0);
            $qty   = (int) ($item['qty'] ?? 1);
            $total += $price * $qty;
        }

        $amount = max(2000, (int) round($total * 100)); // centavos

        // Build line items (nice for PayMongo UI)
        $lineItems = [];
        foreach ($data['cart'] as $item) {
            $name = (string) ($item['name'] ?? 'Item');
            $price = (int) round(((float)($item['price'] ?? 0)) * 100);
            $qty = (int) ($item['qty'] ?? 1);

            if ($price <= 0 || $qty <= 0) continue;

            $lineItems[] = [
                'name' => $name,
                'amount' => $price,
                'currency' => 'PHP',
                'quantity' => $qty,
            ];
        }

        try {
            $res = Http::withBasicAuth($paymongoKey, '')
                ->acceptJson()
                ->withOptions(['verify' => app()->environment('local') ? false : true])
                ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'line_items' => $lineItems,
                            'payment_method_types' => ['gcash', 'paymaya',], // h
                            'success_url' => url('/payment-success'),
                            'cancel_url'  => url('/payment-cancelled'),
                            'description' => 'Order Payment',
                        ],
                    ],
                ]);

            Log::debug('PayMongo CheckoutSession', [
                'status' => $res->status(),
                'body' => $res->json(),
            ]);

            if (!$res->ok()) {
                return response()->json([
                    'error' => 'PayMongo API error (checkout_sessions)',
                    'details' => $res->json(),
                ], 400);
            }

            $checkoutUrl = data_get($res->json(), 'data.attributes.checkout_url');
            if (!$checkoutUrl) {
                return response()->json(['error' => 'Checkout URL not generated'], 500);
            }

            return response()->json([
                'redirect' => $checkoutUrl,
            ]);

        } catch (\Throwable $e) {
            Log::error('PayMongo Exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
