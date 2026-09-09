@extends('admin.layouts.app')
@section('title', 'Edit Brand - ' . $brand->name)

@section('content')
<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h2>Edit Brand</h2>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm">Back to Brands</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.brands.update', $brand->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="required" for="name">Brand Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug <small style="color: #6b7280; font-weight: normal;">(auto-generated from name if empty)</small></label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $brand->slug) }}">
            </div>

            <div class="form-group">
                <label for="logo">Logo URL</label>
                <input type="text" name="logo" id="logo" class="form-control" value="{{ old('logo', $brand->logo) }}" placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="website">Website URL</label>
                <input type="text" name="website" id="website" class="form-control" value="{{ old('website', $brand->website) }}" placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $brand->description) }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Brand</button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
