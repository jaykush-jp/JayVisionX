@extends('admin.layouts.app')
@section('title', 'Audit Logs')
@section('content')
<div class="page-header-row">
    <h2>Audit Logs <span class="count-badge">{{ $logs->total() }}</span></h2>
</div>

<form method="GET" class="crm-filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search action or user...">
    <select name="entity_type" class="form-control">
        <option value="">All entities</option>
        @foreach($entityTypes as $et)
        <option value="{{ $et }}" {{ request('entity_type')===$et ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$et)) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
    @if(request()->hasAny(['q','entity_type']))
    <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>When</th><th>Action</th><th>Entity</th><th>By</th><th>Details</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                <td><span class="lead-badge lead-badge-{{ \Illuminate\Support\Str::contains($log->action,'delet') ? 'lost' : (\Illuminate\Support\Str::contains($log->action,'creat') ? 'won' : 'qualified') }}">{{ $log->action }}</span></td>
                <td>{{ ucwords(str_replace('_',' ', $log->entity_type)) }}@if($log->entity_id) <small class="text-muted">#{{ $log->entity_id }}</small>@endif</td>
                <td><small>{{ $log->performed_by ?: 'system' }}</small></td>
                <td><small class="text-muted">{{ $log->changes ? \Illuminate\Support\Str::limit(json_encode($log->changes), 80) : '—' }}</small></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No audit entries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $logs->links() }}</div>
@endsection
