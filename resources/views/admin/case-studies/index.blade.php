@extends('admin.layouts.app')
@section('title','Case Studies')
@section('content')
<div class="page-header-row">
    <h2>Case Studies <span class="count-badge">{{ $caseStudies->count() }}</span></h2>
    <a href="{{ route('admin.case-studies.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Case Study</a>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Thumbnail</th><th>Title</th><th>Client</th><th>Industry</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($caseStudies as $cs)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><img src="{{ $cs->thumbnail_url }}" style="width:56px;height:40px;object-fit:cover;border-radius:4px"></td>
                <td><strong>{{ $cs->title }}</strong></td>
                <td>{{ $cs->client ?? '-' }}</td>
                <td>{{ $cs->industry ?? '-' }}</td>
                <td><span class="badge {{ $cs->is_active?'badge-yes':'badge-no' }}">{{ $cs->is_active?'Active':'Inactive' }}</span></td>
                <td class="actions-cell">
                    <a href="{{ route('admin.case-studies.edit',$cs->id) }}" class="btn-sm btn-edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.case-studies.destroy',$cs->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No case studies yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
