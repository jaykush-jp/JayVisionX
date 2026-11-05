@extends('admin.layouts.app')
@section('title','API Credentials')
@section('content')
<div class="page-header-row">
    <h2>API Credentials</h2>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-secondary"><i class="fas fa-plug"></i> Integrations</a>
</div>

<div class="admin-form-card">
    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <p style="color:#6B7FA3;margin-bottom:8px">
        Saari third-party API keys yahin se manage karein — AI providers, Google aur Meta.
        Values <strong>encrypted</strong> store hoti hain (database me, <code>.env</code> me nahi).
    </p>
    <p style="color:#6B7FA3;margin-bottom:20px;font-size:13px">
        Secret fields par <em>"saved — leave blank to keep"</em> dikhega: blank chhodne par purani value rahegi.
        Kisi key ko hatane ke liye uske saath <strong>Clear</strong> checkbox tick karein. Active AI provider aur model
        <a href="{{ route('admin.ai-settings.index') }}">AI Configuration</a> me chunein.
    </p>

    <form action="{{ route('admin.credentials.update') }}" method="POST">
        @csrf

        @foreach($groups as $group => $fields)
        <h3 style="margin:22px 0 12px;font-size:16px;color:#0A1628;border-bottom:1px solid #E6ECF5;padding-bottom:8px">{{ $group }}</h3>

        @foreach($fields as $key => $f)
        <div class="form-group" style="margin-bottom:16px">
            <label style="display:flex;justify-content:space-between;align-items:center">
                <span>
                    {{ $f['label'] }}
                    @if($f['isSet'])<span class="badge badge-yes" style="margin-left:6px">SAVED</span>@endif
                </span>
                <label style="font-weight:400;font-size:12px;color:#B4342A;display:inline-flex;align-items:center;gap:5px;cursor:pointer">
                    <input type="checkbox" name="clear[{{ $key }}]" value="1" @if(!$f['isSet']) disabled @endif> Clear
                </label>
            </label>

            @if($f['secret'])
            <input type="password" name="cred[{{ $key }}]" class="form-control" autocomplete="new-password"
                   placeholder="{{ $f['isSet'] ? '•••••••• (saved — leave blank to keep)' : 'Enter key / token' }}">
            @else
            <input type="text" name="cred[{{ $key }}]" class="form-control"
                   value="{{ old('cred.'.$key, $f['value']) }}" placeholder="{{ $f['hint'] ?? '' }}">
            @endif

            @if(!empty($f['hint']) && $f['secret'])
            <small class="text-muted">{{ $f['hint'] }}</small>
            @endif
        </div>
        @endforeach
        @endforeach

        <div class="form-actions" style="margin-top:24px">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Credentials</button>
        </div>
    </form>
</div>

<div class="admin-form-card" style="margin-top:18px">
    <p style="color:#6B7FA3;margin:0;font-size:13px">
        <i class="fas fa-shield-alt"></i> Encryption <code>APP_KEY</code> par depend karti hai. Agar <code>APP_KEY</code>
        kabhi change / rotate ho, to saari saved credentials dobara enter karni hongi.
    </p>
</div>
@endsection
