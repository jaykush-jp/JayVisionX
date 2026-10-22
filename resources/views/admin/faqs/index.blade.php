@extends('admin.layouts.app')
@section('title','Manage FAQs')
@section('content')
<div class="page-header-row">
    <h2>FAQs <span class="count-badge">{{ $faqs->total() }}</span></h2>
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add FAQ</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Question</th><th>Category</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($faqs as $f)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="max-width:400px"><strong>{{ $f->question }}</strong><br><small class="text-muted">{{ Str::limit($f->answer,80) }}</small></td>
                <td>{{ $f->category ?? '-' }}</td>
                <td><span class="badge {{ $f->is_active?'badge-yes':'badge-no' }}">{{ $f->is_active?'Active':'Inactive' }}</span></td>
                <td>{{ $f->sort_order }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.faqs.edit',$f->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.faqs.destroy',$f->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No FAQs yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $faqs->links('admin.partials.pagination') }}
@endsection
