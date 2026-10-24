@extends('admin.layouts.app')
@section('title', 'Knowledge Base')
@section('content')
<div class="page-header-row">
    <h2>Knowledge Base <span class="count-badge">{{ $articles->total() }}</span></h2>
    <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Entry</a>
</div>

<form method="GET" class="admin-filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search title or content...">
    <select name="category" class="form-control">
        <option value="">All categories</option>
        @foreach($categories as $key => $label)
            <option value="{{ $key }}" @selected(request('category')===$key)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="status" class="form-control">
        <option value="">All statuses</option>
        @foreach(['draft'=>'Draft','published'=>'Published','archived'=>'Archived'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
        @endforeach
    </select>
    <button class="btn btn-secondary"><i class="fas fa-filter"></i> Filter</button>
    @if(request()->hasany(['q','category','status']))
        <a href="{{ route('admin.knowledge.index') }}" class="btn btn-secondary">Clear</a>
    @endif
</form>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Search</th><th>Updated</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($articles as $a)
            <tr>
                <td><strong>{{ $a->title }}</strong></td>
                <td>{{ $categories[$a->category] ?? $a->category }}</td>
                <td>
                    <span class="badge {{ $a->status==='published' ? 'badge-yes' : ($a->status==='archived' ? 'badge-no' : '') }}">{{ ucfirst($a->status) }}</span>
                </td>
                <td>{!! $a->is_searchable ? '<i class="fas fa-check" style="color:#1a7f43"></i>' : '<i class="fas fa-minus text-muted"></i>' !!}</td>
                <td class="text-muted">{{ $a->updated_at->diffForHumans() }}</td>
                <td class="actions-cell">
                    @if($a->status !== 'published')
                    <form action="{{ route('admin.knowledge.status', $a->id) }}" method="POST" style="display:inline">
                        @csrf <input type="hidden" name="status" value="published">
                        <button class="btn-sm btn-view" title="Publish"><i class="fas fa-globe"></i></button>
                    </form>
                    @else
                    <form action="{{ route('admin.knowledge.status', $a->id) }}" method="POST" style="display:inline">
                        @csrf <input type="hidden" name="status" value="archived">
                        <button class="btn-sm btn-edit" title="Archive"><i class="fas fa-box-archive"></i></button>
                    </form>
                    @endif
                    <a href="{{ route('admin.knowledge.edit', $a->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.knowledge.destroy', $a->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this entry?')">
                        @csrf @method('DELETE')
                        <button class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No knowledge entries yet. <a href="{{ route('admin.knowledge.create') }}">Add one</a> — only <strong>published</strong> entries are used by the chatbot.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $articles->links('admin.partials.pagination') }}
@endsection
