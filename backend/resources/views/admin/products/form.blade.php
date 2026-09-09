@php
$isEdit = isset($product);
$action = $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store');
$method = $isEdit ? 'PUT' : 'POST';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="form-row">
        <div class="form-group">
            <label class="required" for="name">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $isEdit ? $product->name : '') }}" required>
        </div>

        <div class="form-group">
            <label for="slug">Slug <small style="color: #6b7280; font-weight: normal;">(auto-generated if empty)</small></label>
            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $isEdit ? $product->slug : '') }}">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="required" for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $isEdit ? $product->category_id : '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="brand_id">Brand</label>
            <select name="brand_id" id="brand_id" class="form-control">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $isEdit ? $product->brand_id : '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="required" for="price">Price ($)</label>
            <input type="number" name="price" id="price" step="0.01" min="0" class="form-control" value="{{ old('price', $isEdit ? $product->price : '') }}" required>
        </div>

        <div class="form-group">
            <label class="required" for="stock">Stock Quantity</label>
            <input type="number" name="stock" id="stock" min="0" class="form-control" value="{{ old('stock', $isEdit ? $product->stock : '') }}" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="active" {{ old('status', $isEdit ? $product->status : 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $isEdit ? $product->status : '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image_file">Image</label>
            <input type="file" name="image_file" id="image_file" class="form-control" accept="image/*">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
        </div>

        <div class="form-group">
            <label for="overview">Overview</label>
            <textarea name="overview" id="overview" class="form-control" rows="5">{{ old('overview', $isEdit ? $product->overview : '') }}</textarea>
        </div>
    </div>

    <div class="form-group">
        <label for="features">Features <small style="color: #6b7280; font-weight: normal;">(comma separated or JSON)</small></label>
        <textarea name="features" id="features" class="form-control" rows="4" placeholder="Wireless, Noise Cancelling, Bluetooth 5.0...">{{ old('features', $isEdit ? $product->features : '') }}</textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Product' : 'Create Product' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
