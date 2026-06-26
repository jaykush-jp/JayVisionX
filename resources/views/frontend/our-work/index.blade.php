@extends('frontend.layouts.app')
@section('meta_title', 'Our Work & Case Studies - JayVisionX')
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Our Work & Case Studies</h1>
            <p>Real challenges. Real solutions. Real results.</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Our Work</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="case-studies-grid" data-aos="fade-up">
            @forelse($caseStudies as $cs)
            <div class="case-study-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="case-study-image">
                    <img src="{{ $cs->thumbnail_url }}" alt="{{ $cs->title }}" loading="lazy">
                    @if($cs->industry)<span class="case-badge">{{ $cs->industry }}</span>@endif
                </div>
                <div class="case-study-body">
                    @if($cs->client)<p class="case-client"><i class="fas fa-building"></i> {{ $cs->client }}</p>@endif
                    <h3>{{ $cs->title }}</h3>
                    @if($cs->result)<p class="case-result"><i class="fas fa-chart-line"></i> {{ Str::limit($cs->result, 100) }}</p>@endif
                    @if($cs->stats)
                    <div class="case-stats">
                        @foreach(array_slice($cs->stats, 0, 3) as $stat)
                        <div class="case-stat"><strong>{{ $stat['value'] ?? '' }}</strong><span>{{ $stat['label'] ?? '' }}</span></div>
                        @endforeach
                    </div>
                    @endif
                    <a href="{{ route('our-work.show', $cs->slug) }}" class="btn btn-outline-primary mt-3">View Case Study <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            @empty
            <div class="empty-state"><i class="fas fa-briefcase"></i><p>Case studies coming soon!</p></div>
            @endforelse
        </div>
        <div class="pagination-wrapper">{{ $caseStudies->links() }}</div>
    </div>
</section>
@endsection
