@extends('admin.layouts.app')
@section('title', 'Social Posts')

@section('head_extra')
<style>
/* ===== Social Posts studio ===== */
.sp-layout { display:grid; grid-template-columns: 1.15fr 0.85fr; gap:22px; align-items:start; }
.sp-card { background:#fff; border:1px solid #e6ecf5; border-radius:16px; padding:22px; }
.sp-card h3 { font-size:1.05rem; margin:0 0 16px; color:#0a1628; }
.sp-field { margin-bottom:16px; }
.sp-field > label { display:block; font-weight:600; font-size:.82rem; color:#44566d; margin-bottom:6px; letter-spacing:.02em; text-transform:uppercase; }
.sp-input, .sp-textarea { width:100%; border:1px solid #d9e2ef; border-radius:10px; padding:11px 13px; font-family:inherit; font-size:.92rem; transition:.15s; background:#fbfdff; }
.sp-input:focus, .sp-textarea:focus { outline:none; border-color:var(--primary,#0a7ce0); box-shadow:0 0 0 3px rgba(10,124,224,.12); background:#fff; }
.sp-textarea { resize:vertical; min-height:120px; line-height:1.55; }
.sp-with-icon { position:relative; }
.sp-with-icon i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#9fb0c7; }
.sp-with-icon .sp-input { padding-left:36px; }
/* location autocomplete */
.sp-loc-wrap { position:relative; }
.sp-geo-btn { position:absolute; right:6px; top:50%; transform:translateY(-50%); width:30px; height:30px; border:none; border-radius:8px; background:#eef3fa; color:var(--primary,#0a7ce0); cursor:pointer; display:flex; align-items:center; justify-content:center; }
.sp-geo-btn:hover { background:var(--primary,#0a7ce0); color:#fff; }
.sp-geo-btn.loading i { animation:spSpin .8s linear infinite; }
@keyframes spSpin { to { transform:rotate(360deg); } }
.sp-with-icon .sp-input { padding-right:42px; }
.sp-loc-list { position:absolute; left:0; right:0; top:calc(100% + 4px); z-index:20; margin:0; padding:6px; list-style:none; background:#fff; border:1px solid #e1e8f3; border-radius:10px; box-shadow:0 12px 30px rgba(10,22,40,.14); max-height:230px; overflow:auto; display:none; }
.sp-loc-list.show { display:block; }
.sp-loc-list li { padding:9px 11px; border-radius:7px; font-size:.84rem; color:#334; cursor:pointer; display:flex; gap:9px; align-items:flex-start; }
.sp-loc-list li:hover, .sp-loc-list li.active { background:#f0f6ff; }
.sp-loc-list li i { color:var(--primary,#0a7ce0); margin-top:2px; flex-shrink:0; }
.sp-row { display:flex; justify-content:space-between; align-items:center; margin-top:5px; }
.sp-hint { font-size:.76rem; color:#8496ad; }
.sp-count { font-size:.76rem; color:#8496ad; font-variant-numeric:tabular-nums; }

/* image dropzone */
.sp-drop { border:2px dashed #cfdbec; border-radius:12px; padding:18px; text-align:center; cursor:pointer; transition:.15s; background:#fbfdff; position:relative; }
.sp-drop:hover { border-color:var(--primary,#0a7ce0); background:#f5faff; }
.sp-drop i { font-size:1.4rem; color:#9fb0c7; }
.sp-drop p { margin:6px 0 0; font-size:.85rem; color:#6b7f9a; }
.sp-drop input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; }
.sp-thumb { margin-top:10px; display:none; }
.sp-thumb img { max-height:120px; border-radius:10px; border:1px solid #e6ecf5; }
.sp-thumb.show { display:block; }

/* platform picker */
.sp-platforms { display:flex; flex-wrap:wrap; gap:10px; }
.sp-plat { flex:1; min-width:120px; display:flex; align-items:center; gap:10px; border:1.5px solid #e1e8f3; border-radius:12px; padding:11px 13px; cursor:pointer; transition:.15s; background:#fff; }
.sp-plat input { display:none; }
.sp-plat .sp-plat-ic { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1rem; flex-shrink:0; }
.sp-plat[data-p=instagram] .sp-plat-ic { background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
.sp-plat[data-p=facebook] .sp-plat-ic { background:#1877f2; }
.sp-plat[data-p=google] .sp-plat-ic { background:#0f9d58; }
.sp-plat b { font-size:.86rem; color:#223; display:block; }
.sp-plat small { font-size:.7rem; }
.sp-plat .sp-dot { margin-left:auto; width:9px; height:9px; border-radius:50%; background:#cbd5e1; flex-shrink:0; }
.sp-plat.connected .sp-dot { background:#22c55e; }
.sp-plat.checked { border-color:var(--primary,#0a7ce0); box-shadow:0 0 0 3px rgba(10,124,224,.1); background:#f5faff; }

.sp-actions { display:flex; flex-wrap:wrap; gap:10px; margin-top:6px; }
.sp-btn { border:none; border-radius:10px; padding:11px 18px; font-weight:600; font-size:.88rem; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
.sp-btn-primary { background:linear-gradient(135deg,var(--primary,#0a7ce0),var(--accent,#15c6ff)); color:#fff; }
.sp-btn-primary:hover { filter:brightness(1.05); transform:translateY(-1px); }
.sp-btn-ghost { background:#eef3fa; color:#44566d; }
.sp-btn-ghost:hover { background:#e2eaf5; }

/* ===== Live preview ===== */
.sp-preview-wrap { position:sticky; top:16px; }
.sp-pv-tabs { display:flex; gap:6px; margin-bottom:12px; background:#eef3fa; padding:5px; border-radius:12px; }
.sp-pv-tab { flex:1; border:none; background:transparent; padding:8px; border-radius:9px; font-weight:600; font-size:.82rem; color:#64748b; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:.15s; }
.sp-pv-tab.active { background:#fff; color:#0a1628; box-shadow:0 2px 8px rgba(10,22,40,.08); }
.sp-pv-tab[data-pv=instagram].active { color:#dc2743; }
.sp-pv-tab[data-pv=facebook].active { color:#1877f2; }
.sp-pv-tab[data-pv=google].active { color:#0f9d58; }

.sp-phone { background:#fff; border:1px solid #e6ecf5; border-radius:16px; overflow:hidden; box-shadow:0 12px 40px rgba(10,22,40,.12); max-width:390px; margin:0 auto; }
.sp-pv-head { display:flex; align-items:center; gap:10px; padding:12px 14px; }
.sp-pv-avatar { width:38px; height:38px; border-radius:50%; object-fit:cover; background:linear-gradient(135deg,var(--primary,#0a7ce0),var(--accent,#15c6ff)); flex-shrink:0; }
.sp-pv-meta b { font-size:.85rem; color:#111; display:block; line-height:1.2; }
.sp-pv-meta span { font-size:.72rem; color:#8e8e8e; }
.sp-pv-head .fa-ellipsis-h { margin-left:auto; color:#333; }
.sp-pv-media { position:relative; width:100%; aspect-ratio:1/1; background:#f0f3f8 center/cover no-repeat; display:flex; align-items:center; justify-content:center; color:#aab6c7; }
.sp-pv-media .sp-pv-ph { text-align:center; }
.sp-pv-media .sp-pv-ph i { font-size:2rem; }
.sp-pv-media .sp-pv-ph p { font-size:.78rem; margin:6px 0 0; }
.sp-phone.has-img .sp-pv-ph { display:none; }

/* instagram chrome */
.sp-ig-actions { display:flex; align-items:center; gap:16px; padding:12px 14px 6px; font-size:1.25rem; color:#222; }
.sp-ig-actions .fa-bookmark { margin-left:auto; }
.sp-ig-likes { padding:0 14px; font-size:.82rem; font-weight:600; color:#111; }
.sp-pv-caption { padding:6px 14px 14px; font-size:.84rem; color:#222; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
.sp-pv-caption b { font-weight:600; margin-right:5px; }
.sp-pv-tags { color:#00376b; }

/* facebook chrome */
.sp-phone .sp-fb-only, .sp-phone .sp-ig-only, .sp-phone .sp-g-only { display:none; }
.sp-phone--instagram .sp-ig-only { display:block; }
.sp-phone--instagram .sp-ig-actions { display:flex; }
.sp-phone--facebook .sp-fb-only { display:block; }
.sp-phone--google .sp-g-only { display:block; }
.sp-fb-text { padding:4px 14px 12px; font-size:.9rem; color:#1c1e21; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
.sp-fb-bar { display:flex; border-top:1px solid #eceef1; }
.sp-fb-bar span { flex:1; text-align:center; padding:9px; font-size:.82rem; color:#65676b; font-weight:600; }
.sp-fb-head span { font-size:.72rem; color:#65676b; }
.sp-g-text { padding:4px 14px 14px; font-size:.88rem; color:#202124; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
.sp-g-cta { margin:0 14px 14px; border:1px solid #dadce0; border-radius:8px; padding:9px; text-align:center; font-size:.82rem; font-weight:600; color:#1a73e8; }

/* ===== History ===== */
.sp-history { margin-top:22px; }
.sp-posts { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; }
.sp-post { border:1px solid #e6ecf5; border-radius:14px; overflow:hidden; background:#fff; display:flex; flex-direction:column; }
.sp-post-img { aspect-ratio:16/10; background:#eef2f8 center/cover no-repeat; display:flex; align-items:center; justify-content:center; color:#aeb9ca; }
.sp-post-body { padding:14px; flex:1; display:flex; flex-direction:column; gap:8px; }
.sp-post-plats { display:flex; gap:6px; }
.sp-post-plats i { width:24px; height:24px; border-radius:6px; color:#fff; display:flex; align-items:center; justify-content:center; font-size:.72rem; }
.sp-post-plats .p-instagram { background:linear-gradient(45deg,#e6683c,#bc1888); }
.sp-post-plats .p-facebook { background:#1877f2; }
.sp-post-plats .p-google { background:#0f9d58; }
.sp-post p { margin:0; font-size:.86rem; color:#44566d; line-height:1.5; }
.sp-post-foot { display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:6px; }
.sp-status { font-size:.72rem; font-weight:700; padding:3px 10px; border-radius:20px; text-transform:capitalize; }
.sp-status.draft { background:#eef2f7; color:#64748b; }
.sp-status.scheduled { background:#fff3e0; color:#e67e22; }
.sp-status.published { background:#e7f7ed; color:#1a9f54; }
.sp-status.failed { background:#fdecea; color:#c0392b; }
.sp-post-acts { display:flex; gap:6px; }
.sp-ia { width:30px; height:30px; border:none; border-radius:8px; cursor:pointer; font-size:.8rem; }
.sp-ia.pub { background:#e7f2ff; color:#0a7ce0; }
.sp-ia.del { background:#fdecea; color:#c0392b; }

@media (max-width: 1000px) { .sp-layout { grid-template-columns:1fr; } .sp-preview-wrap { position:static; } }
</style>
@endsection

@section('content')
@php
    $siteName = \App\Models\Setting::get('site_name', 'JayVisionX');
    $logo = \App\Models\Setting::get('site_logo') ? asset('storage/'.\App\Models\Setting::get('site_logo')) : asset('images/logo.png');
    $igHandle = strtolower(str_replace(' ', '', $siteName));
@endphp
<div class="page-header-row">
    <h2>Social Posts</h2>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-secondary"><i class="fas fa-plug"></i> Integrations</a>
</div>
@if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="sp-layout">
    <!-- Composer -->
    <form class="sp-card" id="spForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <h3><i class="fas fa-pen-fancy"></i> Compose Post</h3>

        <div class="sp-field">
            <label>Content *</label>
            <textarea id="spBody" name="body" class="sp-textarea" maxlength="2200" placeholder="What do you want to share?" required>{{ old('body') }}</textarea>
            <div class="sp-row"><span class="sp-hint">Write your caption here.</span><span class="sp-count"><span id="spCount">0</span>/2200</span></div>
        </div>

        <div class="sp-field">
            <label><i class="fas fa-hashtag"></i> Hashtags</label>
            <input type="text" id="spHashtags" name="hashtags" class="sp-input" maxlength="500" value="{{ old('hashtags') }}" placeholder="#digitalmarketing #seo #delhi">
            <span class="sp-hint">Space separated — # auto-added if missing.</span>
        </div>

        <div class="sp-field">
            <label><i class="fas fa-location-dot"></i> Location</label>
            <div class="sp-loc-wrap">
                <div class="sp-with-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="spLocation" name="location" class="sp-input" maxlength="120" autocomplete="off" value="{{ old('location') }}" placeholder="Search a place…">
                    <button type="button" id="spGeo" class="sp-geo-btn" title="Use my current location"><i class="fas fa-crosshairs"></i></button>
                </div>
                <ul class="sp-loc-list" id="spLocList" role="listbox"></ul>
            </div>
        </div>

        <div class="sp-field">
            <label>Image</label>
            <div class="sp-drop">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click to upload an image <small>(Instagram requires one)</small></p>
                <input type="file" id="spImage" name="image" accept="image/*">
            </div>
            <div class="sp-thumb" id="spThumb"><img id="spThumbImg" alt="preview"></div>
        </div>

        <div class="sp-field">
            <label>Publish to</label>
            <div class="sp-platforms">
                @foreach(\App\Models\SocialPost::PLATFORMS as $key => $label)
                @php $conn = $connections[$key] ?? false; @endphp
                <label class="sp-plat {{ $conn ? 'connected' : '' }}" data-p="{{ $key }}">
                    <input type="checkbox" name="platforms[]" value="{{ $key }}">
                    <span class="sp-plat-ic"><i class="fab fa-{{ $key === 'google' ? 'google' : $key }}"></i></span>
                    <span><b>{{ $key === 'google' ? 'Google' : ucfirst($key) }}</b><small class="sp-hint">{{ $conn ? 'Connected' : 'Not connected' }}</small></span>
                    <span class="sp-dot" title="{{ $conn ? 'Connected' : 'Not connected' }}"></span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="sp-field">
            <label><i class="fas fa-clock"></i> Schedule for (optional)</label>
            <input type="datetime-local" name="scheduled_at" class="sp-input">
        </div>

        <div class="sp-actions">
            <button type="submit" name="action" value="publish" class="sp-btn sp-btn-primary"><i class="fas fa-paper-plane"></i> Publish now</button>
            <button type="submit" name="action" value="schedule" class="sp-btn sp-btn-ghost"><i class="fas fa-clock"></i> Schedule</button>
            <button type="submit" name="action" value="draft" class="sp-btn sp-btn-ghost"><i class="fas fa-save"></i> Save draft</button>
        </div>
    </form>

    <!-- Live preview -->
    <div class="sp-preview-wrap">
        <div class="sp-pv-tabs">
            <button type="button" class="sp-pv-tab active" data-pv="instagram"><i class="fab fa-instagram"></i> Instagram</button>
            <button type="button" class="sp-pv-tab" data-pv="facebook"><i class="fab fa-facebook"></i> Facebook</button>
            <button type="button" class="sp-pv-tab" data-pv="google"><i class="fab fa-google"></i> Google</button>
        </div>

        <div class="sp-phone sp-phone--instagram" id="spPhone">
            <div class="sp-pv-head">
                <img src="{{ $logo }}" class="sp-pv-avatar" alt="">
                <div class="sp-pv-meta">
                    <b id="spPvName">{{ $igHandle }}</b>
                    <span id="spPvLoc" class="sp-ig-only" style="display:none"></span>
                    <span class="sp-fb-only">Just now · <i class="fas fa-globe-americas"></i></span>
                </div>
                <i class="fas fa-ellipsis-h"></i>
            </div>

            <div class="sp-fb-text sp-fb-only" id="spPvFbText"></div>

            <div class="sp-pv-media" id="spPvMedia">
                <div class="sp-pv-ph"><i class="fas fa-image"></i><p>Image preview</p></div>
            </div>

            <!-- Instagram -->
            <div class="sp-ig-actions sp-ig-only">
                <i class="far fa-heart"></i><i class="far fa-comment"></i><i class="far fa-paper-plane"></i><i class="far fa-bookmark"></i>
            </div>
            <div class="sp-ig-likes sp-ig-only">Liked by <b>jayvisionx</b> and others</div>
            <div class="sp-pv-caption sp-ig-only"><b id="spPvIgName">{{ $igHandle }}</b><span id="spPvIgCap"></span></div>

            <!-- Facebook -->
            <div class="sp-fb-bar sp-fb-only"><span><i class="far fa-thumbs-up"></i> Like</span><span><i class="far fa-comment"></i> Comment</span><span><i class="fas fa-share"></i> Share</span></div>

            <!-- Google -->
            <div class="sp-g-text sp-g-only" id="spPvGText"></div>
            <div class="sp-g-cta sp-g-only">Learn more</div>
        </div>
    </div>
</div>

<!-- Recent / Published posts -->
<div class="sp-card sp-history">
    <h3><i class="fas fa-stream"></i> Your Posts</h3>
    <div class="sp-posts">
        @forelse($posts as $p)
        <div class="sp-post">
            <div class="sp-post-img" @if($p->image) style="background-image:url('{{ asset('storage/'.$p->image) }}')" @endif>
                @unless($p->image)<i class="fas fa-align-left"></i>@endunless
            </div>
            <div class="sp-post-body">
                <div class="sp-post-plats">
                    @foreach($p->platforms ?? [] as $pl)<i class="p-{{ $pl }} fab fa-{{ $pl === 'google' ? 'google' : $pl }}"></i>@endforeach
                </div>
                <p>{{ Str::limit($p->body, 120) }}</p>
                @if($p->hashtags)<p style="color:#0a7ce0;font-size:.8rem">{{ Str::limit($p->hashtags, 80) }}</p>@endif
                @if($p->location)<p style="font-size:.76rem"><i class="fas fa-map-marker-alt"></i> {{ $p->location }}</p>@endif
                @if($p->scheduled_at)<p style="font-size:.76rem"><i class="fas fa-clock"></i> {{ $p->scheduled_at->format('d M Y, h:i A') }}</p>@endif
                @if($p->error)<p style="color:#c0392b;font-size:.76rem">{{ $p->error }}</p>@endif
                <div class="sp-post-foot">
                    <span class="sp-status {{ $p->status }}">{{ $p->status }}</span>
                    <div class="sp-post-acts">
                        @if(in_array($p->status, ['draft','scheduled','failed']))
                        <form action="{{ route('admin.posts.publish', $p->id) }}" method="POST">@csrf<button class="sp-ia pub" title="Publish now"><i class="fas fa-paper-plane"></i></button></form>
                        @endif
                        <form action="{{ route('admin.posts.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this post?')">@csrf @method('DELETE')<button class="sp-ia del" title="Delete"><i class="fas fa-trash"></i></button></form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted" style="grid-column:1/-1;text-align:center;padding:30px">No posts yet — compose your first post above. 🚀</p>
        @endforelse
    </div>
    <div class="pagination-wrapper" style="margin-top:16px">{{ $posts->links('admin.partials.pagination') }}</div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var body = document.getElementById('spBody'),
        tags = document.getElementById('spHashtags'),
        loc  = document.getElementById('spLocation'),
        img  = document.getElementById('spImage'),
        phone = document.getElementById('spPhone'),
        media = document.getElementById('spPvMedia');

    function render() {
        var text = body.value || 'Your caption will appear here…';
        var tg = (tags.value || '').trim();
        var cap = text + (tg ? '\n' : '');
        // Instagram caption
        document.getElementById('spPvIgCap').textContent = ' ' + text;
        var igTags = document.querySelector('.sp-pv-caption');
        // Facebook / Google text
        var fullFb = text + (tg ? '\n\n' + tg : '');
        document.getElementById('spPvFbText').textContent = fullFb;
        document.getElementById('spPvGText').textContent = fullFb;
        // hashtags appended (IG)
        var igCap = document.getElementById('spPvIgCap');
        igCap.textContent = ' ' + text + (tg ? '  ' : '');
        if (tg) {
            var tagSpan = document.createElement('span');
            tagSpan.className = 'sp-pv-tags';
            tagSpan.textContent = ' ' + tg;
            igCap.appendChild(tagSpan);
        }
        // location
        var locEl = document.getElementById('spPvLoc');
        if (loc.value.trim()) { locEl.textContent = loc.value.trim(); locEl.style.display = 'block'; }
        else { locEl.style.display = 'none'; }
        document.getElementById('spCount').textContent = body.value.length;
    }
    [body, tags, loc].forEach(function (el) { el && el.addEventListener('input', render); });

    // image preview
    if (img) img.addEventListener('change', function () {
        var f = this.files && this.files[0];
        var thumb = document.getElementById('spThumb'), timg = document.getElementById('spThumbImg');
        if (f) {
            var r = new FileReader();
            r.onload = function (e) {
                media.style.backgroundImage = 'url(' + e.target.result + ')';
                phone.classList.add('has-img');
                timg.src = e.target.result; thumb.classList.add('show');
            };
            r.readAsDataURL(f);
        } else { media.style.backgroundImage = ''; phone.classList.remove('has-img'); thumb.classList.remove('show'); }
    });

    // platform checkbox highlight
    document.querySelectorAll('.sp-plat input').forEach(function (cb) {
        cb.addEventListener('change', function () { this.closest('.sp-plat').classList.toggle('checked', this.checked); });
    });

    // preview tabs
    document.querySelectorAll('.sp-pv-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.sp-pv-tab').forEach(function (t) { t.classList.remove('active'); });
            this.classList.add('active');
            phone.className = 'sp-phone sp-phone--' + this.dataset.pv;
            if (media.style.backgroundImage) phone.classList.add('has-img');
        });
    });

    // ---- Location autocomplete (OpenStreetMap Nominatim — free, no key) ----
    var locList = document.getElementById('spLocList'),
        geoBtn  = document.getElementById('spGeo'),
        locTimer, locData = [];

    function shortName(d) {
        var a = d.address || {};
        var city = a.city || a.town || a.village || a.suburb || a.county || '';
        var parts = [city, a.state, a.country].filter(Boolean);
        return parts.length ? parts.join(', ') : (d.display_name || '').split(',').slice(0, 3).join(',').trim();
    }
    function closeLoc() { locList.classList.remove('show'); locList.innerHTML = ''; }

    if (loc) loc.addEventListener('input', function () {
        var q = this.value.trim();
        clearTimeout(locTimer);
        render();
        if (q.length < 3) { closeLoc(); return; }
        locTimer = setTimeout(function () {
            fetch('https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=6&q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    locData = data || [];
                    if (!locData.length) { closeLoc(); return; }
                    locList.innerHTML = locData.map(function (d) {
                        return '<li><i class="fas fa-location-dot"></i><span>' + d.display_name + '</span></li>';
                    }).join('');
                    locList.classList.add('show');
                    [].forEach.call(locList.children, function (li, i) {
                        li.addEventListener('click', function () { loc.value = shortName(locData[i]); closeLoc(); render(); });
                    });
                }).catch(function () { closeLoc(); });
        }, 400);
    });
    document.addEventListener('click', function (e) { if (loc && e.target !== loc && !locList.contains(e.target)) closeLoc(); });

    // Current location
    if (geoBtn) geoBtn.addEventListener('click', function () {
        if (!navigator.geolocation) { alert('Geolocation not supported in this browser.'); return; }
        geoBtn.classList.add('loading');
        navigator.geolocation.getCurrentPosition(function (pos) {
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude)
                .then(function (r) { return r.json(); })
                .then(function (d) { loc.value = shortName(d); render(); })
                .catch(function () {})
                .finally(function () { geoBtn.classList.remove('loading'); });
        }, function () { geoBtn.classList.remove('loading'); alert('Could not get your location — please allow location access.'); }, { timeout: 8000 });
    });

    render();
})();
</script>
@endsection
