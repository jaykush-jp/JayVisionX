@extends('admin.layouts.app')
@section('title','Team')
@section('content')
<div class="page-header-row">
    <h2>Team <span class="count-badge">{{ $members->total() }}</span></h2>
    <a href="{{ route('admin.team.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Member</a>
</div>
@if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Photo</th><th>Name</th><th>Designation</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($members as $m)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($m->photo)
                    <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" style="width:46px;height:46px;object-fit:cover;border-radius:50%">
                    @else
                    <span style="width:46px;height:46px;border-radius:50%;background:var(--primary,#0a7ce0);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700">{{ strtoupper(substr($m->name,0,1)) }}</span>
                    @endif
                </td>
                <td><strong>{{ $m->name }}</strong></td>
                <td>{{ $m->designation ?? '-' }}</td>
                <td>{{ $m->sort_order }}</td>
                <td><button class="toggle-btn {{ $m->is_active ? 'active':'inactive' }}" onclick="toggleStatus('{{ route('admin.team.toggle-status',$m->id) }}',this)">{{ $m->is_active?'Active':'Inactive' }}</button></td>
                <td class="actions-cell">
                    <a href="{{ route('admin.team.edit',$m->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.team.destroy',$m->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this member?')">@csrf @method('DELETE')<button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No team members yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $members->links('admin.partials.pagination') }}
@endsection
