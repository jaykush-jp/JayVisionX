@extends('admin.layouts.app')
@section('title', 'Quote Requests')
@section('content')
<div class="page-header-row">
    <h2>Quote Requests <span class="count-badge">{{ $quotes->total() }}</span></h2>
    <a href="{{ route('admin.quotes.build') }}" class="btn btn-primary"><i class="fas fa-calculator"></i> Quote Builder</a>
</div>

<form method="GET" class="crm-filter-bar">
    <select name="status" class="form-control">
        <option value="">All statuses</option>
        @foreach($statuses as $k => $v)
        <option value="{{ $k }}" {{ request('status')===$k ? 'selected' : '' }}>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
    @if(request('status'))<a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary">Reset</a>@endif
</form>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Project</th><th>Estimate</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
            @forelse($quotes as $q)
            <tr>
                <td><strong>{{ $q->name ?: 'Visitor' }}</strong>@if($q->email)<br><small class="text-muted">{{ $q->email }}</small>@endif</td>
                <td>{{ $q->projectLabel() }}</td>
                <td>{{ $q->estimateLabel() }}</td>
                <td><span class="lead-badge lead-badge-{{ ['new'=>'new','reviewed'=>'qualified','sent'=>'proposal','won'=>'won','lost'=>'lost'][$q->status] ?? 'new' }}">{{ $statuses[$q->status] ?? $q->status }}</span></td>
                <td>{{ $q->created_at->format('d M Y') }}</td>
                <td><a href="{{ route('admin.quotes.show', $q->id) }}" class="btn-sm btn-view"><i class="fas fa-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No quote requests yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $quotes->links() }}</div>
@endsection
