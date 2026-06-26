@extends('admin.layouts.app')
@section('title', 'Manage Services')
@section('content')
<div class="page-header-row">
    <h2>Services <span class="count-badge">{{ $services->count() }}</span></h2>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Service</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Icon</th><th>Title</th><th>Status</th><th>Home</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($services as $s)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><i class="fas {{ $s->icon ?? 'fa-star' }}" style="font-size:1.4rem;color:var(--primary)"></i></td>
                <td><strong>{{ $s->title }}</strong><br><small class="text-muted">{{ $s->slug }}</small></td>
                <td>
                    <button class="toggle-btn {{ $s->is_active ? 'active' : 'inactive' }}" onclick="toggleStatus('{{ route('admin.services.toggle-status',$s->id) }}', this)">
                        {{ $s->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </td>
                <td>{{ $s->show_on_home ? '<span class="badge badge-yes">Yes</span>' : '<span class="badge badge-no">No</span>' }}</td>
                <td>{{ $s->sort_order }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.services.edit', $s->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.services.destroy', $s->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this service?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No services yet. <a href="{{ route('admin.services.create') }}">Add one</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
