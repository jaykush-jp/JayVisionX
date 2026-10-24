@extends('admin.layouts.app')
@section('title', 'Quote Builder')
@section('content')
<div class="page-header-row">
    <h2>Quote Builder</h2>
    <a href="{{ route('admin.quotes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quote Requests</a>
</div>
<p class="muted-intro">Deterministic estimate built only from your active, approved packages — it never invents a number.</p>

<div class="crm-detail-grid">
    <div class="crm-col">
        <div class="admin-form-card">
            <form method="GET" action="{{ route('admin.quotes.build') }}">
                <div class="form-group">
                    <label>Project type</label>
                    <select name="project_type" class="form-control" onchange="this.form.submit()">
                        <option value="">— Select —</option>
                        @foreach($projectTypes as $k => $v)
                        <option value="{{ $k }}" {{ $selected===$k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-calculator"></i> Estimate</button>
            </form>

            @if($estimate)
            <div class="quote-estimate">
                @if($estimate['min'] !== null)
                <div class="quote-estimate-amount">
                    {{ $estimate['currency'] }} {{ number_format((float)$estimate['min']) }}
                    @if($estimate['max'] && $estimate['max'] != $estimate['min']) – {{ number_format((float)$estimate['max']) }} @endif
                </div>
                <div class="quote-estimate-note">Estimated range based on {{ count($estimate['basis']) }} matching package(s)</div>
                <ul class="quote-basis">
                    @foreach($estimate['basis'] as $b)<li>{{ $b }}</li>@endforeach
                </ul>
                @else
                <div class="quote-estimate-amount">Custom quote</div>
                <div class="quote-estimate-note">No matching priced package — pricing depends on requirements.</div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Active packages</h3>
            @forelse($packages as $p)
            <div class="social-post-item">
                <strong>{{ $p->service ? $p->service->title . ' — ' : '' }}{{ $p->name }}</strong>
                <span class="lead-badge lead-badge-qualified">{{ $p->price_label }}</span>
            </div>
            @empty
            <p class="text-muted">No active packages. Add them under Packages &amp; Pricing.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
