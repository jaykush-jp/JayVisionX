@extends('frontend.layouts.app')
@section('meta_title', ($caseStudy->meta_title ?? $caseStudy->title) . ' - JayVisionX Case Study')
@section('meta_description', $caseStudy->meta_description ?? $caseStudy->result)
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            @if($caseStudy->industry)<span class="page-banner-badge">{{ $caseStudy->industry }}</span>@endif
            <h1>{{ $caseStudy->title }}</h1>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('our-work') }}">Our Work</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ Str::limit($caseStudy->title, 40) }}</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        @if($caseStudy->stats)
        <div class="case-results-bar" data-aos="fade-up">
            @foreach($caseStudy->stats as $stat)
            <div class="case-result-item">
                <strong>{{ $stat['value'] ?? '' }}</strong>
                <span>{{ $stat['label'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
        @endif

        <div class="case-study-detail-grid">
            <div class="case-study-main" data-aos="fade-right">
                @if($caseStudy->thumbnail)
                <img src="{{ $caseStudy->thumbnail_url }}" alt="{{ $caseStudy->title }}" class="case-detail-img" loading="lazy">
                @endif
                @if($caseStudy->challenge)
                <div class="case-section"><h3><i class="fas fa-exclamation-circle"></i> The Challenge</h3><p>{{ $caseStudy->challenge }}</p></div>
                @endif
                @if($caseStudy->solution)
                <div class="case-section"><h3><i class="fas fa-lightbulb"></i> Our Solution</h3><p>{{ $caseStudy->solution }}</p></div>
                @endif
                @if($caseStudy->result)
                <div class="case-section case-result-section"><h3><i class="fas fa-chart-line"></i> Results Achieved</h3><p>{{ $caseStudy->result }}</p></div>
                @endif
                @if($caseStudy->content)<div class="case-detail-content">{!! $caseStudy->content !!}</div>@endif
            </div>
            <div class="case-study-sidebar" data-aos="fade-left" data-aos-delay="150">
                <div class="sidebar-card">
                    <h4>Project Info</h4>
                    <ul class="project-info-list">
                        @if($caseStudy->client)<li><strong>Client:</strong> {{ $caseStudy->client }}</li>@endif
                        @if($caseStudy->industry)<li><strong>Industry:</strong> {{ $caseStudy->industry }}</li>@endif
                    </ul>
                </div>
                <div class="sidebar-card cta-sidebar">
                    <h3>Want Similar Results?</h3>
                    <p>Let us create a customized strategy for your business.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-full">Get Free Strategy Call</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
