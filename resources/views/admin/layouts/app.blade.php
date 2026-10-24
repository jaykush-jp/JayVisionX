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
            <i class="fas fa-chart-bar"></i> <span>Case Studies</span>
        </a>
        <a href="{{ route('admin.blogs.index') }}" class="sidebar-link {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}">
            <i class="fas fa-blog"></i> <span>Blogs</span>
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

        <div class="nav-section-label">Settings</div>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i> <span>Website Settings</span>
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

<script src="{{ asset('js/admin.js') }}"></script>
@yield('scripts')
</body>
</html>
