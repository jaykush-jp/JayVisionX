@extends('admin.layouts.app')
@section('title', 'Packages & Pricing')
@section('content')
<div class="page-header-row">
    <h2>Packages &amp; Pricing <span class="count-badge">{{ $packages->total() }}</span></h2>
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Package</a>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Package</th><th>Service</th><th>Price</th><th>Timeline</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($packages as $p)
            <tr>
                <td><strong>{{ $p->name }}</strong>@if($p->tier)<br><small class="text-muted">{{ ucfirst($p->tier) }}</small>@endif</td>
                <td>{{ $p->service->title ?? '—' }}</td>
                <td><strong>{{ $p->price_label }}</strong>@if($p->tax_note)<br><small class="text-muted">{{ $p->tax_note }}</small>@endif</td>
                <td class="text-muted">{{ $p->estimated_timeline ?: '—' }}</td>
                <td><span class="badge {{ $p->is_active ? 'badge-yes' : 'badge-no' }}">{{ $p->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td class="actions-cell">
                    <a href="{{ route('admin.packages.edit', $p->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.packages.destroy', $p->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this package?')">
                        @csrf @method('DELETE')
                        <button class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No packages yet. <a href="{{ route('admin.packages.create') }}">Add one</a> — the chatbot quotes only from active packages.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $packages->links('admin.partials.pagination') }}
@endsection
