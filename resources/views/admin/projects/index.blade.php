{{-- resources/views/admin/projects/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Manage Projects')
@section('content')
<div class="page-header-row">
    <h2>Projects <span class="count-badge">{{ $projects->count() }}</span></h2>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Project</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Thumbnail</th><th>Title</th><th>Category</th><th>Client</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($projects as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $p->thumbnail_url }}" style="width:56px;height:40px;object-fit:cover;border-radius:4px"></td>
                <td><strong>{{ $p->title }}</strong><br><small class="text-muted">{{ $p->slug }}</small></td>
                <td>{{ $p->category ?? '-' }}</td>
                <td>{{ $p->client_name ?? '-' }}</td>
                <td><button class="toggle-btn {{ $p->is_active ? 'active':'inactive' }}" onclick="toggleStatus('{{ route('admin.projects.toggle-status',$p->id) }}',this)">{{ $p->is_active?'Active':'Inactive' }}</button></td>
                <td>{{ $p->sort_order }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.projects.edit',$p->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.projects.destroy',$p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No projects yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
