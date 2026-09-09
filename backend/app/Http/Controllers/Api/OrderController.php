<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class OrderController extends Controller
{
    /**
     * List the orders (and their invoices) belonging to the current customer.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::withCount('items')
            ->where('customer_email', $user->email)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'orders' => $orders->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'invoice_number' => $order->invoice_number,
                    'status' => $order->status,
                    'total' => (float) $order->total,
                    'created_at' => $order->created_at?->toIso8601String(),
                    'items_count' => (int) $order->items_count,
                ];
            }),
        ]);
    }

    /**
     * Place an order. A sales tax (VAT, default 16%) is charged on the value
     * of every item and included in the order total.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'delivery_method' => 'required|in:standard,express',
            'payment_method' => 'required|in:cod,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $taxRate = (float) config('invoice.tax_rate', 16);
            $subtotal = 0;
            $vatTotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}. Available: {$product->stock}"],
                    ]);
                }

                $itemSubtotal = round((float) $product->price * (int) $item['quantity'], 2);
                $itemVat = round($itemSubtotal * $taxRate / 100, 2);
                $subtotal += $itemSubtotal;
                $vatTotal += $itemVat;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'quantity' => $item['quantity'],
                    'price' => (float) $product->price,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $deliveryFee = $validated['delivery_method'] === 'express'
                ? 12.99
                : ($subtotal >= 50 ? 0 : 4.99);

            $total = round($subtotal + $vatTotal + $deliveryFee, 2);

            do {
                $orderNumber = 'VM-'.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (Order::where('order_number', $orderNumber)->exists());

            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_number' => $this->nextInvoiceNumber(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'city' => $validated['city'],
                'delivery_method' => $validated['delivery_method'],
                'payment_method' => $validated['payment_method'],
                'subtotal' => round($subtotal, 2),
                'tax_rate' => $taxRate,
                'tax_amount' => round($vatTotal, 2),
                'delivery_fee' => round($deliveryFee, 2),
                'total' => $total,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            $order->load('items.product');

            $response = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'invoice_number' => $order->invoice_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'city' => $order->city,
                'delivery_method' => $order->delivery_method,
                'payment_method' => $order->payment_method,
                'subtotal' => (float) $order->subtotal,
                'tax_rate' => (float) $order->tax_rate,
                'tax_amount' => (float) $order->tax_amount,
                'delivery_fee' => (float) $order->delivery_fee,
                'total' => (float) $order->total,
                'items' => $order->items->map(function ($oi) {
                    return [
                        'product_id' => $oi->product_id,
                        'product_name' => $oi->product?->name,
                        'quantity' => (int) $oi->quantity,
                        'price' => (float) $oi->price,
                    ];
                }),
            ];

            return response()->json($response, 201);
        });
    }

    /**
     * Download the tax invoice of an order as a PDF.
     *
     * Security rules:
     *  - a customer can only download invoices of orders placed under their
     *    own e-mail address;
     *  - the invoice is only issued when the order actually contains items
     *    (no purchases → no invoice).
     */
    public function invoice(Request $request, Order $order)
    {
        $user = $request->user();
        if (
            $order->customer_email
            && strcasecmp($order->customer_email, $user->email) !== 0
        ) {
            return response()->json([
                'message' => 'You are not allowed to download this invoice.',
            ], 403);
        }

        try {
            return app(InvoiceService::class)->pdf($order);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Local sequential invoice number (INV-000001, INV-000002, …).
     *
     * The counter lives in the dedicated order_sequences table so numbers are
     * never reused, even when orders are deleted. Runs inside the order
     * transaction.
     */
    private function nextInvoiceNumber(): string
    {
        $sequence = DB::table('order_sequences')
            ->where('id', 1)
            ->lockForUpdate()
            ->first();

        $next = ((int) ($sequence->last_invoice_number ?? 0)) + 1;

        DB::table('order_sequences')
            ->where('id', 1)
            ->update(['last_invoice_number' => $next]);

        return 'INV-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
