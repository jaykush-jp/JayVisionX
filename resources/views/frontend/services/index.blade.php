@extends('frontend.layouts.app')
@section('meta_title', $page->meta_title ?? 'Our Services - JayVisionX Digital Marketing Agency')
@section('meta_description', $page->meta_description ?? 'Explore JayVisionX services: SEO, Google Ads, Meta Ads, Social Media, Website Development, App Development and more.')
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Our Services</h1>
            <p>Complete Digital Marketing & IT Solutions for Business Growth</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Services</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">What We Offer</span>
            <h2 class="section-title">End-to-End Digital Solutions</h2>
            <p class="section-subtitle">From SEO to App Development, we cover every aspect of your digital growth journey.</p>
        </div>
        <div class="services-grid-full" data-aos="fade-up">
            @foreach($services as $service)
            <div class="service-card-full" data-aos="fade-up" data-aos-delay="{{ $loop->index * 40 }}">
                <div class="service-card-icon">
                    <i class="fas {{ $service->icon ?? 'fa-star' }}"></i>
                </div>
                <div class="service-card-body">
                    <h3>{{ $service->title }}</h3>
                    <p>{{ $service->short_description }}</p>
                    <a href="{{ route('services.show', $service->slug) }}" class="service-card-link">
                        View Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section">
    <div class="cta-bg-overlay"></div>
    <div class="container">
        <div class="cta-inner" data-aos="zoom-in">
            <div class="cta-content">
                <h2>Not Sure Which Service You Need?</h2>
                <p>Talk to our experts and get a free consultation. We'll help you find the right solution for your business goals.</p>
                <a href="{{ route('contact') }}" class="btn btn-white btn-lg">Get Free Consultation <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
@endsection
