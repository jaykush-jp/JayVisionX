@extends('admin.layouts.app')
@section('title', 'AI Configuration')
@section('content')
<div class="page-header-row">
    <h2>AI Configuration</h2>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary"><i class="fas fa-inbox"></i> Live Inbox</a>
</div>

<div class="crm-detail-grid">
    <!-- Settings form -->
    <div class="crm-col">
        <div class="admin-form-card">
            <form action="{{ route('admin.ai-settings.update') }}" method="POST">
                @csrf

                <div class="form-check-group" style="margin-bottom:16px">
                    <label class="form-check-label">
                        <input type="checkbox" name="is_enabled" value="1" {{ $settings->is_enabled ? 'checked' : '' }}>
                        <strong>Chatbot enabled</strong> — when off, visitors see the fallback message
                    </label>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Provider</label>
                        <input type="text" name="provider" class="form-control" value="{{ old('provider', $settings->provider ?? 'anthropic') }}" placeholder="anthropic">
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $settings->model) }}" placeholder="claude-haiku-5-5">
                        <small class="text-muted">API key is set server-side in <code>.env</code> (never here).</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Business description</label>
                    <textarea name="business_description" class="form-control" rows="2" placeholder="One-line description of the business for grounding.">{{ old('business_description', $settings->business_description) }}</textarea>
                </div>

                <div class="form-group">
                    <label>System prompt / persona</label>
                    <textarea name="system_prompt" class="form-control" rows="4" placeholder="You are the friendly AI assistant for ...">{{ old('system_prompt', $settings->system_prompt) }}</textarea>
                    <small class="text-muted">Leave blank to use the built-in grounded persona.</small>
                </div>

                <div class="form-group">
                    <label>Welcome message</label>
                    <textarea name="welcome_message" class="form-control" rows="2" placeholder="Hi! Welcome to JayVisionX 👋 ...">{{ old('welcome_message', $settings->welcome_message) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Suggested questions <small class="text-muted">(one per line)</small></label>
                    <textarea name="suggested_questions" class="form-control" rows="4" placeholder="What services do you offer?&#10;How much does a website cost?&#10;Request a custom quote">{{ old('suggested_questions', is_array($settings->suggested_questions) ? implode("\n", $settings->suggested_questions) : '') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Languages</label>
                    <div class="platform-picker">
                        @php $sel = is_array($settings->languages) ? $settings->languages : ['en','hi','hinglish']; @endphp
                        @foreach($languages as $key => $label)
                        <label class="platform-chip">
                            <input type="checkbox" name="languages[]" value="{{ $key }}" {{ in_array($key, $sel) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Response style</label>
                        <select name="response_style" class="form-control">
                            @foreach($styles as $key => $label)
                            <option value="{{ $key }}" {{ ($settings->response_style ?? 'friendly') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Max response tokens</label>
                        <input type="number" name="max_response_tokens" class="form-control" value="{{ old('max_response_tokens', $settings->max_response_tokens ?? 1024) }}" min="128" max="8192">
                    </div>
                </div>

                <div class="form-group">
                    <label>Fallback message <small class="text-muted">(when AI is off or unavailable)</small></label>
                    <textarea name="fallback_message" class="form-control" rows="2">{{ old('fallback_message', $settings->fallback_message) }}</textarea>
                </div>

                <h3 class="section-heading" style="margin-top:8px">Limits &amp; cost safeguards</h3>
                <div class="form-row-2">
                    <div class="form-group">
                        <label>Rate limit (messages / minute / visitor)</label>
                        <input type="number" name="rate_limit_per_minute" class="form-control" value="{{ old('rate_limit_per_minute', $settings->rate_limit_per_minute ?? 10) }}" min="1" max="120">
                    </div>
                    <div class="form-group">
                        <label>Daily message limit <small class="text-muted">(0 = unlimited)</small></label>
                        <input type="number" name="daily_message_limit" class="form-control" value="{{ old('daily_message_limit', $settings->daily_message_limit) }}" min="0">
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label>Confidence threshold <small class="text-muted">(0–1)</small></label>
                        <input type="number" step="0.01" name="confidence_threshold" class="form-control" value="{{ old('confidence_threshold', $settings->confidence_threshold) }}" min="0" max="1">
                    </div>
                    <div class="form-group">
                        <label>Monthly cost cap (₹)</label>
                        <input type="number" step="0.01" name="monthly_cost_cap" class="form-control" value="{{ old('monthly_cost_cap', $settings->monthly_cost_cap) }}" min="0">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Configuration</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Test chat -->
    <div class="crm-col">
        <div class="admin-form-card">
            <h3 class="section-heading">Test chat</h3>
            <p class="text-muted" style="margin-bottom:10px">Try the assistant with your current settings &amp; knowledge. Uses the live chatbot endpoint.</p>
            <div id="testMessages" class="test-chat-box"></div>
            <form id="testForm" class="chatbot-input" style="display:flex;gap:8px;margin-top:10px">
                <input type="text" id="testInput" class="form-control" placeholder="Ask something a customer might ask..." autocomplete="off" required>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
    var form = document.getElementById('testForm');
    var input = document.getElementById('testInput');
    var box = document.getElementById('testMessages');
    var token = null, busy = false;
    var csrf = '{{ csrf_token() }}';

    function add(text, who){
        var d = document.createElement('div');
        d.className = 'test-msg ' + who;
        d.textContent = text;
        box.appendChild(d);
        box.scrollTop = box.scrollHeight;
        return d;
    }

    form.addEventListener('submit', function(e){
        e.preventDefault();
        var msg = input.value.trim();
        if (!msg || busy) return;
        busy = true;
        add(msg, 'user');
        input.value = '';
        var typing = add('…', 'bot');
        fetch('{{ route('chatbot.reply') }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({ message: msg, session_token: token })
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            typing.remove();
            add(data.reply || 'No reply.', 'bot');
            if (data.session_token) token = data.session_token;
            if (data.mode) { var m=add('mode: '+data.mode, 'meta'); }
        })
        .catch(function(){ typing.remove(); add('Request failed.', 'bot'); })
        .finally(function(){ busy = false; });
    });
})();
</script>
@endsection
