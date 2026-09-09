@extends('admin.layouts.app')
@section('title', 'Brands')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Brands List</h2>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Brand
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Website</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td>
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="thumbnail" onerror="this.style.display='none'">
                        @else
                            <div class="thumbnail" style="display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 11px;">N/A</div>
                        @endif
                    </td>
                    <td><strong>{{ $brand->name }}</strong></td>
                    <td><code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px; font-size: 13px;">{{ $brand->slug }}</code></td>
                    <td>
                        @if($brand->website)
                            <a href="{{ $brand->website }}" target="_blank" style="color: #4f46e5;">{{ Str::limit($brand->website, 25) }}</a>
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="max-width: 250px; color: #6b7280;">{{ Str::limit($brand->description ?? '—', 50) }}</td>
                    <td>{{ \Carbon\Carbon::parse($brand->created_at)->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">No brands found. <a href="{{ route('admin.brands.create') }}">Create your first brand</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($brands->hasPages())
        <div style="padding: 20px 24px;">
            {{ $brands->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
