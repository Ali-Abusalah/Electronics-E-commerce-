@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-value">{{ $totalProducts }}</div>
    </div>
    <div class="stat-card orders">
        <h3>Total Orders</h3>
        <div class="stat-value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card revenue">
        <h3>Total Revenue</h3>
        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="stat-card categories">
        <h3>Categories</h3>
        <div class="stat-value">{{ $totalCategories }}</div>
    </div>
    <div class="stat-card">
        <h3>Brands</h3>
        <div class="stat-value">{{ $totalBrands }}</div>
    </div>
    <div class="stat-card outofstock">
        <h3>Out of Stock</h3>
        <div class="stat-value">{{ $outOfStock }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Latest Orders</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">View All Orders</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestOrders as $order)
                <tr>
                    <td><strong>{{ $order->order_number ?? '#' . $order->id }}</strong></td>
                    <td>{{ $order->customer_name ?? 'Guest' }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                    <td>${{ number_format($order->total ?? 0, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status ?? 'pending' }}">{{ ucfirst($order->status ?? 'pending') }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
