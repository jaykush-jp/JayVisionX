@extends('frontend.layouts.app')
@section('meta_title', ($caseStudy->meta_title ?? $caseStudy->title) . ' - JayVisionX Case Study')
@section('meta_description', $caseStudy->meta_description ?? $caseStudy->result)
@section('og_image', $caseStudy->thumbnail_url)
@section('content')

@php
    // stats is cast to array, but guard against bad/legacy data (string/null).
    $stats = $caseStudy->stats;
    if (is_string($stats)) { $stats = json_decode($stats, true); }
    if (! is_array($stats)) { $stats = []; }
@endphp

<section class="page-banner cs-detail-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            @if($caseStudy->industry)<span class="page-banner-badge">{{ $caseStudy->industry }}</span>@endif
            <h1>{{ $caseStudy->title }}</h1>
            @if($caseStudy->client)<p class="cs-detail-client"><i class="fas fa-building"></i> {{ $caseStudy->client }}</p>@endif
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
        @if(!empty($stats))
        <div class="case-results-bar" data-aos="fade-up">
            @foreach(array_slice($stats, 0, 4) as $stat)
            <div class="case-result-item">
                <strong>{{ is_array($stat) ? ($stat['value'] ?? '') : '' }}</strong>
                <span>{{ is_array($stat) ? ($stat['label'] ?? '') : '' }}</span>
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
                <div class="cs-detail-block cs-detail-block--challenge">
                    <span class="cs-detail-ico"><i class="fas fa-circle-exclamation"></i></span>
                    <div class="cs-detail-text"><h3>The Challenge</h3><p>{{ $caseStudy->challenge }}</p></div>
                </div>
                @endif
                @if($caseStudy->solution)
                <div class="cs-detail-block cs-detail-block--solution">
                    <span class="cs-detail-ico"><i class="fas fa-lightbulb"></i></span>
                    <div class="cs-detail-text"><h3>Our Solution</h3><p>{{ $caseStudy->solution }}</p></div>
                </div>
                @endif
                @if($caseStudy->result)
                <div class="cs-detail-block cs-detail-block--result">
                    <span class="cs-detail-ico"><i class="fas fa-chart-line"></i></span>
                    <div class="cs-detail-text"><h3>The Result</h3><p>{{ $caseStudy->result }}</p></div>
                </div>
                @endif

                @if($caseStudy->content)
                <div class="case-detail-content">{!! $caseStudy->content !!}</div>
                @endif

                <a href="{{ route('our-work') }}" class="btn btn-outline-primary mt-3"><i class="fas fa-arrow-left"></i> All case studies</a>
            </div>

            <aside class="case-study-sidebar" data-aos="fade-left" data-aos-delay="150">
                <div class="sidebar-card">
                    <h4>Project Info</h4>
                    <ul class="project-info-list">
                        @if($caseStudy->client)<li><strong>Client:</strong> {{ $caseStudy->client }}</li>@endif
                        @if($caseStudy->industry)<li><strong>Industry:</strong> {{ $caseStudy->industry }}</li>@endif
                    </ul>
                </div>
                <div class="sidebar-card cta-sidebar">
                    <h3>Want Similar Results?</h3>
                    <p>Let us build a customized growth strategy for your business.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-full">Get a Free Strategy Call</a>
                </div>
            </aside>
        </div>

        @if(isset($related) && $related->count())
        <div class="cs-related" data-aos="fade-up">
            <h3 class="section-title cs-related-title">More Case Studies</h3>
            <div class="cs-related-grid">
                @foreach($related as $rc)
                <a href="{{ route('our-work.show', $rc->slug) }}" class="cs-related-card">
                    <div class="cs-related-img"><img src="{{ $rc->thumbnail_url }}" alt="{{ $rc->title }}" loading="lazy">
                        @if($rc->industry)<span class="cs-industry">{{ $rc->industry }}</span>@endif
                    </div>
                    <div class="cs-related-body">
                        <h4>{{ $rc->title }}</h4>
                        @if($rc->result)<p>{{ Str::limit(strip_tags($rc->result), 80) }}</p>@endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
