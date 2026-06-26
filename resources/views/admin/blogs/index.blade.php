@extends('admin.layouts.app')
@section('title', 'Manage Blogs')
@section('content')
<div class="page-header-row">
    <h2>Blog Posts <span class="count-badge">{{ $blogs->count() }}</span></h2>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Post</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Thumbnail</th><th>Title</th><th>Category</th><th>Views</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($blogs as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $b->thumbnail_url }}" style="width:50px;height:40px;object-fit:cover;border-radius:4px"></td>
                <td><strong>{{ $b->title }}</strong><br><small class="text-muted">{{ $b->slug }}</small></td>
                <td>{{ $b->category ?? '-' }}</td>
                <td>{{ $b->views }}</td>
                <td>
                    <button class="toggle-btn {{ $b->is_active ? 'active' : 'inactive' }}" onclick="toggleStatus('{{ route('admin.blogs.toggle-status',$b->id) }}', this)">
                        {{ $b->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </td>
                <td>{{ $b->created_at->format('M d, Y') }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.blogs.edit', $b->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <a href="{{ route('blog.show', $b->slug) }}" target="_blank" class="btn-sm btn-view"><i class="fas fa-eye"></i></a>
                    <form action="{{ route('admin.blogs.destroy', $b->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No blog posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
