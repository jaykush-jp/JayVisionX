{{-- NOTE: This is a general-purpose terms & conditions template. Have it reviewed
     by a legal professional and adjust to your actual business terms before relying on it. --}}
@extends('frontend.layouts.app')
@section('meta_title', 'Terms & Conditions - JayVisionX')
@section('meta_description', 'Read the terms and conditions governing the use of the JayVisionX website and services.')
@section('content')

    @php
        $company = \App\Models\Setting::get('site_name', 'JayVisionX');
        $email   = \App\Models\Setting::get('site_email', 'info@jayvisionx.com');
        $phone   = \App\Models\Setting::get('site_phone', '+91-9999999999');
    @endphp

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="container">
            <div class="page-banner-content" data-aos="fade-up">
                <h1>Terms &amp; Conditions</h1>
                <p>The terms governing the use of our website and services</p>
                <nav class="breadcrumb-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Terms &amp; Conditions</span>
                </nav>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="legal-content" data-aos="fade-up">
                <p class="legal-updated">Last updated: {{ date('F Y') }}</p>

                <p>Welcome to <strong>{{ $company }}</strong>. By accessing or using our website and services, you
                    agree to be bound by these Terms &amp; Conditions. Please read them carefully.</p>

                <h2>1. Acceptance of Terms</h2>
                <p>By using our website or engaging our services, you confirm that you accept these terms and agree to
                    comply with them. If you do not agree, please do not use our services.</p>

                <h2>2. Our Services</h2>
                <p>We provide digital marketing, website and application development, and related creative services.
                    The scope, deliverables, and timelines of any engagement will be agreed separately in a proposal or
                    contract.</p>

                <h2>3. Intellectual Property</h2>
                <p>All content on this website — including text, graphics, logos, and designs — is the property of
                    {{ $company }} unless otherwise stated, and may not be reproduced without permission.</p>

                <h2>4. Client Responsibilities</h2>
                <ul>
                    <li>Provide accurate information and materials required to deliver the services.</li>
                    <li>Review and approve deliverables within agreed timelines.</li>
                    <li>Ensure that any content you supply does not infringe third-party rights.</li>
                </ul>

                <h2>5. Payments</h2>
                <p>Fees, payment schedules, and refund terms for any project will be defined in the relevant proposal
                    or agreement. Late payments may result in suspension of services.</p>

                <h2>6. Limitation of Liability</h2>
                <p>We strive to deliver high-quality services but do not guarantee specific results. To the extent
                    permitted by law, {{ $company }} shall not be liable for any indirect or consequential losses
                    arising from the use of our website or services.</p>

                <h2>7. Third-Party Links</h2>
                <p>Our website may contain links to third-party sites. We are not responsible for the content or
                    practices of those websites.</p>

                <h2>8. Termination</h2>
                <p>We reserve the right to suspend or terminate access to our services for any breach of these terms.</p>

                <h2>9. Governing Law</h2>
                <p>These terms are governed by the laws of India, and any disputes shall be subject to the jurisdiction
                    of the courts of Delhi NCR.</p>

                <h2>10. Changes to These Terms</h2>
                <p>We may revise these Terms &amp; Conditions at any time. Continued use of our services after changes
                    constitutes acceptance of the updated terms.</p>

                <h2>11. Contact Us</h2>
                <p>For questions about these terms, contact us at
                    <a href="mailto:{{ $email }}">{{ $email }}</a> or
                    <a href="tel:{{ $phone }}">{{ $phone }}</a>.</p>
            </div>
        </div>
    </section>

@endsection
