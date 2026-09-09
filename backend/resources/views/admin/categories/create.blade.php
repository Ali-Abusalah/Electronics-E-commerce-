@extends('admin.layouts.app')
@section('title', 'Add Category')

@section('content')
<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h2>Add New Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">Back to Categories</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="required" for="name">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug <small style="color: #6b7280; font-weight: normal;">(auto-generated from name if empty)</small></label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}">
            </div>

            <div class="form-group">
                <label for="image_file">Image</label>
                <input type="file" name="image_file" id="image_file" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('name').addEventListener('input', function() {
    var slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('slug').value = slug;
});
</script>
@endsection
