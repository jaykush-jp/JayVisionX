<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - JayVisionX</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('head_extra')
</head>
<body class="admin-body">

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ \App\Models\Setting::get('site_logo') ? asset('storage/'.\App\Models\Setting::get('site_logo')) : asset('images/logo.png') }}" alt="JayVisionX" class="sidebar-logo">
            <span>Admin Panel</span>
        </a>
        <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Content</div>
        <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i> <span>Services</span>
        </a>
        <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i> <span>Projects</span>
        </a>
        <a href="{{ route('admin.case-studies.index') }}" class="sidebar-link {{ request()->routeIs('admin.case-studies*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> <span>Our Work</span>
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="sidebar-link {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}">
            <i class="fas fa-blog"></i> <span>Blogs</span>
        </a>
        <a href="{{ route('admin.technologies.index') }}" class="sidebar-link {{ request()->routeIs('admin.technologies*') ? 'active' : '' }}">
            <i class="fas fa-microchip"></i> <span>Technologies</span>
        </a>
        <a href="{{ route('admin.team.index') }}" class="sidebar-link {{ request()->routeIs('admin.team*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> <span>Team</span>
        </a>
        <a href="{{ route('admin.testimonials.index') }}" class="sidebar-link {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}">
            <i class="fas fa-star"></i> <span>Testimonials</span>
        </a>
        <a href="{{ route('admin.faqs.index') }}" class="sidebar-link {{ request()->routeIs('admin.faqs*') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i> <span>FAQs</span>
        </a>

        <div class="nav-section-label">AI Support</div>
        <a href="{{ route('admin.knowledge.index') }}" class="sidebar-link {{ request()->routeIs('admin.knowledge*') ? 'active' : '' }}">
            <i class="fas fa-brain"></i> <span>Knowledge Base</span>
        </a>
        <a href="{{ route('admin.packages.index') }}" class="sidebar-link {{ request()->routeIs('admin.packages*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i> <span>Packages &amp; Pricing</span>
        </a>
        <a href="{{ route('admin.inbox.index') }}" class="sidebar-link {{ request()->routeIs('admin.inbox*') ? 'active' : '' }}">
            <i class="fas fa-comments"></i> <span>Live Inbox</span>
            @php $openChats = \App\Models\ChatSession::where('status','open')->count(); @endphp
            @if($openChats > 0)<span class="badge-count">{{ $openChats }}</span>@endif
        </a>
        <a href="{{ route('admin.ai-settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.ai-settings*') ? 'active' : '' }}">
            <i class="fas fa-robot"></i> <span>AI Configuration</span>
        </a>
        <a href="{{ route('admin.ai-insights.index') }}" class="sidebar-link {{ request()->routeIs('admin.ai-insights*') ? 'active' : '' }}">
            <i class="fas fa-coins"></i> <span>AI Insights</span>
        </a>
        <a href="{{ route('admin.quotes.index') }}" class="sidebar-link {{ request()->routeIs('admin.quotes*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i> <span>Quotes</span>
        </a>
        <a href="{{ route('admin.automation.index') }}" class="sidebar-link {{ request()->routeIs('admin.automation*') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i> <span>Automation</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> <span>Reports</span>
        </a>
        <a href="{{ route('admin.audit.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> <span>Audit Logs</span>
        </a>

        <div class="nav-section-label">SEO & Pages</div>
        <a href="{{ route('admin.pages.index') }}" class="sidebar-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> <span>Pages & SEO</span>
        </a>

        <div class="nav-section-label">CRM</div>
        <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span>Leads</span>
            @php $unread = \App\Models\Contact::where('is_read',false)->count(); @endphp
            @if($unread > 0)<span class="badge-count">{{ $unread }}</span>@endif
        </a>
        <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
            <i class="fas fa-handshake"></i> <span>Customers</span>
        </a>
        <a href="{{ route('admin.email-templates.index') }}" class="sidebar-link {{ request()->routeIs('admin.email-templates*') ? 'active' : '' }}">
            <i class="fas fa-envelope-open-text"></i> <span>Email Templates</span>
        </a>

        <div class="nav-section-label">Marketing</div>
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
            <i class="fas fa-star-half-alt"></i> <span>Google Reviews</span>
        </a>
        <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i> <span>Social Posts</span>
        </a>
        <a href="{{ route('admin.integrations.index') }}" class="sidebar-link {{ request()->routeIs('admin.integrations*') ? 'active' : '' }}">
            <i class="fas fa-plug"></i> <span>Integrations</span>
        </a>
        <a href="{{ route('admin.credentials.index') }}" class="sidebar-link {{ request()->routeIs('admin.credentials*') ? 'active' : '' }}">
            <i class="fas fa-key"></i> <span>API Credentials</span>
        </a>

        <div class="nav-section-label">Settings</div>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i> <span>Website Settings</span>
        </a>
        <a href="{{ route('admin.account.password') }}" class="sidebar-link {{ request()->routeIs('admin.account*') ? 'active' : '' }}">
            <i class="fas fa-key"></i> <span>Change Password</span>
        </a>
        <a href="{{ route('home') }}" target="_blank" class="sidebar-link">
            <i class="fas fa-external-link-alt"></i> <span>View Website</span>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" class="sidebar-link sidebar-logout">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>

<!-- Main Content -->
<div class="admin-main" id="adminMain">
    <!-- Topbar -->
    <header class="admin-topbar">
        <button class="topbar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-title">@yield('title', 'Dashboard')</div>
        <div class="topbar-right">
            <span class="topbar-admin">
                <i class="fas fa-user-circle"></i> {{ session('admin_name', 'Administrator') }}
            </span>
        </div>
    </header>

    <!-- Content -->
    <div class="admin-content">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<!-- ===== Custom confirm popup + toast (replaces native alert/confirm) ===== -->
<style>
.jvx-modal{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:20px}
.jvx-modal.open{display:flex}
.jvx-modal-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.55)}
.jvx-modal-box{position:relative;background:#fff;border-radius:16px;max-width:400px;width:100%;padding:28px 26px;text-align:center;box-shadow:0 30px 70px rgba(0,0,0,.3);animation:jvxPop .2s ease}
@keyframes jvxPop{from{transform:translateY(10px) scale(.96);opacity:0}to{transform:none;opacity:1}}
.jvx-modal-icon{width:58px;height:58px;border-radius:50%;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 16px}
.jvx-modal-title{font-size:1.2rem;margin:0 0 8px;color:#0f172a;font-weight:700}
.jvx-modal-text{color:#64748b;font-size:.95rem;margin:0 0 22px;line-height:1.5}
.jvx-modal-actions{display:flex;gap:12px;justify-content:center}
.jvx-modal-actions .jvx-btn{border:none;border-radius:10px;padding:11px 24px;font-size:.92rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s ease}
.jvx-btn-ghost{background:#f1f5f9;color:#475569}
.jvx-btn-ghost:hover{background:#e2e8f0}
.jvx-btn-danger{background:#dc2626;color:#fff}
.jvx-btn-danger:hover{background:#b91c1c}
.jvx-toast-wrap{position:fixed;top:20px;right:20px;z-index:100000;display:flex;flex-direction:column;gap:10px;max-width:340px}
.jvx-toast{background:#0f172a;color:#fff;padding:13px 16px;border-radius:10px;font-size:.9rem;box-shadow:0 12px 30px rgba(0,0,0,.25);display:flex;gap:10px;align-items:flex-start;line-height:1.4;animation:jvxToastIn .25s ease;transition:all .25s ease}
.jvx-toast.err{background:#dc2626}
.jvx-toast.ok{background:#16a34a}
.jvx-toast i{margin-top:1px}
@keyframes jvxToastIn{from{transform:translateX(20px);opacity:0}to{transform:none;opacity:1}}
</style>
<div class="jvx-modal" id="jvxConfirm" aria-hidden="true">
    <div class="jvx-modal-backdrop" data-jvx-cancel></div>
    <div class="jvx-modal-box" role="dialog" aria-modal="true" aria-labelledby="jvxConfirmTitle">
        <div class="jvx-modal-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <h3 class="jvx-modal-title" id="jvxConfirmTitle">Are you sure?</h3>
        <p class="jvx-modal-text" id="jvxConfirmText"></p>
        <div class="jvx-modal-actions">
            <button type="button" class="jvx-btn jvx-btn-ghost" data-jvx-cancel>Cancel</button>
            <button type="button" class="jvx-btn jvx-btn-danger" id="jvxConfirmOk">Confirm</button>
        </div>
    </div>
</div>
<div class="jvx-toast-wrap" id="jvxToasts"></div>
<script>
(function () {
    var modal  = document.getElementById('jvxConfirm');
    var textEl = document.getElementById('jvxConfirmText');
    var okBtn  = document.getElementById('jvxConfirmOk');
    var pendingForm = null;

    function okLabel(msg) {
        var m = (msg || '').toLowerCase();
        if (m.indexOf('disconnect') > -1) return 'Disconnect';
        if (m.indexOf('delete') > -1 || m.indexOf('remove') > -1) return 'Delete';
        return 'Confirm';
    }
    function openModal(msg, form) {
        pendingForm = form;
        textEl.textContent = msg || 'Are you sure?';
        okBtn.textContent  = okLabel(msg);
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        okBtn.focus();
    }
    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        pendingForm = null;
    }
    okBtn.addEventListener('click', function () {
        var f = pendingForm;
        closeModal();
        if (f) { f.dataset.jvxConfirmed = '1'; f.submit(); }
    });
    modal.querySelectorAll('[data-jvx-cancel]').forEach(function (b) {
        b.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });

    // Strip inline onsubmit="return confirm('…')" so the native dialog never fires.
    document.querySelectorAll('form[onsubmit]').forEach(function (f) {
        var os = f.getAttribute('onsubmit') || '';
        var m  = os.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
        if (m) {
            f.dataset.confirm = m[2];
            f.removeAttribute('onsubmit');
            f.onsubmit = null;
        }
    });

    // Intercept submit of any confirm form and show the custom popup instead.
    document.addEventListener('submit', function (e) {
        var f = e.target;
        if (f && f.dataset && f.dataset.confirm && f.dataset.jvxConfirmed !== '1') {
            e.preventDefault();
            e.stopPropagation();
            openModal(f.dataset.confirm, f);
        }
    }, true);

    // Toast helper + replace blocking alert() with a non-blocking toast.
    window.jvxToast = function (msg, type) {
        var wrap = document.getElementById('jvxToasts');
        if (!wrap) return;
        var icon = type === 'err' ? 'circle-exclamation' : (type === 'ok' ? 'circle-check' : 'circle-info');
        var t = document.createElement('div');
        t.className = 'jvx-toast ' + (type || '');
        t.innerHTML = '<i class="fas fa-' + icon + '"></i><span></span>';
        t.querySelector('span').textContent = msg;
        wrap.appendChild(t);
        setTimeout(function () {
            t.style.opacity = '0';
            t.style.transform = 'translateX(20px)';
            setTimeout(function () { t.remove(); }, 250);
        }, 3800);
    };
    window.alert = function (msg) { window.jvxToast(String(msg), 'err'); };
})();
</script>
<script src="{{ asset('js/admin.js') }}"></script>
<script>
/* Keep the sidebar scrolled to where it was after a page navigation, so the
   clicked menu item stays in view instead of jumping back to the top. */
(function () {
    var KEY = 'jvx_admin_sidebar_scroll';
    var nav  = document.querySelector('.sidebar-nav');
    var side = document.getElementById('adminSidebar');

    function scroller() {
        if (nav  && nav.scrollHeight  > nav.clientHeight  + 2) return nav;
        if (side && side.scrollHeight > side.clientHeight + 2) return side;
        return nav || side;
    }
    var el = scroller();
    if (!el) return;

    // Restore saved position (runs before first paint at end of <body>).
    try {
        var y = parseInt(sessionStorage.getItem(KEY) || '0', 10);
        if (y > 0) el.scrollTop = y;
    } catch (e) {}

    var t;
    el.addEventListener('scroll', function () {
        clearTimeout(t);
        t = setTimeout(function () {
            try { sessionStorage.setItem(KEY, el.scrollTop); } catch (e) {}
        }, 120);
    }, { passive: true });

    // Capture the position the instant a menu item is clicked, before navigating.
    document.querySelectorAll('.sidebar-nav a, .sidebar-nav button').forEach(function (link) {
        link.addEventListener('click', function () {
            try { sessionStorage.setItem(KEY, el.scrollTop); } catch (e) {}
        });
    });
})();
</script>
@yield('scripts')
</body>
</html>
