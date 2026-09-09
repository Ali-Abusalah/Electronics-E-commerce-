@extends('admin.layouts.app')
@section('title', 'Products')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Products List</h2>
        <div class="search-bar">
            <form method="GET" action="{{ route('admin.products.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="form-control" style="width: 280px;">
                <select name="category_id" class="form-control" style="width: 200px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                @if(request('search') || request('category_id'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Product
            </a>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="thumbnail" onerror="this.style.display='none'">
                        @else
                            <div class="thumbnail" style="display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 11px;">N/A</div>
                        @endif
                    </td>
                    <td><strong>{{ $product->name }}</strong><br><small style="color: #6b7280;">{{ $product->slug }}</small></td>
                    <td>{{ $product->category_name ?? '—' }}</td>
                    <td>{{ $product->brand_name ?? '—' }}</td>
                    <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                    <td>
                        @if($product->stock <= 0)
                            <span class="badge badge-stock-low">{{ $product->stock }} (Out)</span>
                        @elseif($product->stock < 10)
                            <span class="badge badge-stock-low">{{ $product->stock }} (Low)</span>
                        @else
                            <span class="badge badge-stock-ok">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $product->status }}">{{ ucfirst($product->status) }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">No products found. <a href="{{ route('admin.products.create') }}">Create your first product</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div style="padding: 20px 24px;">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
