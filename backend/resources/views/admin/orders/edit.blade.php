@extends('admin.layouts.app')
@section('title', 'Update Order Status - ' . ($order->order_number ?? '#' . $order->id))

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-sm">&larr; Back to Order Details</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h2>Update Order Status</h2>
    </div>
    <div class="card-body">
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
            <p style="margin-bottom: 8px;"><strong>Order:</strong> {{ $order->order_number ?? '#' . $order->id }}</p>
            <p style="margin-bottom: 8px;"><strong>Current Status:</strong> <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
            <p><strong>Total:</strong> ${{ number_format($order->total ?? 0, 2) }}</p>
        </div>

        <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="status">New Status</label>
                <select name="status" id="status" class="form-control" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Status</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
