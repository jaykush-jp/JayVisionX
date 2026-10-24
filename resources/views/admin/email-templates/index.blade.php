@extends('admin.layouts.app')
@section('title', 'Email Templates')
@section('content')
<div class="page-header-row">
    <h2>Email Templates <span class="count-badge">{{ $templates->total() }}</span></h2>
    <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Template</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Subject</th><th>Updated</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($templates as $t)
            <tr>
                <td><strong>{{ $t->name }}</strong></td>
                <td>{{ $t->subject }}</td>
                <td>{{ $t->updated_at->format('d M Y') }}</td>
                <td class="actions-cell">
                    <a href="{{ route('admin.email-templates.edit', $t->id) }}" class="btn-sm btn-view"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.email-templates.destroy', $t->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete template?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No templates yet. Create one to reuse in lead replies.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $templates->links() }}</div>
@endsection
