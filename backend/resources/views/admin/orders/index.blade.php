@extends('admin.layouts.app')
@section('title', 'Orders')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Orders List</h2>
        <div class="search-bar">
            <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order # or customer..." class="form-control" style="width: 300px;">
                <select name="status" class="form-control" style="width: 180px;">
                    <option value="">All Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="width: 170px;" title="To date">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if(request('search') || request('status') || request('date_to'))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                @endif
            </form>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number ?? '#' . $order->id }}</strong></td>
                    <td>{{ $order->customer_name ?? 'Guest' }}</td>
                    <td>{{ $order->customer_email ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}</td>
                    <td>
                        @php
                            $itemCount = isset($order->items_count) ? $order->items_count : '—';
                        @endphp
                        {{ $itemCount }}
                    </td>
                    <td><strong>${{ number_format($order->total ?? 0, 2) }}</strong></td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-secondary btn-sm">Status</a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-success btn-sm">Invoice</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($orders->hasPages())
        <div style="padding: 20px 24px;">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
