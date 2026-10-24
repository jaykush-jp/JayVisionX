@extends('admin.layouts.app')
@section('title', 'Live Inbox')
@section('content')
<div class="page-header-row">
    <h2>Live Inbox <span class="count-badge">{{ $sessions->total() }}</span></h2>
    <a href="{{ route('admin.ai-settings.index') }}" class="btn btn-secondary"><i class="fas fa-robot"></i> AI Config</a>
</div>

<div class="crm-stat-strip">
    <span class="crm-stat"><span class="crm-stat-num">{{ $stats['total'] }}</span><span class="crm-stat-label">All chats</span></span>
    <a href="{{ route('admin.inbox.index', ['status' => 'open']) }}" class="crm-stat {{ request('status')==='open' ? 'active' : '' }}"><span class="crm-stat-num">{{ $stats['open'] }}</span><span class="crm-stat-label">Open</span></a>
    <a href="{{ route('admin.inbox.index', ['filter' => 'human']) }}" class="crm-stat {{ request('filter')==='human' ? 'active' : '' }}"><span class="crm-stat-num">{{ $stats['human'] }}</span><span class="crm-stat-label">Human takeover</span></a>
    <span class="crm-stat"><span class="crm-stat-num">{{ $stats['withLead'] }}</span><span class="crm-stat-label">Became leads</span></span>
</div>

<form method="GET" class="crm-filter-bar">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search visitor name, email, phone...">
    <select name="status" class="form-control">
        <option value="">All statuses</option>
        @foreach(\App\Http\Controllers\Admin\AdminInboxController::STATUSES as $k => $v)
        <option value="{{ $k }}" {{ request('status')===$k ? 'selected' : '' }}>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
    @if(request()->hasAny(['q','status','filter']))
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <thead><tr><th>Visitor</th><th>Messages</th><th>Status</th><th>Handled by</th><th>Lead</th><th>Last activity</th><th>Action</th></tr></thead>
        <tbody>
            @forelse($sessions as $s)
            <tr>
                <td>
                    <strong>{{ $s->visitor_name ?: 'Anonymous visitor' }}</strong>
                    @if($s->visitor_email)<br><small class="text-muted">{{ $s->visitor_email }}</small>@endif
                    @if($s->visitor_phone)<br><small class="text-muted">{{ $s->visitor_phone }}</small>@endif
                </td>
                <td>{{ $s->messages_count }}</td>
                <td><span class="lead-badge lead-badge-{{ ['open'=>'new','pending'=>'contacted','resolved'=>'won','closed'=>'lost'][$s->status] ?? 'new' }}">{{ ucfirst($s->status) }}</span></td>
                <td>@if($s->is_ai_active)<span class="lead-badge lead-badge-qualified">AI</span>@else<span class="lead-badge lead-badge-proposal">Human</span>@endif</td>
                <td>@if($s->lead_id)<a href="{{ route('admin.contacts.show', $s->lead_id) }}">#{{ $s->lead_id }}</a>@else — @endif</td>
                <td>{{ optional($s->last_message_at ?? $s->created_at)->diffForHumans() }}</td>
                <td><a href="{{ route('admin.inbox.show', $s->id) }}" class="btn-sm btn-view" title="Open"><i class="fas fa-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No conversations yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pagination-wrapper">{{ $sessions->links() }}</div>
@endsection
