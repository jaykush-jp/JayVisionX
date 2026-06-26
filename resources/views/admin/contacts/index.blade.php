@extends('admin.layouts.app')
@section('title', 'Contact Enquiries')
@section('content')
<div class="page-header-row">
    <h2>Contact Enquiries <span class="count-badge">{{ $contacts->total() }}</span></h2>
</div>
<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Service</th><th>Budget</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($contacts as $c)
            <tr class="{{ !$c->is_read ? 'row-unread' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $c->name }}</strong></td>
                <td><a href="mailto:{{ $c->email }}">{{ $c->email }}</a></td>
                <td>{{ $c->phone ?? '-' }}</td>
                <td>{{ $c->service_interested ?? '-' }}</td>
                <td>{{ $c->budget ?? '-' }}</td>
                <td>{{ $c->created_at->format('M d, Y H:i') }}</td>
                <td>
                    @if(!$c->is_read)
                    <span class="badge badge-new">New</span>
                    @else
                    <span class="badge badge-read">Read</span>
                    @endif
                </td>
                <td class="actions-cell">
                    <a href="{{ route('admin.contacts.show', $c->id) }}" class="btn-sm btn-view"><i class="fas fa-eye"></i></a>
                    <form action="{{ route('admin.contacts.destroy', $c->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">No enquiries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $contacts->links() }}</div>
@endsection
