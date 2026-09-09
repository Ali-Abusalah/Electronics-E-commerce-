@extends('admin.layouts.app')
@section('title', 'Edit Product - ' . $product->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Product</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Back to Products</a>
    </div>
    <div class="card-body">
        @include('admin.products.form', ['product' => $product])
    </div>
</div>
@endsection
