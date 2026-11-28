@extends('admin.layouts.app')
@section('title', 'AI Configuration')
@section('content')
<style>
.ai-config .section-heading { display:flex; align-items:center; gap:9px; font-size:1rem; font-weight:700; color:#0f172a; padding-bottom:9px; border-bottom:2px solid #eef2f7; margin:24px 0 16px; }
.ai-config .section-heading i { color:#0a7ce0; }
.ai-config .admin-form-card { border-radius:16px; }
.ai-config label { font-weight:600; color:#334155; font-size:.86rem; }
.ai-config .form-control { border-radius:10px; }
.ai-subcard { background:#f8fafc; border:1px solid #eef2f7; border-radius:14px; padding:14px 18px 4px; margin-top:20px; }
.ai-subcard .section-heading { margin-top:4px; border-bottom-color:#e2e8f0; }
.ai-subcard--accent { background:linear-gradient(135deg,#eff6ff,#f6faff); border-color:#dbeafe; }
.ai-subcard--accent .section-heading i { color:#0a66c2; }
/* Provider connection card */
.ai-test-card { text-align:left; }
.ai-provider-row { display:flex; align-items:center; gap:12px; padding:12px 14px; background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; margin-bottom:12px; }
.ai-provider-ic { width:42px; height:42px; border-radius:11px; background:linear-gradient(145deg,#0a7ce0,#13b0ff); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.ai-provider-meta b { display:block; font-size:.95rem; color:#0f172a; }
.ai-provider-meta small { color:#64748b; font-size:.78rem; }
.ai-test-btn { width:100%; justify-content:center; }
.ai-test-result { margin-top:12px; padding:12px 14px; border-radius:11px; font-size:.86rem; display:none; align-items:flex-start; gap:9px; line-height:1.45; }
.ai-test-result.show { display:flex; }
.ai-test-result.ok { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
.ai-test-result.err { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
.ai-test-result.load { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
.ai-test-result i { margin-top:2px; }
.ai-config .lead-badge { font-size:.68rem; padding:2px 8px; border-radius:20px; vertical-align:middle; }
</style>
<div class="page-header-row">
    <h2><i class="fas fa-robot" style="color:#0a7ce0"></i> AI Configuration</h2>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary"><i class="fas fa-inbox"></i> Live Inbox</a>
</div>

<div class="crm-detail-grid ai-config">
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
                        <label>AI Provider</label>
                        <select name="provider" id="aiProvider" class="form-control">
                            @foreach($providers as $key => $def)
                            <option value="{{ $key }}"
                                data-model="{{ $def['default_model'] }}"
                                data-hint="{{ $def['key_hint'] }}"
                                data-label="{{ $def['label'] }}"
                                {{ old('provider', $settings->provider ?? 'anthropic') === $key ? 'selected' : '' }}>{{ $def['label'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pick which AI powers the chatbot, then enter its key below.</small>
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" name="model" id="aiModel" class="form-control" list="aiModelList" autocomplete="off" value="{{ old('model', $settings->model) }}" placeholder="{{ $providers[$settings->provider ?? 'anthropic']['default_model'] ?? '' }}">
                        <datalist id="aiModelList"></datalist>
                        <button type="button" id="aiLoadModels" style="margin-top:8px;display:inline-flex;align-items:center;gap:7px;white-space:nowrap;background:#eef4fc;color:#0a7ce0;border:1px solid #cfe2fa;border-radius:9px;padding:8px 15px;font-size:.82rem;font-weight:600;cursor:pointer;font-family:inherit;transition:.15s"><i class="fas fa-rotate"></i> Load available models</button>
                        <small class="text-muted" id="aiModelStatus" style="display:block;margin-top:7px">Load your account's models, or type any model name.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>API Key
                        @if($keyIsSet)<span class="lead-badge lead-badge-won">saved</span>@elseif($activeKeyOk)<span class="lead-badge lead-badge-qualified">from .env</span>@else<span class="lead-badge lead-badge-lost">not set</span>@endif
                    </label>
                    <input type="password" name="api_key" class="form-control" autocomplete="new-password" placeholder="{{ $keyIsSet ? '•••••••• (saved — leave blank to keep)' : 'Paste your API key' }}">
                    <small class="text-muted">
                        Stored encrypted, server-side only. Get a key from <code id="aiKeyHint">{{ $providers[$settings->provider ?? 'anthropic']['key_hint'] ?? '' }}</code>.
                        Switching provider with this blank clears the saved key.
                    </small>
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

                <div class="ai-subcard">
                <h3 class="section-heading" style="margin-top:0"><i class="fas fa-shield-halved"></i> Limits &amp; cost safeguards</h3>
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
                </div>

                <div class="ai-subcard ai-subcard--accent">
                <h3 class="section-heading" style="margin-top:0"><i class="fas fa-user-clock"></i> Lead auto follow-up (AI)</h3>
                <p class="text-muted" style="margin:-6px 0 14px;font-size:.85rem">Jab koi naya lead aata hai (jiska email ho), AI apne-aap warm follow-up emails bhejta rahega jab tak lead reply na kare ya Won/Lost na ho jaye. AI key na ho to friendly template emails jaati hain.</p>
                <div class="form-group form-check-group">
                    <label class="form-check-label">
                        <input type="checkbox" name="ai_followup_enabled" value="1" {{ ($followup['enabled'] ?? false) ? 'checked' : '' }}>
                        <span>Enable AI auto follow-up for new leads</span>
                    </label>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label>Max follow-ups per lead</label>
                        <input type="number" name="ai_followup_max" class="form-control" value="{{ old('ai_followup_max', $followup['max'] ?? 3) }}" min="1" max="10">
                    </div>
                    <div class="form-group">
                        <label>Gap between follow-ups <small class="text-muted">(hours)</small></label>
                        <input type="number" name="ai_followup_gap_hours" class="form-control" value="{{ old('ai_followup_gap_hours', $followup['gap_hours'] ?? 48) }}" min="1" max="720">
                    </div>
                </div>
                <div class="form-group">
                    <label>Follow-up instructions / tone <small class="text-muted">(optional)</small></label>
                    <textarea name="ai_followup_instructions" class="form-control" rows="2" placeholder="e.g. mention our ₹6,999 website offer, keep it friendly and short">{{ old('ai_followup_instructions', $followup['instructions'] ?? '') }}</textarea>
                </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Configuration</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Provider test + Test chat -->
    <div class="crm-col">
        <div class="admin-form-card ai-test-card">
            <h3 class="section-heading"><i class="fas fa-plug-circle-check"></i> Provider connection</h3>
            <div class="ai-provider-row">
                <span class="ai-provider-ic"><i class="fas fa-microchip"></i></span>
                <div class="ai-provider-meta">
                    <b id="aiProvName">{{ $providers[$settings->provider ?? 'anthropic']['label'] ?? '—' }}</b>
                    <small>{{ $keyIsSet ? 'API key saved' : 'No API key saved yet' }}</small>
                </div>
            </div>
            <button type="button" id="aiTestBtn" class="btn btn-primary ai-test-btn"><i class="fas fa-bolt"></i> Test connection</button>
            <div id="aiTestResult" class="ai-test-result"></div>
            <p class="text-muted" style="font-size:.76rem;margin:10px 0 0">Provider ya key badli? Pehle <strong>Save Configuration</strong> karo, phir test.</p>
        </div>

        <div class="admin-form-card">
            <h3 class="section-heading"><i class="fas fa-comments"></i> Test chat</h3>
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
    // Provider dropdown → update model placeholder + where-to-get-key hint
    var providerSel = document.getElementById('aiProvider');
    var modelInput = document.getElementById('aiModel');
    var keyHint = document.getElementById('aiKeyHint');
    var provName = document.getElementById('aiProvName');
    if (providerSel) {
        providerSel.addEventListener('change', function(){
            var opt = providerSel.options[providerSel.selectedIndex];
            if (modelInput) modelInput.placeholder = opt.getAttribute('data-model') || '';
            if (keyHint) keyHint.textContent = opt.getAttribute('data-hint') || '';
            if (provName) provName.textContent = opt.getAttribute('data-label') || '';
        });
    }
})();
(function(){
    // Test the selected AI provider's saved key with a tiny live request.
    var btn = document.getElementById('aiTestBtn');
    var out = document.getElementById('aiTestResult');
    var providerSel = document.getElementById('aiProvider');
    if (!btn) return;
    function esc(s){ var d = document.createElement('div'); d.textContent = (s == null ? '' : s); return d.innerHTML; }
    function show(cls, html){ out.className = 'ai-test-result show ' + cls; out.innerHTML = html; }
    btn.addEventListener('click', function(){
        var provider = providerSel ? providerSel.value : '';
        btn.disabled = true;
        show('load', '<i class="fas fa-spinner fa-spin"></i><span>Testing connection…</span>');
        fetch('{{ route('admin.ai-settings.test') }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'},
            body: JSON.stringify({ provider: provider })
        })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.ok) {
                show('ok', '<i class="fas fa-circle-check"></i><span><strong>Working ✓</strong><br>' + esc(d.provider) + ' · ' + esc(d.model) + '<br><em>Reply: ' + esc(d.reply) + '</em></span>');
            } else {
                show('err', '<i class="fas fa-circle-xmark"></i><span>' + esc(d.message || 'Test failed.') + '</span>');
            }
        })
        .catch(function(){ show('err', '<i class="fas fa-circle-xmark"></i><span>Request failed. Try again.</span>'); })
        .finally(function(){ btn.disabled = false; });
    });
})();
(function(){
    // Load the live list of models the saved key can use into the datalist.
    var btn = document.getElementById('aiLoadModels');
    var list = document.getElementById('aiModelList');
    var status = document.getElementById('aiModelStatus');
    var modelInput = document.getElementById('aiModel');
    if (!btn) return;
    btn.addEventListener('click', function(){
        btn.disabled = true;
        status.textContent = 'Loading models…';
        fetch('{{ route('admin.ai-settings.models') }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'},
            body:'{}'
        })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.ok && d.models && d.models.length) {
                list.innerHTML = '';
                d.models.forEach(function(m){ var o = document.createElement('option'); o.value = m; list.appendChild(o); });
                status.textContent = d.count + ' models loaded — click the Model box to pick one.';
                modelInput.focus();
            } else {
                status.textContent = d.message || 'Could not load models.';
            }
        })
        .catch(function(){ status.textContent = 'Request failed. Try again.'; })
        .finally(function(){ btn.disabled = false; });
    });
})();
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
