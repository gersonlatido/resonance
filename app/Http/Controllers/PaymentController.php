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
            // We will get table from session, not from user input
'table_number' => 'nullable',
        ]);

        $xenditKey = env('XENDIT_SECRET_KEY');
        if (!$xenditKey) {
            return response()->json(['error' => 'Xendit secret key not found. Put XENDIT_SECRET_KEY in .env'], 500);
        }

        $total   = 0;
        $items   = [];   // Xendit items
        $dbItems = [];   // DB items

        foreach ($data['cart'] as $item) {

            $name  = (string) ($item['name'] ?? 'Item');
            $price = (float) ($item['price'] ?? 0);
            $qty   = (int) ($item['qty'] ?? 1);

            if ($price <= 0 || $qty <= 0) continue;

            /**
             * ✅ IMPORTANT:
             * Your menu_items table uses menu_id like MENU001 as the key (NO numeric id column).
             * So order_items.menu_id MUST be MENU001 etc.
             */
            $menuId = (string) ($item['menu_id'] ?? '');

            // If frontend mistakenly sends "id" but it's actually "MENU001", accept it
            if (!$menuId && isset($item['id']) && is_string($item['id']) && str_starts_with($item['id'], 'MENU')) {
                $menuId = $item['id'];
            }

            // If still empty -> we cannot map numeric ids because menu_items has no id
            if (!$menuId) {
                return response()->json([
                    'error' => 'Cart item missing menu_id (expected MENU001 etc). Please send menu_id from frontend cart.',
                    'bad_item' => $item,
                ], 422);
            }

            $lineTotal = $price * $qty;
            $total += $lineTotal;

            $items[] = [
                'name'     => $name,
                'quantity' => $qty,
                'price'    => (int) round($price),
            ];

            $dbItems[] = [
                'menu_id'  => $menuId,  // ✅ ALWAYS MENU001 format
                'name'     => $name,
                'price'    => $price,
                'qty'      => $qty,
                'subtotal' => $lineTotal,
            ];
        }

        if ($total <= 0 || count($dbItems) === 0) {
            return response()->json(['error' => 'No valid items to pay for. Check cart price/qty.'], 422);
        }

        $amount = (int) round($total);
        if ($amount < 1) {
            return response()->json(['error' => 'Invalid amount computed.'], 422);
        }

        $orderCode  = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $externalId = 'order_' . now()->timestamp . '_' . uniqid();
   // ✅ Get shared/solo session data
$tableNumbers = session('table_numbers');
$tableLabel   = session('table_label');

// fallback if no shared session
if (!is_array($tableNumbers) || count($tableNumbers) < 1) {
    $tableNumbers = [(int) $data['table_number']];
    $tableLabel   = 'Table ' . (int) $data['table_number'];
}

sort($tableNumbers);
$primaryTable = (int) $tableNumbers[0];

// ✅ Create order with shared support
$order = Order::create([
    'order_code'     => $orderCode,
    'external_id'    => $externalId,

    'table_number'   => $primaryTable,
    'table_label'    => $tableLabel,
    'table_numbers'  => $tableNumbers,

    'status'         => 'preparing',
    'eta_minutes'    => null,
    'payment_status' => 'unpaid',
    'total'          => $total,
]);

        // ✅ Save order items (menu_id is MENU001 etc)
        foreach ($dbItems as $it) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id'  => $it['menu_id'],
                'name'     => $it['name'],
                'price'    => $it['price'],
                'qty'      => $it['qty'],
                'subtotal' => $it['subtotal'],
            ]);
        }

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
                'redirect'    => $invoiceUrl,
                'external_id' => $externalId,
                'order_code'  => $orderCode,
            ]);
        } catch (\Throwable $e) {
            $order->update(['payment_status' => 'failed']);
            Log::error('Xendit Exception', ['message' => $e->getMessage()]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}