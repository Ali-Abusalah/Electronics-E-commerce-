@extends('admin.layouts.app')
@section('title', 'Order Details - ' . ($order->order_number ?? '#' . $order->id))

@section('content')
<div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">&larr; Back to Orders</a>
    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-success btn-sm">⬇ Download Tax Invoice (PDF)</a>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2>Order {{ $order->order_number ?? '#' . $order->id }}</h2>
        <span class="badge badge-{{ $order->status }}" style="padding: 8px 14px; font-size: 13px;">{{ strtoupper($order->status) }}</span>
    </div>
    <div class="card-body">
        <div class="order-details-grid">
            <div class="detail-box">
                <h4>Customer Information</h4>
                <p><strong>Name:</strong> {{ $order->customer_name ?? 'Guest' }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email ?? '—' }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone ?? '—' }}</p>
                <p><strong>Address:</strong> {{ $order->customer_address ?? '—' }}</p>
            </div>
            <div class="detail-box">
                <h4>Order Information</h4>
                <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}</p>
                <p><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($order->updated_at)->format('M d, Y H:i') }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment_method ?? '—' }}</p>
                <p><strong>Payment Status:</strong> {{ $order->payment_status ?? '—' }}</p>
            </div>
        </div>

        <h3 style="margin: 28px 0 16px; font-size: 16px;">Order Items</h3>
        <div style="background: #f9fafb; border-radius: 8px; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @forelse($items as $item)
                    @php
                        $itemSubtotal = ($item->price ?? 0) * ($item->quantity ?? 0);
                        $subtotal += $itemSubtotal;
                    @endphp
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($item->product_image)
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="thumbnail" onerror="this.style.display='none'">
                                @endif
                                <div>
                                    <strong>{{ $item->product_name ?? 'Product #' . ($item->product_id ?? 'N/A') }}</strong>
                                    @if(isset($item->product_attributes))
                                        <br><small style="color: #6b7280;">{{ $item->product_attributes }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>${{ number_format($item->price ?? 0, 2) }}</td>
                        <td>x {{ $item->quantity ?? 1 }}</td>
                        <td><strong>${{ number_format($itemSubtotal, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #6b7280;">No items found for this order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $taxAmount = (float) ($order->tax_amount ?? 0);
            $taxRate = (float) ($order->tax_rate ?? 0);
            $deliveryFee = (float) ($order->delivery_fee ?? 0);
        @endphp
        <div style="margin-top: 24px; max-width: 320px; margin-left: auto;">
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="color: #4b5563;">Subtotal:</span>
                <strong>${{ number_format($subtotal, 2) }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="color: #4b5563;">VAT{{ $taxRate ? ' (' . rtrim(rtrim(number_format($taxRate, 2), '0'), '.') . '%)' : '' }}:</span>
                <strong>${{ number_format($taxAmount, 2) }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="color: #4b5563;">Delivery:</span>
                <strong>${{ number_format($deliveryFee, 2) }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 18px;">
                <span><strong>Total (incl. VAT):</strong></span>
                <strong style="color: #4f46e5;">${{ number_format($order->total ?? 0, 2) }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Update Order Status</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group" style="max-width: 400px;">
                <label for="status">Order Status</label>
                <select name="status" id="status" class="form-control" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Status</button>
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-secondary">Edit Page</a>
            </div>
        </form>
    </div>
</div>
@endsection
