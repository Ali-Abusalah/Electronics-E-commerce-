<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Builds everything a printed tax invoice (PDF) needs.
 *
 * VAT is charged on every item at the configured rate (default 16%):
 *   line total (excl. VAT) = unit price × quantity
 *   VAT per line           = round(line total × rate / 100, 2)
 *   grand total            = Σ line totals + Σ VAT + delivery fee
 *
 * The same rounding is applied per line so the invoice always reconciles
 * with the totals the customer was charged at checkout.
 */
class InvoiceService
{
    public function taxRate(): float
    {
        return (float) config('invoice.tax_rate', 16);
    }

    /**
     * Compute the full invoice payload for an order.
     *
     * @throws RuntimeException when the order has no items (an invoice must
     *                          never be issued without purchases).
     */
    public function invoiceData(Order $order): array
    {
        if ($order->relationLoaded('items') ? $order->items->isEmpty() : ! $order->items()->exists()) {
            throw new RuntimeException('Invoice cannot be issued for an order without any items.');
        }

        $rate = $this->taxRate();

        $lines = [];
        $subtotal = 0.0;
        $vatTotal = 0.0;

        foreach ($order->items as $item) {
            $lineTotal = round((float) $item->price * (int) $item->quantity, 2);
            $lineVat = round($lineTotal * $rate / 100, 2);
            $subtotal += $lineTotal;
            $vatTotal += $lineVat;

            $lines[] = [
                'name' => $item->product_name ?: ('Product #'.$item->product_id),
                'code' => $item->product_code ?: null,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price,
                'line_total' => $lineTotal,
                'vat' => $lineVat,
                'line_grand' => round($lineTotal + $lineVat, 2),
            ];
        }

        $deliveryFee = round((float) $order->delivery_fee, 2);
        $grandTotal = round($subtotal + $vatTotal + $deliveryFee, 2);

        return [
            'rate' => $rate,
            'lines' => $lines,
            'subtotal' => $subtotal,
            'vat_total' => $vatTotal,
            'delivery_fee' => $deliveryFee,
            'grand_total' => $grandTotal,
            'seller' => [
                'name' => config('invoice.company_name'),
                'tagline' => config('invoice.company_tagline'),
                'address' => config('invoice.company_address'),
                'phone' => config('invoice.company_phone'),
                'email' => config('invoice.company_email'),
                'vat_number' => config('invoice.vat_number'),
            ],
            'buyer' => [
                'name' => $order->customer_name ?: 'Guest',
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address' => trim(($order->shipping_address ?: '').', '.($order->city ?: '')),
            ],
            'order_number' => $order->order_number,
            'invoice_label' => $order->invoice_number ?: $order->order_number,
            'order_id' => $order->id,
            'date' => $order->created_at ? Carbon::parse($order->created_at) : Carbon::now(),
            'payment_method' => $order->payment_method,
            'delivery_method' => $order->delivery_method,
            'currency_code' => config('invoice.currency_code'),
            'currency_symbol' => config('invoice.currency_symbol'),
            'qr_data_uri' => $this->qrDataUri($order, $vatTotal, $grandTotal),
        ];
    }

    /**
     * Build the QR payload and render it as a PNG data URI.
     *
     * The QR code embeds the invoice identity (seller, tax number, invoice
     * number, date and totals) as readable JSON, so scanning it with any phone
     * shows the invoice details for verification.
     */
    private function qrDataUri(Order $order, float $vatTotal, float $grandTotal): string
    {
        $payload = json_encode([
            'seller' => config('invoice.company_name'),
            'vat_number' => config('invoice.vat_number'),
            'invoice' => $order->invoice_number ?: $order->order_number,
            'order' => $order->order_number,
            'date' => $order->created_at ? Carbon::parse($order->created_at)->toIso8601String() : Carbon::now()->toIso8601String(),            'currency' => config('invoice.currency_code'),
            'total_incl_vat' => $grandTotal,
            'vat_amount' => round($vatTotal, 2),
        ], JSON_UNESCAPED_SLASHES);

        $qrCode = new QrCode(
            data: $payload ?: (string) $order->order_number,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        $writer = new PngWriter;
        $result = $writer->write($qrCode);

        // When the optional decoder package is installed, self-validate by
        // decoding the generated image back and comparing it to the payload,
        // so a broken QR can never be shipped on an invoice.
        if (class_exists('\\Zxing\\QrReader')) {
            $writer->validateResult($result, $payload ?: (string) $order->order_number);
        }

        return $result->getDataUri();
    }

    /**
     * Generate (once) and return the invoice PDF of an order.
     *
     * Every generated PDF is stored locally under storage/app/invoices so it
     * can be re-downloaded anytime without regenerating, and serves as a
     * permanent archive of the issued invoice.
     *
     * @throws RuntimeException when the order has no items
     */
    public function pdf(Order $order)
    {
        // Throws when the order has no items, before anything is written.
        $invoice = $this->invoiceData($order->loadMissing('items'));

        $label = $order->invoice_number ?: $order->order_number;
        $directory = storage_path('app/invoices');
        $path = $directory.DIRECTORY_SEPARATOR.'invoice-'.$label.'.pdf';

        if (! is_file($path)) {
            if (! is_dir($directory)) {
                mkdir($directory, 0775, true);
            }

            Pdf::loadView('invoice.tax', ['invoice' => $invoice])->save($path);
        }

        return response()->download($path, 'invoice-'.$label.'.pdf');
    }
}
