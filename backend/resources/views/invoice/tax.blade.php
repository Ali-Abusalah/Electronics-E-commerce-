@php
    $seller = $invoice['seller'];
    $buyer = $invoice['buyer'];
    $lines = $invoice['lines'];
    $sym = $invoice['currency_symbol'];
    $rateLabel = number_format($invoice['rate'], 0);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice {{ $invoice['invoice_label'] }}</title>
    <style>
        @page { margin: 12mm 14mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
            background: #fff;
        }
        table { border-collapse: collapse; border-spacing: 0; width: 100%; }
        td, th { vertical-align: top; }

        /* ── Outer container ─────────────────────────────── */
        .invoice-wrap {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        /* ── Blue top bar ─────────────────────────────────── */
        .top-bar {
            background: #2563eb;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .top-bar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-circle {
            width: 40px;
            height: 40px;
            background: #fff;
            color: #2563eb;
            font-size: 16px;
            font-weight: 800;
            text-align: center;
            line-height: 40px;
            border-radius: 8px;
        }
        .brand-name {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }
        .brand-tagline {
            font-size: 10px;
            color: rgba(255,255,255,0.75);
        }
        .invoice-title {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
        }
        .invoice-subtitle {
            font-size: 9.5px;
            color: rgba(255,255,255,0.8);
        }

        .body {
            padding: 18px 24px;
        }

        /* ── Header bottom divider ───────────────────────── */
        .header-bottom {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        /* ── Invoice details row ──────────────────────────── */
        .detail-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
        }
        .detail-value {
            font-size: 12px;
            font-weight: 700;
            color: #111827;
            margin-top: 2px;
        }
        .detail-block { display: inline-block; min-width: 90px; }

        /* ── Section headings ─────────────────────────────── */
        .section-heading {
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }

        /* ── Customer info ────────────────────────────────── */
        .customer-grid {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .customer-field {
            display: flex;
            flex-direction: column;
        }
        .field-label {
            font-weight: 600;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .field-value {
            font-size: 12px;
            color: #1f2937;
            font-weight: 500;
            margin-top: 2px;
        }

        /* ── Line items table ─────────────────────────────── */
        table.lines th {
            background: #f3f4f6;
            color: #374151;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            font-weight: 600;
        }
        table.lines td {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            font-size: 11px;
        }
        table.lines tr.alt td { background: #fafafa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-bold { font-weight: 700; }
        .text-muted { color: #6b7280; }
        .line-total { font-weight: 700; color: #111827; }

        /* ── Totals ───────────────────────────────────────── */
        .totals-block {
            margin-top: 14px;
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .totals-side {
            flex: 1;
        }
        .totals-right {
            width: 200px;
            border-left: 1px solid #e5e7eb;
            padding-left: 20px;
        }
        .t-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: 11.5px;
        }
        .t-label { color: #4b5563; font-weight: 400; }
        .t-amount { font-weight: 600; color: #111827; }
        .grand-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 0;
            margin-top: 8px;
            border-top: 2px solid #2563eb;
        }
        .grand-label {
            color: #2563eb;
            font-size: 14px;
            font-weight: 700;
        }
        .grand-amount {
            color: #2563eb;
            font-size: 17px;
            font-weight: 800;
        }

        /* ── Footer ───────────────────────────────────────── */
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }

        /* ── QR code ─────────────────────────────────────── */
        .qr-cell {
            text-align: center;
        }
        .qr-cell img {
            display: block;
            margin: 0 auto;
            max-width: 95px;
        }
        .qr-label {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="invoice-wrap">

    {{-- ── TOP BAR ───────────────────────────────────────────────── --}}
    <div class="top-bar">
        <div class="top-bar-brand">
            <div class="logo-circle">DC</div>
            <div>
                <div class="brand-name">{{ $seller['name'] }}</div>
                <div class="brand-tagline">{{ $seller['tagline'] }}</div>
            </div>
        </div>
        <div>
            <div class="invoice-title">Tax Invoice</div>
            <div class="invoice-subtitle">Electronic tax invoice | VAT {{ $rateLabel }}%</div>
        </div>
    </div>

    <div class="body">

        {{-- ── INVOICE DETAILS ROW ─────────────────────────────────── --}}
        <div class="header-bottom">
            <div class="customer-grid" style="margin-bottom:16px;">
                <div class="detail-block">
                    <div class="detail-label">Invoice No.</div>
                    <div class="detail-value">{{ $invoice['invoice_label'] }}</div>
                </div>
                <div class="detail-block">
                    <div class="detail-label">Date</div>
                    <div class="detail-value">{{ $invoice['date']->format('d M Y') }}</div>
                </div>
                <div class="detail-block" style="text-align:right;">
                    <div class="detail-label" style="text-align:right;">Order No.</div>
                    <div class="detail-value" style="text-align:right;">{{ $invoice['order_number'] }}</div>
                </div>
            </div>
        </div>

        {{-- ── CUSTOMER INFORMATION ────────────────────────────────── --}}
        <div class="section-heading">Customer Information</div>
        <div class="customer-grid" style="margin-bottom:18px;">
            <div class="customer-field">
                <span class="field-label">Name</span>
                <span class="field-value">{{ $buyer['name'] }}</span>
            </div>
            <div class="customer-field">
                <span class="field-label">Contact</span>
                <span class="field-value">{{ $buyer['phone'] ?: '-' }}</span>
            </div>
            <div class="customer-field">
                <span class="field-label">Payment</span>
                <span class="field-value">
                    @if ($invoice['payment_method'] === 'cod')
                        Cash on Delivery
                    @elseif ($invoice['payment_method'] === 'card')
                        Card Payment
                    @else
                        {{ ucfirst($invoice['payment_method']) }}
                    @endif
                </span>
            </div>
        </div>

        {{-- ── LINE ITEMS ──────────────────────────────────────────── --}}
        <div class="section-heading">Line Items</div>
        <table class="lines" style="margin-bottom:8px;">
            <thead>
                <tr>
                    <th class="text-center" style="width:4%;">#</th>
                    <th class="text-left" style="width:32%;">Item</th>
                    <th class="text-left" style="width:10%;">Code</th>
                    <th class="text-center" style="width:6%;">Qty</th>
                    <th class="text-right" style="width:14%;">Unit Price</th>
                    <th class="text-right" style="width:14%;">Tax {{ $rateLabel }}%</th>
                    <th class="text-right" style="width:20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines as $index => $line)
                <tr class="{{ $index % 2 ? 'alt' : '' }}">
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td class="text-left text-bold">{{ $line['name'] }}</td>
                    <td class="text-left text-muted" style="font-size:10px;">{{ $line['code'] ?: '-' }}</td>
                    <td class="text-center">{{ $line['quantity'] }}</td>
                    <td class="text-right">{{ $sym }}{{ number_format($line['price'], 2) }}</td>
                    <td class="text-right">{{ $sym }}{{ number_format($line['vat'], 2) }}</td>
                    <td class="text-right line-total">{{ $sym }}{{ number_format($line['line_grand'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ── TOTALS + QR ─────────────────────────────────────────── --}}
        <div class="totals-block">
            <div class="totals-side">
                <div class="t-row">
                    <span class="t-label">Subtotal (items)</span>
                    <span class="t-amount">{{ $sym }}{{ number_format($invoice['subtotal'], 2) }}</span>
                </div>
                <div class="t-row">
                    <span class="t-label">Delivery ({{ ucfirst($invoice['delivery_method']) }})</span>
                    <span class="t-amount">{{ $sym }}{{ number_format($invoice['delivery_fee'], 2) }}</span>
                </div>
                <div class="t-row">
                    <span class="t-label">Tax ({{ $rateLabel }}%)</span>
                    <span class="t-amount">{{ $sym }}{{ number_format($invoice['vat_total'], 2) }}</span>
                </div>
                <div class="grand-row">
                    <span class="grand-label">Grand Total</span>
                    <span class="grand-amount">{{ $sym }}{{ number_format($invoice['grand_total'], 2) }}</span>
                </div>
            </div>
            <div class="totals-right">
                <table style="margin-top:4px;">
                    <tr>
                        <td class="qr-cell" style="width:50%;">
                            <img src="{{ $invoice['qr_data_uri'] }}" alt="Invoice QR">
                            <div class="qr-label">Scan to verify invoice</div>
                        </td>
                        <td style="width:50%; vertical-align:top; padding-left:12px;">
                            <div style="font-size:9px; color:#9ca3af;">
                                <strong>{{ $seller['name'] }}</strong><br>
                                VAT: {{ $seller['vat_number'] ?: 'N/A' }}<br>
                                {{ $seller['address'] }}<br>
                                {{ $seller['phone'] ? 'Phone: '.$seller['phone'].'<br>' : '' }}
                                {{ $seller['email'] ? 'Email: '.$seller['email'] : '' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ── FOOTER ───────────────────────────────────────────────── --}}
        <div class="footer">
            <span>© {{ date('Y') }} {{ $seller['name'] }} — All rights reserved</span>
            <span>Invoice generated electronically · VAT {{ $rateLabel }}%</span>
        </div>

    </div>
</div>

</body>
</html>
