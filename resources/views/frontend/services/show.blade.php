@extends('frontend.layouts.app')
@section('meta_title', ($service->meta_title ?? $service->title) . ' - JayVisionX')
@section('meta_description', $service->meta_description ?? $service->short_description)
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <span class="page-banner-badge"><i class="fas {{ $service->icon ?? 'fa-star' }}"></i></span>
            <h1>{{ $service->title }}</h1>
            <p>{{ $service->short_description }}</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('services') }}">Services</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $service->title }}</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="service-detail-grid">
            <div class="service-detail-main" data-aos="fade-right">
                @if($service->image)
                <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="service-detail-img" loading="lazy">
                @endif
                <div class="service-detail-content">
                    {!! $service->description !!}
                </div>
                @if($service->features && count($service->features) > 0)
                <div class="service-features-box">
                    <h3>Key Features & Benefits</h3>
                    <ul class="service-features-list">
                        @foreach($service->features as $feature)
                        <li><i class="fas fa-check-circle"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="service-detail-sidebar" data-aos="fade-left" data-aos-delay="150">
                <div class="sidebar-card cta-sidebar">
                    <h3>Get This Service</h3>
                    <p>Ready to get started? Contact us for a free consultation and custom quote.</p>
                    <a href="{{ route('contact') }}?service={{ urlencode($service->title) }}" class="btn btn-primary btn-full">Get Free Quote</a>
                    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number','919999999999') }}?text=I'm interested in {{ urlencode($service->title) }}" target="_blank" class="btn btn-whatsapp-lg btn-full mt-2">
                        <i class="fab fa-whatsapp"></i> WhatsApp Us
                    </a>
                </div>
                <div class="sidebar-card">
                    <h4>Other Services</h4>
                    <ul class="sidebar-services-list">
                        @foreach($related as $r)
                        <li><a href="{{ route('services.show', $r->slug) }}"><i class="fas {{ $r->icon ?? 'fa-star' }}"></i> {{ $r->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
