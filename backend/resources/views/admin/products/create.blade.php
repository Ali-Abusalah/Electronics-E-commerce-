@extends('admin.layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Add New Product</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">Back to Products</a>
    </div>
    <div class="card-body">
        @include('admin.products.form')
    </div>
</div>
@endsection
