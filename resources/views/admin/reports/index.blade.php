@extends('admin.layouts.app')
@section('title', 'Reports')
@section('content')
<div class="page-header-row">
    <h2>Reports &amp; Analytics</h2>
</div>

<div class="stats-row">
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fas fa-comments"></i></div>
        <div class="stat-info"><span class="stat-num">{{ $chatTotal }}</span><span class="stat-label">Conversations</span></div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info"><span class="stat-num">{{ $chatLeadConv }}%</span><span class="stat-label">Chat → lead rate</span></div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="fas fa-handshake"></i></div>
        <div class="stat-info"><span class="stat-num">{{ $conversion }}%</span><span class="stat-label">Lead → won rate</span></div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="stat-info"><span class="stat-num">{{ $quotesTotal }}</span><span class="stat-label">Quote requests</span></div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fas fa-robot"></i></div>
        <div class="stat-info"><span class="stat-num">{{ $aiMessages }}</span><span class="stat-label">AI replies</span></div>
    </div>
</div>

<!-- Message trend -->
<div class="dashboard-section">
    <h3 class="section-heading">Chat messages — last 14 days</h3>
    <div class="report-bars">
        @foreach($trend as $day => $count)
        <div class="report-bar" title="{{ $day }}: {{ $count }}">
            <div class="report-bar-fill" style="height:{{ $count > 0 ? max(6, round($count / $trendMax * 100)) : 2 }}%"></div>
            <span class="report-bar-label">{{ \Illuminate\Support\Str::of($day)->explode(' ')->first() }}</span>
        </div>
        @endforeach
    </div>
</div>

<div class="crm-detail-grid">
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Leads by source</h3>
            @forelse($leadsBySource as $src => $c)
            <div class="report-row"><span>{{ ucfirst(str_replace('_',' ', $src ?: 'unknown')) }}</span><strong>{{ $c }}</strong></div>
            @empty<p class="text-muted">No leads yet.</p>@endforelse
        </div>
        <div class="admin-form-card">
            <h3 class="section-heading">Most-requested services</h3>
            @forelse($topServices as $svc => $c)
            <div class="report-row"><span>{{ $svc }}</span><strong>{{ $c }}</strong></div>
            @empty<p class="text-muted">No service interest recorded yet.</p>@endforelse
        </div>
    </div>
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Conversations by status</h3>
            @forelse($chatByStatus as $st => $c)
            <div class="report-row"><span>{{ ucfirst($st) }}</span><strong>{{ $c }}</strong></div>
            @empty<p class="text-muted">No conversations yet.</p>@endforelse
            <div class="report-row"><span>Human takeover</span><strong>{{ $chatHuman }}</strong></div>
            <div class="report-row"><span>Became leads</span><strong>{{ $chatToLead }}</strong></div>
        </div>
        <div class="admin-form-card">
            <h3 class="section-heading">Quote requests by type</h3>
            @forelse($quotesByType as $type => $c)
            <div class="report-row"><span>{{ \App\Models\QuoteRequest::PROJECT_TYPES[$type] ?? $type }}</span><strong>{{ $c }}</strong></div>
            @empty<p class="text-muted">No quote requests yet.</p>@endforelse
            <div class="report-row"><span>Unanswered (fallback) replies</span><strong>{{ $unanswered }}</strong></div>
        </div>
    </div>
</div>
@endsection
