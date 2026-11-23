@extends('admin.layouts.app')
@section('title', 'AI Insights')

@section('head_extra')
<style>
.ai-sub { color:#64748b; margin:-6px 0 18px; }
.ai-tabs { display:flex; gap:8px; align-items:center; margin-bottom:18px; flex-wrap:wrap; }
.ai-tab { border:1px solid #e1e8f3; background:#eef3fb; color:#44566d; border-radius:9px; padding:7px 14px; font-size:.82rem; font-weight:600; text-decoration:none; }
.ai-tab.active { background:var(--primary,#4b40e6); color:#fff; border-color:transparent; }
.ai-tab.ghost { margin-left:auto; background:#fff; }
.ai-grid4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:16px; }
.ai-kpi { background:#fff; border:1px solid #e6ecf5; border-radius:14px; padding:16px 18px; }
.ai-kpi small { color:#8496ad; font-size:.8rem; }
.ai-kpi b { display:block; font-size:1.5rem; color:#0a1628; margin:4px 0 2px; }
.ai-kpi span { color:#8496ad; font-size:.82rem; }
.ai-active-bar { background:#fff; border:1px solid #e6ecf5; border-radius:12px; padding:13px 18px; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; font-size:.88rem; flex-wrap:wrap; gap:8px; }
.ai-active-bar a { color:var(--primary,#4b40e6); font-weight:600; }
.ai-provs { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:16px; }
.ai-prov { background:#fff; border:1px solid #e6ecf5; border-top:3px solid #9aa6bb; border-radius:14px; padding:18px; }
.ai-prov.gemini { border-top-color:#4285f4; }
.ai-prov.openai { border-top-color:#10a37f; }
.ai-prov.anthropic { border-top-color:#d97757; }
.ai-prov-head { display:flex; justify-content:space-between; align-items:center; }
.ai-prov-head b { font-size:1rem; color:#0a1628; }
.ai-badge { font-size:.68rem; font-weight:700; color:#1a9f54; background:#e7f7ed; padding:3px 9px; border-radius:20px; }
.ai-prov-cost { font-size:1.7rem; font-weight:800; color:#0a1628; margin:8px 0 2px; }
.ai-prov-meta { color:#8496ad; font-size:.82rem; }
.ai-prov-tip { font-size:.75rem; color:#9aa6bb; margin:12px 0; line-height:1.4; }
.ai-prov-rows { border-top:1px solid #eef2f8; padding-top:10px; font-size:.84rem; }
.ai-prov-rows div { display:flex; justify-content:space-between; padding:3px 0; color:#44566d; }
.ai-prov-rows b { color:#0a1628; font-variant-numeric:tabular-nums; }
.ai-prov-rows .mono { font-family:ui-monospace,monospace; font-size:.8rem; }
.ai-ok { color:#1a9f54; font-weight:600; } .ai-no { color:#c0392b; font-weight:600; }
.ai-panel { background:#fff; border:1px solid #e6ecf5; border-radius:14px; padding:20px; margin-bottom:16px; }
.ai-panel h3 { margin:0 0 16px; font-size:1rem; color:#0a1628; }
.ai-chart { display:flex; align-items:flex-end; gap:4px; height:150px; }
.ai-bar { flex:1; background:linear-gradient(to top,var(--primary,#4b40e6),#7b74f0); border-radius:4px 4px 0 0; min-height:2px; transition:.2s; }
.ai-bar:hover { filter:brightness(1.1); }
.ai-chart-foot { display:flex; justify-content:space-between; color:#9aa6bb; font-size:.76rem; margin-top:8px; }
.ai-tbl { width:100%; border-collapse:collapse; }
.ai-tbl th { text-align:left; color:#8496ad; font-size:.74rem; text-transform:uppercase; padding:8px 10px; border-bottom:1px solid #eef2f8; }
.ai-tbl td { padding:12px 10px; border-bottom:1px solid #f1f5fb; font-size:.88rem; color:#223; }
.ai-tbl td.num { text-align:right; font-variant-numeric:tabular-nums; }
.ai-tbl .prov { color:var(--primary,#4b40e6); font-weight:600; }
.ai-tbl .mono { font-family:ui-monospace,monospace; font-size:.82rem; }
.ai-tbl .cost b { display:block; } .ai-tbl .cost small { color:#8496ad; }
.ai-note { color:#8496ad; font-size:.8rem; line-height:1.5; }
@media (max-width:1000px){ .ai-grid4{grid-template-columns:repeat(2,1fr)} .ai-provs{grid-template-columns:1fr} }
</style>
@endsection

@section('content')
@php
    $rupee = fn($usd) => '₹' . number_format($usd * $inr, 2);
    $dollar = fn($usd) => '$' . number_format($usd, $usd < 1 ? 4 : 2);
    $peak = max(0.0001, max($daily ?: [0]));
@endphp

<div class="page-header-row"><h2>AI Insights</h2></div>
<p class="ai-sub">Spend across Gemini, OpenAI and Claude — per provider and per model.</p>

<div class="ai-tabs">
    @foreach(['7'=>'7 days','30'=>'30 days','month'=>'This month','all'=>'All time'] as $k => $lbl)
    <a href="{{ route('admin.ai-insights.index', ['range'=>$k]) }}" class="ai-tab {{ $range===$k ? 'active':'' }}">{{ $lbl }}</a>
    @endforeach
    <a href="{{ route('admin.ai-insights.index', ['range'=>$range]) }}" class="ai-tab ghost"><i class="fas fa-rotate-right"></i> Refresh</a>
</div>

<div class="ai-grid4">
    <div class="ai-kpi"><small>This month</small><b>{{ $rupee($summary['month']) }}</b><span>{{ $dollar($summary['month']) }}</span></div>
    <div class="ai-kpi"><small>Today</small><b>{{ $rupee($summary['today']) }}</b><span>{{ $dollar($summary['today']) }}</span></div>
    <div class="ai-kpi"><small>30 days</small><b>{{ $rupee($summary['d30']) }}</b><span>{{ $dollar($summary['d30']) }}</span></div>
    <div class="ai-kpi"><small>All time</small><b>{{ $rupee($summary['all']) }}</b><span>{{ $dollar($summary['all']) }}</span></div>
</div>

<div class="ai-active-bar">
    <span>Active provider: <a href="{{ route('admin.ai-settings.index') }}">{{ $activeLabel }}</a></span>
    <span class="ai-note">≈ ₹{{ (int) $inr }}/$ · figures are estimates</span>
</div>

<div class="ai-provs">
    @foreach($providers as $key => $p)
    <div class="ai-prov {{ $key }}">
        <div class="ai-prov-head">
            <b>{{ $p['label'] }}</b>
            @if($p['active'])<span class="ai-badge">ACTIVE</span>@endif
        </div>
        <div class="ai-prov-cost">{{ $rupee($p['cost']) }}</div>
        <div class="ai-prov-meta">spent ({{ $range==='all'?'all time':($range==='month'?'this month':$range.' days') }}) · {{ number_format($p['calls']) }} calls</div>
        <p class="ai-prov-tip">💡 Set this provider's balance in Settings → AI Provider to track remaining credit here.</p>
        <div class="ai-prov-rows">
            <div><span>Input tokens</span><b>{{ number_format($p['in']) }}</b></div>
            <div><span>Output tokens</span><b>{{ number_format($p['out']) }}</b></div>
            <div><span>Images</span><b>{{ number_format($p['images']) }}</b></div>
            <div><span>Text model</span><b class="mono">{{ $p['model'] }}</b></div>
            <div><span>API key</span><b class="{{ $p['key_set'] ? 'ai-ok':'ai-no' }}">{{ $p['key_set'] ? 'set':'not set' }}</b></div>
        </div>
    </div>
    @endforeach
</div>

<div class="ai-panel">
    <h3>Daily spend (last 30 days)</h3>
    <div class="ai-chart">
        @foreach($daily as $d => $c)
        <div class="ai-bar" style="height:{{ max(2, round($c / $peak * 150)) }}px" title="{{ $d }}: {{ $dollar($c) }}"></div>
        @endforeach
    </div>
    <div class="ai-chart-foot">
        <span>{{ array_key_first($daily) }}</span>
        <span>peak {{ $dollar($peak) }}/day</span>
        <span>{{ array_key_last($daily) }}</span>
    </div>
</div>

<div class="ai-panel">
    <h3>Spend by model</h3>
    <table class="ai-tbl">
        <thead><tr><th>Provider</th><th>Model</th><th class="num">Calls</th><th class="num">In tokens</th><th class="num">Out tokens</th><th class="num">Images</th><th class="num">Cost</th></tr></thead>
        <tbody>
            @forelse($byModel as $r)
            <tr>
                <td class="prov">{{ \App\Services\AiProviderService::PROVIDERS[$r->provider]['label'] ?? ucfirst($r->provider) }}</td>
                <td class="mono">{{ $r->model ?? '—' }}</td>
                <td class="num">{{ number_format($r->calls) }}</td>
                <td class="num">{{ number_format($r->intok) }}</td>
                <td class="num">{{ number_format($r->outtok) }}</td>
                <td class="num">{{ number_format($r->images) }}</td>
                <td class="num cost"><b>{{ $rupee($r->cost) }}</b><small>{{ $dollar($r->cost) }}</small></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#8496ad;padding:24px">No AI usage recorded yet. The chatbot logs usage here automatically.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="ai-note"><i class="fas fa-circle-info"></i> Estimated from this app's own per-call usage, priced with list rates in <code>config/ai_pricing.php</code>. Google AI Studio has no public billing API; OpenAI &amp; Anthropic publish official cost APIs for cross-checking.</p>
@endsection
