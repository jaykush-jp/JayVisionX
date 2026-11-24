@extends('frontend.layouts.app')
@section('meta_title', $page->meta_title ?? 'Our Work — Digital Solutions Showcase | JayVisionX')
@section('meta_description', $page->meta_description ?? 'See how JayVisionX turns business challenges into digital solutions — automation, websites, web apps and marketing integrations.')

@section('head_extra')
<style>
/* ===== Our Work — Digital Solutions Showcase ===== */
.ow-hero { position:relative; overflow:hidden; background:radial-gradient(1100px 500px at 80% -10%, rgba(21,198,255,.22), transparent 60%), radial-gradient(900px 500px at 0% 110%, rgba(10,124,224,.25), transparent 55%), linear-gradient(135deg, var(--dark,#0a1628), var(--dark-2,#0f1e38)); color:#fff; padding:120px 0 96px; }
.ow-hero::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size:46px 46px; mask-image:radial-gradient(600px 400px at 50% 30%, #000, transparent 75%); }
.ow-hero .container { position:relative; z-index:2; text-align:center; }
.ow-eyebrow { display:inline-block; font-size:.78rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--accent,#15c6ff); background:rgba(21,198,255,.12); border:1px solid rgba(21,198,255,.3); padding:7px 16px; border-radius:30px; margin-bottom:22px; }
.ow-hero h1 { font-size:clamp(2rem,4.6vw,3.4rem); line-height:1.12; max-width:900px; margin:0 auto 20px; letter-spacing:-.02em; color:#fff; }
.ow-hero h1 .grad { background:linear-gradient(120deg,var(--primary-light,#3da2f3),var(--accent,#15c6ff)); -webkit-background-clip:text; background-clip:text; color:transparent; }
.ow-hero p { font-size:1.12rem; color:#c8d3e8; max-width:680px; margin:0 auto 34px; line-height:1.7; }
.ow-btn { display:inline-flex; align-items:center; gap:10px; border-radius:40px; padding:15px 30px; font-weight:700; font-size:.98rem; text-decoration:none; transition:.25s; }
.ow-btn-primary { background:linear-gradient(135deg,var(--primary,#0a7ce0),var(--accent,#15c6ff)); color:#fff; box-shadow:0 14px 34px rgba(10,124,224,.4); }
.ow-btn-primary:hover { transform:translateY(-3px); box-shadow:0 20px 44px rgba(10,124,224,.5); }
.ow-btn-ghost { background:rgba(255,255,255,.08); color:#fff; border:1px solid rgba(255,255,255,.18); }
.ow-btn-ghost:hover { background:rgba(255,255,255,.16); }

.ow-section { padding:90px 0; }
.ow-head { text-align:center; max-width:720px; margin:0 auto 54px; }
.ow-head .ow-eyebrow { color:var(--primary,#0a7ce0); background:rgba(10,124,224,.08); border-color:rgba(10,124,224,.2); }
.ow-head h2 { font-size:clamp(1.7rem,3.4vw,2.5rem); letter-spacing:-.02em; margin-bottom:12px; }
.ow-head p { color:var(--text-muted,#64748b); font-size:1.05rem; line-height:1.7; }

/* 3D visual tile */
.ow-visual { width:70px; height:70px; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:1.7rem; color:#fff; position:relative; flex-shrink:0; background:linear-gradient(145deg,var(--primary,#0a7ce0),var(--accent,#15c6ff)); box-shadow:0 14px 30px rgba(10,124,224,.32), inset 0 2px 3px rgba(255,255,255,.45), inset 0 -5px 10px rgba(0,0,0,.18); }
.ow-visual::after { content:''; position:absolute; inset:4px 4px 48% 4px; border-radius:16px 16px 42px 42px; background:linear-gradient(to bottom,rgba(255,255,255,.4),transparent); pointer-events:none; }
.ow-visual i { position:relative; z-index:2; filter:drop-shadow(0 2px 3px rgba(0,0,0,.25)); }
.ow-visual.v2 { background:linear-gradient(145deg,#6a5cff,#15c6ff); box-shadow:0 14px 30px rgba(106,92,255,.32), inset 0 2px 3px rgba(255,255,255,.45), inset 0 -5px 10px rgba(0,0,0,.18); }
.ow-visual.v3 { background:linear-gradient(145deg,#0a5baa,#3da2f3); box-shadow:0 14px 30px rgba(10,91,170,.32), inset 0 2px 3px rgba(255,255,255,.4), inset 0 -5px 10px rgba(0,0,0,.18); }
.ow-visual.v4 { background:linear-gradient(145deg,#15c6ff,#0fa6db); box-shadow:0 14px 30px rgba(21,198,255,.32), inset 0 2px 3px rgba(255,255,255,.45), inset 0 -5px 10px rgba(0,0,0,.18); }
.ow-visual.v5 { background:linear-gradient(145deg,#1a9f54,#27c06a); box-shadow:0 14px 30px rgba(26,159,84,.32), inset 0 2px 3px rgba(255,255,255,.4), inset 0 -5px 10px rgba(0,0,0,.18); }

/* What we build */
.ow-build { display:grid; grid-template-columns:repeat(2,1fr); gap:24px; }
.ow-build-card { background:#fff; border:1px solid var(--border,#e6ecf5); border-radius:20px; padding:30px; display:flex; gap:22px; transition:.25s; }
.ow-build-card:hover { transform:translateY(-6px); box-shadow:0 24px 50px rgba(10,22,40,.1); border-color:transparent; }
.ow-build-card h3 { font-size:1.2rem; margin:4px 0 8px; }
.ow-build-card p { color:var(--text-muted,#64748b); font-size:.94rem; line-height:1.65; margin:0; }

/* Featured work stories */
.ow-stories { display:flex; flex-direction:column; gap:70px; }
.ow-story { display:grid; grid-template-columns:1.05fr .95fr; gap:50px; align-items:center; }
.ow-story.rev .ow-story-media { order:2; }
.ow-browser { border-radius:14px; overflow:hidden; box-shadow:0 30px 70px rgba(10,22,40,.22); border:1px solid #e6ecf5; background:#fff; }
.ow-browser-bar { display:flex; align-items:center; gap:7px; padding:11px 14px; background:#f3f6fb; border-bottom:1px solid #e9eef6; }
.ow-browser-bar span { width:11px; height:11px; border-radius:50%; background:#d6deea; }
.ow-browser-bar span:nth-child(1){background:#ff5f57} .ow-browser-bar span:nth-child(2){background:#febc2e} .ow-browser-bar span:nth-child(3){background:#28c840}
.ow-browser-shot { aspect-ratio:16/10; background:#eef2f8 center/cover no-repeat; display:flex; align-items:center; justify-content:center; }
.ow-browser-shot .ph { text-align:center; color:#9fb0c7; } .ow-browser-shot .ph i{font-size:2.4rem}
.ow-story-cat { display:inline-block; font-size:.74rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--primary,#0a7ce0); background:rgba(10,124,224,.08); padding:5px 13px; border-radius:20px; margin-bottom:14px; }
.ow-story h3 { font-size:1.7rem; letter-spacing:-.01em; margin-bottom:12px; }
.ow-story .lead { color:var(--text-body,#3d4f6b); line-height:1.7; margin-bottom:18px; }
.ow-cs { margin-bottom:16px; }
.ow-cs .ow-cs-item { margin-bottom:10px; }
.ow-cs b { display:block; font-size:.8rem; text-transform:uppercase; letter-spacing:.04em; margin-bottom:3px; }
.ow-cs .c-challenge b{color:#c0392b} .ow-cs .c-solution b{color:var(--primary,#0a7ce0)}
.ow-cs p { color:var(--text-muted,#64748b); font-size:.92rem; margin:0; line-height:1.6; }
.ow-feats { list-style:none; padding:0; margin:0 0 18px; display:grid; grid-template-columns:1fr 1fr; gap:8px 18px; }
.ow-feats li { font-size:.9rem; color:var(--text-body,#3d4f6b); display:flex; gap:8px; align-items:flex-start; }
.ow-feats li i { color:#1a9f54; margin-top:3px; }
.ow-tech { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:22px; }
.ow-tech span { font-size:.78rem; font-weight:600; color:#44566d; background:#f0f4fa; border:1px solid #e4ebf4; padding:5px 12px; border-radius:20px; }
.ow-link { display:inline-flex; align-items:center; gap:8px; font-weight:700; color:var(--primary,#0a7ce0); text-decoration:none; }
.ow-link:hover { gap:12px; }

/* Process */
.ow-process-wrap { background:linear-gradient(135deg,var(--dark,#0a1628),var(--dark-2,#0f1e38)); color:#fff; }
.ow-process-wrap .ow-head h2{color:#fff} .ow-process-wrap .ow-head p{color:#c8d3e8}
.ow-process-wrap .ow-head .ow-eyebrow{color:var(--accent,#15c6ff);background:rgba(21,198,255,.12);border-color:rgba(21,198,255,.3)}
.ow-process { display:grid; grid-template-columns:repeat(5,1fr); gap:20px; }
.ow-step { text-align:center; position:relative; }
.ow-step .ow-visual { margin:0 auto 18px; }
.ow-step h4 { font-size:1.02rem; margin-bottom:6px; }
.ow-step p { color:#9fb0c7; font-size:.84rem; line-height:1.55; margin:0; }
.ow-step .ow-num { position:absolute; top:-6px; right:calc(50% - 46px); width:24px; height:24px; border-radius:50%; background:#fff; color:var(--dark,#0a1628); font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; }

/* Final CTA */
.ow-cta { background:linear-gradient(120deg,var(--primary-dark,#0a5baa),var(--primary,#0a7ce0) 55%,var(--accent,#15c6ff)); color:#fff; padding:80px 0; text-align:center; }
.ow-cta h2 { font-size:clamp(1.8rem,4vw,2.8rem); margin-bottom:14px; letter-spacing:-.02em; }
.ow-cta p { color:rgba(255,255,255,.9); max-width:560px; margin:0 auto 28px; font-size:1.08rem; }
.ow-cta .ow-btn-primary { background:#fff; color:var(--primary,#0a7ce0); box-shadow:0 14px 34px rgba(0,0,0,.18); }

.ow-empty { text-align:center; padding:60px 20px; color:var(--text-muted,#64748b); }
.ow-empty i { font-size:2.4rem; color:#c3d0e2; margin-bottom:12px; }

@media (max-width:900px){ .ow-build{grid-template-columns:1fr} .ow-story{grid-template-columns:1fr;gap:28px} .ow-story.rev .ow-story-media{order:0} .ow-process{grid-template-columns:repeat(2,1fr);gap:28px} .ow-feats{grid-template-columns:1fr} }
@media (max-width:560px){ .ow-process{grid-template-columns:1fr} .ow-build-card{flex-direction:column;gap:16px} }
</style>
@endsection

@section('content')

<!-- 1. HERO -->
<section class="ow-hero">
    <div class="container">
        <span class="ow-eyebrow">Digital Solutions Showcase</span>
        <h1>{!! $page->title ? e($page->title) : 'We Build Digital Solutions That <span class="grad">Move Businesses Forward.</span>' !!}</h1>
        <p>{{ $page->subtitle ?? 'JayVisionX turns real business challenges into powerful, scalable digital solutions — from automation and web apps to websites and marketing integrations that drive measurable growth.' }}</p>
        <a href="#what-we-build" class="ow-btn ow-btn-primary">Explore Our Solutions <i class="fas fa-arrow-down"></i></a>
    </div>
</section>

<!-- 2. WHAT WE BUILD -->
<section class="ow-section" id="what-we-build">
    <div class="container">
        <div class="ow-head" data-aos="fade-up">
            <span class="ow-eyebrow">What We Build</span>
            <h2>Solutions engineered for growth</h2>
            <p>Every build is designed to solve a real problem and scale with your business.</p>
        </div>
        <div class="ow-build">
            @php $builds = [
                ['fas fa-robot','', 'Business Automation', 'Custom automation that removes manual work — CRM flows, lead capture, chatbots, reminders and reporting that run on autopilot.'],
                ['fas fa-cart-shopping','v4', 'Websites & E-Commerce', 'Fast, SEO-ready websites and online stores with secure payments, inventory and a dashboard that is easy to manage.'],
                ['fas fa-layer-group','v3', 'Custom Web Applications', 'Tailored web apps and SaaS platforms with user roles, dashboards, APIs and the exact workflows your business needs.'],
                ['fas fa-bullhorn','v2', 'Marketing & Integrations', 'Google & Meta Ads, analytics, lead webhooks, Google Business Profile and the integrations that connect your growth stack.'],
            ]; @endphp
            @foreach($builds as [$icon,$v,$title,$desc])
            <div class="ow-build-card" data-aos="fade-up" data-aos-delay="{{ $loop->index*60 }}">
                <div class="ow-visual {{ $v }}"><i class="{{ $icon }}"></i></div>
                <div><h3>{{ $title }}</h3><p>{{ $desc }}</p></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. FEATURED WORK STORIES -->
<section class="ow-section" style="background:#f7fafd" id="featured">
    <div class="container">
        <div class="ow-head" data-aos="fade-up">
            <span class="ow-eyebrow">Featured Work</span>
            <h2>Real projects, real solutions</h2>
            <p>A closer look at how we approach and deliver each build.</p>
        </div>

        <div class="ow-stories">
        @forelse($stories as $story)
            <article class="ow-story {{ $loop->index % 2 ? 'rev' : '' }}" data-aos="fade-up">
                <div class="ow-story-media">
                    <div class="ow-browser">
                        <div class="ow-browser-bar"><span></span><span></span><span></span></div>
                        <div class="ow-browser-shot" @if($story->thumbnail) style="background-image:url('{{ $story->thumbnail_url }}')" @endif>
                            @unless($story->thumbnail)<div class="ph"><i class="fas fa-image"></i></div>@endunless
                        </div>
                    </div>
                </div>
                <div class="ow-story-body">
                    @if($story->category || $story->industry)<span class="ow-story-cat">{{ $story->category ?: $story->industry }}</span>@endif
                    <h3>{{ $story->title }}</h3>
                    @if($story->overview)<p class="lead">{{ $story->overview }}</p>@elseif($story->result)<p class="lead">{{ $story->result }}</p>@endif

                    <div class="ow-cs">
                        @if($story->challenge)<div class="ow-cs-item c-challenge"><b>Challenge</b><p>{{ $story->challenge }}</p></div>@endif
                        @if($story->solution)<div class="ow-cs-item c-solution"><b>Our Solution</b><p>{{ $story->solution }}</p></div>@endif
                    </div>

                    @php $feats = is_array($story->features) ? $story->features : []; @endphp
                    @if(!empty($feats))
                    <ul class="ow-feats">
                        @foreach(array_slice($feats, 0, 6) as $f)<li><i class="fas fa-circle-check"></i> {{ $f }}</li>@endforeach
                    </ul>
                    @endif

                    @php $tech = is_array($story->tech_stack) ? $story->tech_stack : []; @endphp
                    @if(!empty($tech))
                    <div class="ow-tech">@foreach($tech as $t)<span>{{ $t }}</span>@endforeach</div>
                    @endif

                    <a href="{{ $story->project_url ?: route('our-work.show', $story->slug) }}" @if($story->project_url) target="_blank" rel="noopener" @endif class="ow-link">View Project <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
        @empty
            <div class="ow-empty">
                <i class="fas fa-briefcase"></i>
                <p>Our featured work showcase is coming soon.</p>
            </div>
        @endforelse
        </div>
    </div>
</section>

<!-- 4. DEVELOPMENT PROCESS -->
<section class="ow-section ow-process-wrap">
    <div class="container">
        <div class="ow-head" data-aos="fade-up">
            <span class="ow-eyebrow">How We Work</span>
            <h2>Our development process</h2>
            <p>A clear, proven path from idea to launch — and beyond.</p>
        </div>
        <div class="ow-process">
            @php $steps = [
                ['fas fa-compass','', 'Discover', 'We understand your goals, users and business challenge.'],
                ['fas fa-pen-ruler','v2', 'Plan & Design', 'Scope, wireframes and a clean, on-brand design.'],
                ['fas fa-code','v3', 'Develop', 'We build with clean, scalable and secure code.'],
                ['fas fa-rocket','v4', 'Launch', 'Testing, deployment and a smooth go-live.'],
                ['fas fa-headset','v5', 'Support', 'Ongoing updates, maintenance and growth.'],
            ]; @endphp
            @foreach($steps as [$icon,$v,$title,$desc])
            <div class="ow-step" data-aos="fade-up" data-aos-delay="{{ $loop->index*70 }}">
                <span class="ow-num">{{ $loop->iteration }}</span>
                <div class="ow-visual {{ $v }}"><i class="{{ $icon }}"></i></div>
                <h4>{{ $title }}</h4>
                <p>{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 5. FINAL CTA -->
<section class="ow-cta">
    <div class="container" data-aos="zoom-in">
        <h2>Your Next Big Idea Starts Here.</h2>
        <p>Let's turn your business challenge into a digital solution that drives real results.</p>
        <a href="{{ route('contact') }}" class="ow-btn ow-btn-primary">Start Your Project <i class="fas fa-arrow-right"></i></a>
    </div>
</section>
@endsection
