<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $items = [];   // Xendit items
        $dbItems = []; // DB items

        foreach ($data['cart'] as $item) {
            $name   = (string) ($item['name'] ?? 'Item');
            $price  = (float) ($item['price'] ?? 0);
            $qty    = (int) ($item['qty'] ?? 1);
            $menuId = (string) ($item['id'] ?? $item['menu_id'] ?? '');

            if ($price <= 0 || $qty <= 0) continue;

            $lineTotal = $price * $qty;
            $total += $lineTotal;

            $items[] = [
                'name'     => $name,
                'quantity' => $qty,
                'price'    => (int) round($price), // Xendit expects integer PHP
            ];

            $dbItems[] = [
                'menu_id'  => $menuId ?: null,
                'name'     => $name,
                'price'    => $price,
                'qty'      => $qty,
                'subtotal' => $lineTotal, // ✅ ADD THIS
            ];
        }

        if ($total <= 0 || count($dbItems) === 0) {
            return response()->json(['error' => 'No valid items to pay for. Check cart price/qty.'], 422);
        }

        $amount = (int) round($total);
        if ($amount < 1) {
            return response()->json(['error' => 'Invalid amount computed.'], 422);
        }

        // ✅ Create identifiers
        $orderCode  = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $externalId = 'order_' . now()->timestamp . '_' . uniqid();

        // ✅ Save order to DB BEFORE redirect (unpaid first)
        $order = Order::create([
            'order_code'      => $orderCode,
            'external_id'     => $externalId,
            'table_number'    => (int) $data['table_number'],
            'status'          => 'preparing',
            'eta_minutes'     => null,
            'payment_status'  => 'unpaid',
            'total'           => $total,
        ]);

        // ✅ Save items WITH subtotal
        foreach ($dbItems as $it) {
            OrderItem::create([
                'order_id'  => $order->id,
                'menu_id'   => $it['menu_id'],
                'name'      => $it['name'],
                'price'     => $it['price'],
                'qty'       => $it['qty'],
                'subtotal'  => $it['subtotal'], // ✅ FIX
            ]);
        }

        // ✅ Put order_code + external_id in success URL so we can mark paid + show tracking
        $successUrl = url('/payment-success') . '?order_code=' . urlencode($orderCode) . '&external_id=' . urlencode($externalId);
        $failedUrl  = url('/payment-cancelled') . '?order_code=' . urlencode($orderCode);

        try {
            $res = Http::withBasicAuth($xenditKey, '')
                ->acceptJson()
                ->withOptions(['verify' => false]) // DEV ONLY
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id'           => $externalId,
                    'amount'                => $amount,
                    'currency'              => 'PHP',
                    'description'           => 'Order Payment',
                    'success_redirect_url'  => $successUrl,
                    'failure_redirect_url'  => $failedUrl,
                    'items'                 => $items,
                ]);

            Log::debug('Xendit Invoice', [
                'status' => $res->status(),
                'body'   => $res->json(),
                'raw'    => $res->body(),
            ]);

            if (!$res->ok()) {
                $order->update(['payment_status' => 'failed']);

                return response()->json([
                    'error'   => 'Xendit API error (invoices)',
                    'status'  => $res->status(),
                    'details' => $res->json(),
                    'raw'     => $res->body(),
                ], 400);
            }

            $invoiceUrl = $res->json('invoice_url');
            if (!$invoiceUrl) {
                $order->update(['payment_status' => 'failed']);

                return response()->json([
                    'error'   => 'Invoice URL not generated',
                    'details' => $res->json(),
                ], 500);
            }

            return response()->json([
                'redirect'     => $invoiceUrl,
                'external_id'  => $externalId,
                'order_code'   => $orderCode,
            ]);
        } catch (\Throwable $e) {
            $order->update(['payment_status' => 'failed']);
            Log::error('Xendit Exception', ['message' => $e->getMessage()]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
