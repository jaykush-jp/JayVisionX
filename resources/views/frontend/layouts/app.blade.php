<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('images/favicon/favicon.ico') }}?v=10">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}?v=10">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}?v=10">

    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/favicon/favicon-512.png') }}?v=10">

    <!-- SEO Meta Tags -->
    <title>@yield('meta_title', \App\Models\Setting::get('meta_title', 'JayVisionX - Digital Marketing Agency & Website Development Company'))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\Setting::get('meta_description', 'JayVisionX is a top Digital Marketing Agency in Delhi NCR offering SEO, Google Ads, Meta Ads, Social Media Marketing, Website Development, App Development & more.'))">
    <meta name="keywords" content="@yield('meta_keywords', \App\Models\Setting::get('meta_keywords', 'Digital Marketing Agency Delhi NCR, Website Development, SEO Services'))">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('meta_title', \App\Models\Setting::get('meta_title'))">
    <meta property="og:description" content="@yield('meta_description', \App\Models\Setting::get('meta_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="JayVisionX">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', \App\Models\Setting::get('meta_title'))">
    <meta name="twitter:description" content="@yield('meta_description', \App\Models\Setting::get('meta_description'))">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="{{ \App\Models\Setting::get('site_favicon') ? asset('storage/' . \App\Models\Setting::get('site_favicon')) : asset('images/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('head_extra')

    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "JayVisionX",
        "url": "{{ url('/') }}",
        "logo": "{{ \App\Models\Setting::get('site_logo') ? asset('storage/'.\App\Models\Setting::get('site_logo')) : asset('images/logo.png') }}",
        "description": "{{ \App\Models\Setting::get('site_description') }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Delhi NCR",
            "addressCountry": "IN"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ \App\Models\Setting::get('site_phone') }}",
            "contactType": "customer service"
        },
        "sameAs": [
            "{{ \App\Models\Setting::get('facebook_url') }}",
            "{{ \App\Models\Setting::get('instagram_url') }}",
            "{{ \App\Models\Setting::get('linkedin_url') }}"
        ]
    }
    </script>
</head>

<body>

    <!-- Top Bar -->
    <div class="topbar">
        <div class="container">
            <div class="topbar-inner">
                <div class="topbar-left">
                    <a href="mailto:{{ \App\Models\Setting::get('site_email', 'info@jayvisionx.com') }}">
                        <i class="fas fa-envelope"></i>
                        {{ \App\Models\Setting::get('site_email', 'info@jayvisionx.com') }}
                    </a>
                    <a href="tel:{{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}">
                        <i class="fas fa-phone"></i> {{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}
                    </a>
                </div>
                <div class="topbar-right">
                    @if (\App\Models\Setting::get('facebook_url'))
                        <a href="{{ \App\Models\Setting::get('facebook_url') }}" target="_blank" rel="noopener"><i
                                class="fab fa-facebook-f"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('instagram_url'))
                        <a href="{{ \App\Models\Setting::get('instagram_url') }}" target="_blank" rel="noopener"><i
                                class="fab fa-instagram"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('linkedin_url'))
                        <a href="{{ \App\Models\Setting::get('linkedin_url') }}" target="_blank" rel="noopener"><i
                                class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('twitter_url'))
                        <a href="{{ \App\Models\Setting::get('twitter_url') }}" target="_blank" rel="noopener"><i
                                class="fab fa-x-twitter"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('youtube_url'))
                        <a href="{{ \App\Models\Setting::get('youtube_url') }}" target="_blank" rel="noopener"><i
                                class="fab fa-youtube"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header" id="site-header">
        <div class="container">
            <nav class="navbar">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="navbar-brand">
                    @if (\App\Models\Setting::get('site_logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('site_logo')) }}" alt="JayVisionX"
                            class="logo-img">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="JayVisionX" class="logo-img">
                    @endif
                </a>

                <!-- Nav Links -->
                <ul class="navbar-nav" id="navbarNav">
                    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                        <a href="{{ route('about') }}" class="nav-link">About</a>
                    </li>
                    <li class="nav-item has-dropdown {{ request()->routeIs('services*') ? 'active' : '' }}">
                        <a href="{{ route('services') }}" class="nav-link">
                            Services <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="mega-dropdown">
                            <div class="mega-dropdown-inner">
                                <div class="mega-dropdown-header">
                                    <h3>Our Services</h3>
                                    <p>End-to-end digital solutions for your business growth</p>
                                </div>
                                <div class="mega-dropdown-grid">
                                    @php $navServices = \App\Models\Service::active()->take(16)->get(); @endphp
                                    @foreach ($navServices as $s)
                                        <a href="{{ route('services.show', $s->slug) }}" class="mega-item">
                                            <span class="mega-icon"><i
                                                    class="fas {{ $s->icon ?? 'fa-star' }}"></i></span>
                                            <span class="mega-text">{{ $s->title }}</span>
                                            <i class="fas fa-arrow-right mega-arrow"></i>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="mega-dropdown-footer">
                                    <a href="{{ route('services') }}" class="btn-all-services">View All Services <i
                                            class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item {{ request()->routeIs('projects*') ? 'active' : '' }}">
                        <a href="{{ route('projects') }}" class="nav-link">Projects</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('our-work*') ? 'active' : '' }}">
                        <a href="{{ route('our-work') }}" class="nav-link">Our Work</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('blog*') ? 'active' : '' }}">
                        <a href="{{ route('blog') }}" class="nav-link">Blog</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                        <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                    </li>
                </ul>

                <!-- CTA -->
                <div class="navbar-cta">
                    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '919999999999') }}"
                        target="_blank" class="btn-whatsapp" title="WhatsApp Us">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-primary">Get Free Quote</a>
                </div>

                <!-- Hamburger -->
                <button class="hamburger" id="hamburger" aria-label="Toggle Menu">
                    <span></span><span></span><span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Main Content -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <!-- About -->
                    <div class="footer-col footer-about">
                        @if (\App\Models\Setting::get('site_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('site_logo')) }}"
                                alt="JayVisionX" class="footer-logo">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="JayVisionX" class="footer-logo">
                        @endif
                        <p>{{ \App\Models\Setting::get('footer_about', 'JayVisionX is a leading Digital Marketing Agency & IT Company in Delhi NCR. We help businesses grow with powerful digital strategies, stunning websites, and result-driven marketing campaigns.') }}
                        </p>
                        <div class="footer-socials">
                            @if (\App\Models\Setting::get('facebook_url'))
                                <a href="{{ \App\Models\Setting::get('facebook_url') }}" target="_blank"
                                    rel="noopener"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if (\App\Models\Setting::get('instagram_url'))
                                <a href="{{ \App\Models\Setting::get('instagram_url') }}" target="_blank"
                                    rel="noopener"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if (\App\Models\Setting::get('linkedin_url'))
                                <a href="{{ \App\Models\Setting::get('linkedin_url') }}" target="_blank"
                                    rel="noopener"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if (\App\Models\Setting::get('twitter_url'))
                                <a href="{{ \App\Models\Setting::get('twitter_url') }}" target="_blank"
                                    rel="noopener"><i class="fab fa-x-twitter"></i></a>
                            @endif
                            @if (\App\Models\Setting::get('youtube_url'))
                                <a href="{{ \App\Models\Setting::get('youtube_url') }}" target="_blank"
                                    rel="noopener"><i class="fab fa-youtube"></i></a>
                            @endif
                        </div>
                    </div>

                    <!-- Services Links -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Our Services</h4>
                        <ul class="footer-links">
                            @php $footerServices = \App\Models\Service::active()->take(8)->get(); @endphp
                            @foreach ($footerServices as $fs)
                                <li><a href="{{ route('services.show', $fs->slug) }}"><i
                                            class="fas fa-chevron-right"></i> {{ $fs->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="{{ route('home') }}"><i class="fas fa-chevron-right"></i> Home</a></li>
                            <li><a href="{{ route('about') }}"><i class="fas fa-chevron-right"></i> About Us</a></li>
                            <li><a href="{{ route('services') }}"><i class="fas fa-chevron-right"></i> Services</a>
                            </li>
                            <li><a href="{{ route('projects') }}"><i class="fas fa-chevron-right"></i> Projects</a>
                            </li>
                            <li><a href="{{ route('our-work') }}"><i class="fas fa-chevron-right"></i> Our Work</a>
                            </li>
                            <li><a href="{{ route('blog') }}"><i class="fas fa-chevron-right"></i> Blog</a></li>
                            <li><a href="{{ route('contact') }}"><i class="fas fa-chevron-right"></i> Contact Us</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact & Newsletter -->
                    <div class="footer-col">
                        <h4 class="footer-heading">Contact Us</h4>
                        <ul class="footer-contact-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ \App\Models\Setting::get('site_address', 'Delhi NCR, India') }}</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a
                                    href="tel:{{ \App\Models\Setting::get('site_phone') }}">{{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a
                                    href="mailto:{{ \App\Models\Setting::get('site_email') }}">{{ \App\Models\Setting::get('site_email', 'info@jayvisionx.com') }}</a>
                            </li>
                        </ul>

                        <div class="footer-newsletter">
                            <h5>Newsletter</h5>
                            <form action="{{ route('newsletter') }}" method="POST" class="newsletter-form">
                                @csrf
                                <div class="newsletter-input-group">
                                    <input type="email" name="email" placeholder="Your email address" required>
                                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-inner">
                    <p>&copy; {{ date('Y') }} <strong>JayVisionX</strong>. All Rights Reserved. | Digital Marketing
                        Agency & Website Development Company in Delhi NCR</p>
                    <p>Designed & Developed with <i class="fas fa-heart text-orange"></i> by JayVisionX Team</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Buttons -->
    <div class="floating-buttons">
        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '919999999999') }}?text=Hello%20JayVisionX!%20I%20need%20digital%20marketing%20services."
            target="_blank" class="float-btn whatsapp-float" title="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
            <span class="float-label">WhatsApp</span>
        </a>
        <a href="tel:{{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}" class="float-btn call-float"
            title="Call Us Now">
            <i class="fas fa-phone"></i>
            <span class="float-label">Call Now</span>
        </a>
    </div>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" title="Back to Top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    @yield('scripts')

    <!-- Google Analytics -->
    @if (\App\Models\Setting::get('google_analytics'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_analytics') }}">
        </script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ \App\Models\Setting::get('google_analytics') }}');
        </script>
    @endif

</body>

</html>
