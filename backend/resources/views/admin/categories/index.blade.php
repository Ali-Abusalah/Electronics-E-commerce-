@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Categories List</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Category
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>
                        @if($category->image)
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="thumbnail" onerror="this.style.display='none'">
                        @else
                            <div class="thumbnail" style="display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 11px;">N/A</div>
                        @endif
                    </td>
                    <td><strong>{{ $category->name }}</strong></td>
                    <td><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-size: 13px;">{{ $category->slug }}</code></td>
                    <td style="max-width: 300px; color: #6b7280;">{{ Str::limit($category->description ?? '—', 60) }}</td>
                    <td>{{ \Carbon\Carbon::parse($category->created_at)->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">No categories found. <a href="{{ route('admin.categories.create') }}">Create your first category</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($categories->hasPages())
        <div style="padding: 20px 24px;">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
