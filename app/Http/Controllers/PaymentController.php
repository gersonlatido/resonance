<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function show()
    {
        return view('payment');
    }

    public function showFeedback()
    {
        return view('feedback');
    }

    /**
     * Initiate Xendit payment (Invoice / Payment Link)
     * Expects JSON: { cart: [...] }
     * Returns JSON: { redirect: invoice_url, external_id: ... }
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'cart' => 'required|array|min:1',
             'table_number' => 'required|integer|min:1|max:10',
        ]);

        $xenditKey = env('XENDIT_SECRET_KEY');
        if (!$xenditKey) {
            return response()->json(['error' => 'Xendit secret key not found. Put XENDIT_SECRET_KEY in .env'], 500);
        }

        // Compute total + items
        $total = 0;
        $items = [];

        foreach ($data['cart'] as $item) {
            $name  = (string) ($item['name'] ?? 'Item');
            $price = (float) ($item['price'] ?? 0);
            $qty   = (int) ($item['qty'] ?? 1);

            if ($price <= 0 || $qty <= 0) continue;

            $total += $price * $qty;

            // Optional item breakdown in Xendit invoice
            $items[] = [
                'name' => $name,
                'quantity' => $qty,
                'price' => (int) round($price), // Xendit expects integer PHP amount
            ];
        }

        if ($total <= 0) {
            return response()->json(['error' => 'No valid items to pay for (total is 0). Check cart price/qty.'], 422);
        }

        $amount = (int) round($total);
        if ($amount < 1) {
            return response()->json(['error' => 'Invalid amount computed.'], 422);
        }

        // Make external_id unique (avoid collisions)
        $externalId = 'order_' . now()->timestamp . '_' . uniqid();

        try {
            $res = Http::withBasicAuth($xenditKey, '')
                ->acceptJson()
                // DEV ONLY: set to true after you fix SSL (curl error 60)
                ->withOptions(['verify' => false])
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id' => $externalId,
                    'amount' => $amount,
                    'currency' => 'PHP',
                    'description' => 'Order Payment',
                    'success_redirect_url' => url('/payment-success'),
                    'failure_redirect_url' => url('/payment-cancelled'),
                    'items' => $items,
                ]);

            Log::debug('Xendit Invoice', [
                'status' => $res->status(),
                'body' => $res->json(),
                'raw' => $res->body(),
            ]);

            if (!$res->ok()) {
                return response()->json([
                    'error' => 'Xendit API error (invoices)',
                    'status' => $res->status(),
                    'details' => $res->json(),
                    'raw' => $res->body(),
                ], 400);
            }

            $invoiceUrl = $res->json('invoice_url');
            if (!$invoiceUrl) {
                return response()->json([
                    'error' => 'Invoice URL not generated',
                    'details' => $res->json(),
                ], 500);
            }

            return response()->json([
                'redirect' => $invoiceUrl,
                'external_id' => $externalId,
            ]);

        } catch (\Throwable $e) {
            Log::error('Xendit Exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
