@extends('admin.layouts.app')
@section('title', 'Integrations')
@section('content')
<div class="page-header-row">
    <h2>Integrations</h2>
</div>
<p class="muted-intro">Connect the tools that power your marketing. Google Business Profile &amp; Meta need an approved developer app — once credentials are in <code>.env</code>, the Connect button runs the sign-in flow.</p>

@php
$brandSvg = [
    'google_analytics' => '<svg viewBox="0 0 24 24"><rect x="16" y="3" width="5" height="18" rx="2.5" fill="#F9AB00"/><rect x="9.5" y="9" width="5" height="12" rx="2.5" fill="#E37400"/><circle cx="5.5" cy="18.5" r="2.5" fill="#E37400"/></svg>',
    'google_tag_manager' => '<svg viewBox="0 0 24 24"><path fill="#8AB4F8" d="M12 2 2 12l10 10 4-4-6-6 6-6z"/><path fill="#4285F4" d="M12 2l10 10-10 10-4-4 6-6-6-6z"/><circle cx="12" cy="12" r="1.8" fill="#246FDB"/></svg>',
    'google_sheets' => '<svg viewBox="0 0 24 24"><rect x="4" y="3" width="16" height="18" rx="2" fill="#0F9D58"/><g fill="none" stroke="#fff" stroke-width="1.3" opacity=".95"><path d="M8 8.5h8M8 12h8M8 15.5h8M12 8.5v7"/></g></svg>',
    'anthropic' => '<svg viewBox="0 0 24 24"><rect x="4.5" y="7" width="15" height="11" rx="3.2" fill="#D97757"/><circle cx="9.5" cy="12.5" r="1.5" fill="#fff"/><circle cx="14.5" cy="12.5" r="1.5" fill="#fff"/><path d="M12 3.2v3.3" stroke="#D97757" stroke-width="2"/><circle cx="12" cy="3" r="1.5" fill="#D97757"/></svg>',
    'google' => '<svg viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.7-6.1 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34 7.1 29.3 5 24 5 13.5 5 5 13.5 5 24s8.5 19 19 19 19-8.5 19-19c0-1.3-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34 7.1 29.3 5 24 5 16.3 5 9.7 9.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 43c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 34.1 26.7 35 24 35c-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 38.6 16.2 43 24 43z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.1-4.1 5.6l6.2 5.2C41 36.3 43 30.7 43 24c0-1.3-.1-2.3-.4-3.5z"/></svg>',
    'meta' => '<svg viewBox="0 0 24 24"><path fill="#0866FF" d="M24 12a12 12 0 1 0-13.9 11.9v-8.4H7.1V12h3V9.4c0-3 1.8-4.6 4.5-4.6 1.3 0 2.6.2 2.6.2v2.9h-1.5c-1.5 0-1.9.9-1.9 1.8V12h3.3l-.5 3.5h-2.8v8.4A12 12 0 0 0 24 12z"/></svg>',
];
@endphp

<div class="integration-grid">
    @foreach($cards as $card)
    <div class="integration-card {{ ($card['connected'] ?? false) ? 'is-connected' : '' }}">
        <div class="integration-head">
            <span class="integration-icon">{!! $brandSvg[$card['key']] ?? '<i class="'.$card['icon'].' fa-fw"></i>' !!}</span>
            <div>
                <h3>{{ $card['name'] }}</h3>
                @if($card['connected'] ?? false)
                    <span class="lead-badge lead-badge-won">Connected</span>
                @elseif(($card['oauth'] ?? false) && !($card['configured'] ?? false))
                    <span class="lead-badge lead-badge-lost">Not configured</span>
                @else
                    <span class="lead-badge lead-badge-new">Not connected</span>
                @endif
            </div>
        </div>
        <p class="integration-detail">{{ $card['detail'] }}</p>
        <div class="integration-actions">
            @if($card['oauth'] ?? false)
                @if($card['connected'])
                    <form action="{{ route('admin.integrations.disconnect', $card['key']) }}" method="POST" onsubmit="return confirm('Disconnect {{ $card['name'] }}?')">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-secondary">Disconnect</button>
                    </form>
                @elseif($card['configured'])
                    <a href="{{ route('admin.integrations.connect', $card['key']) }}" class="btn btn-xs btn-primary"><i class="fas fa-link"></i> Connect</a>
                @else
                    <span class="btn btn-xs btn-disabled" title="Add credentials in .env first">Set up in .env</span>
                @endif
            @elseif(!empty($card['manage']))
                <a href="{{ $card['manage'] }}" class="btn btn-xs btn-secondary">Manage</a>
            @endif
        </div>
    </div>
    @endforeach
</div>

<div class="admin-form-card" style="margin-top:24px">
    <h3 class="section-heading">Setup notes</h3>
    <ul class="setup-notes">
        <li><strong>Google Business Profile</strong> — create a Google Cloud OAuth client, request Business Profile API access for your verified location, then set <code>GOOGLE_CLIENT_ID</code> / <code>GOOGLE_CLIENT_SECRET</code>. Redirect URI: <code>{{ route('admin.integrations.callback', 'google') }}</code></li>
        <li><strong>Meta (Facebook &amp; Instagram)</strong> — create a Meta app, complete Business Verification + App Review for publishing, link an IG Business account to your Page, then set <code>META_APP_ID</code> / <code>META_APP_SECRET</code>. Redirect URI: <code>{{ route('admin.integrations.callback', 'meta') }}</code></li>
        <li><strong>Google Ads conversions</strong> — add your GTM container ID in Website Settings, then create the conversion tag inside Google Tag Manager.</li>
    </ul>

    <h3 class="section-heading" style="margin-top:20px">Live lead webhooks</h3>
    <ul class="setup-notes">
        <li><strong>Meta Lead Ads (Facebook &amp; Instagram)</strong> — in your Meta app, subscribe the Page to the <code>leadgen</code> webhook with callback URL <code>{{ route('webhooks.meta.verify') }}</code> and the verify token from <code>META_LEAD_VERIFY_TOKEN</code>. New lead-form submissions then appear in Leads tagged <em>Meta</em>.</li>
        <li><strong>Google Ads lead forms</strong> — in your lead-form extension, set the webhook URL to <code>{{ route('webhooks.google.leads') }}</code> and a key matching <code>GOOGLE_LEAD_WEBHOOK_KEY</code>. New leads appear tagged <em>Google</em>.</li>
    </ul>
</div>
@endsection
