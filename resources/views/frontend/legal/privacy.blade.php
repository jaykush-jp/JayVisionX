{{-- NOTE: This is a general-purpose privacy policy template. Have it reviewed
     by a legal professional and adjust to your actual data practices before relying on it. --}}
@extends('frontend.layouts.app')
@section('meta_title', 'Privacy Policy - JayVisionX')
@section('meta_description', 'Read the JayVisionX privacy policy to understand how we collect, use, and protect your personal information.')
@section('content')

    @php
        $company = \App\Models\Setting::get('site_name', 'JayVisionX');
        $email   = \App\Models\Setting::get('site_email', 'info@jayvisionx.com');
        $phone   = \App\Models\Setting::get('site_phone', '+91-9999999999');
        $address = \App\Models\Setting::get('site_address', 'Delhi NCR, India');
    @endphp

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="container">
            <div class="page-banner-content" data-aos="fade-up">
                <h1>Privacy Policy</h1>
                <p>How we collect, use, and protect your information</p>
                <nav class="breadcrumb-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Privacy Policy</span>
                </nav>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="legal-content" data-aos="fade-up">
                <p class="legal-updated">Last updated: {{ date('F Y') }}</p>

                <p>At <strong>{{ $company }}</strong> ("we", "us", or "our"), we respect your privacy and are
                    committed to protecting your personal data. This Privacy Policy explains how we collect, use, and
                    safeguard your information when you visit our website or use our services.</p>

                <h2>1. Information We Collect</h2>
                <p>We may collect the following information when you contact us or use our website:</p>
                <ul>
                    <li>Name, email address, and phone number submitted through our forms.</li>
                    <li>Details of the services you are interested in and your project requirements.</li>
                    <li>Technical data such as IP address, browser type, and pages visited.</li>
                </ul>

                <h2>2. How We Use Your Information</h2>
                <ul>
                    <li>To respond to your enquiries and provide requested services.</li>
                    <li>To send you relevant updates, if you have opted in to our newsletter.</li>
                    <li>To improve our website, services, and customer experience.</li>
                    <li>To comply with legal obligations.</li>
                </ul>

                <h2>3. Cookies &amp; Analytics</h2>
                <p>Our website may use cookies and third-party analytics tools (such as Google Analytics) to understand
                    how visitors use the site. You can control cookies through your browser settings.</p>

                <h2>4. Third-Party Services</h2>
                <p>We may use trusted third-party providers (for hosting, analytics, advertising, or communication) who
                    process data on our behalf. These providers are required to keep your information secure.</p>

                <h2>5. Data Security</h2>
                <p>We implement reasonable technical and organisational measures to protect your personal data against
                    unauthorised access, loss, or misuse. However, no method of transmission over the internet is 100%
                    secure.</p>

                <h2>6. Your Rights</h2>
                <p>You may request access to, correction of, or deletion of your personal data at any time by contacting
                    us. You may also unsubscribe from our communications using the link in our emails.</p>

                <h2>7. Data Retention</h2>
                <p>We retain your information only for as long as necessary to fulfil the purposes outlined in this
                    policy or as required by law.</p>

                <h2>8. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with an
                    updated revision date.</p>

                <h2>9. Contact Us</h2>
                <p>If you have questions about this Privacy Policy, contact us at:</p>
                <ul>
                    <li>Email: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    <li>Phone: <a href="tel:{{ $phone }}">{{ $phone }}</a></li>
                    <li>Address: {{ $address }}</li>
                </ul>
            </div>
        </div>
    </section>

@endsection
