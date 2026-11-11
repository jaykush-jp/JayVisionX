@extends('frontend.layouts.app')
@section('meta_title', 'Our Work & Case Studies - JayVisionX')
@section('meta_description', 'See the measurable results we have delivered — real business challenges, our strategy, and the outcomes for clients across industries.')
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Case Studies</h1>
            <p>Real challenges. Our strategy. Measurable results.</p>
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
        <div class="cs-intro" data-aos="fade-up">
            <span class="section-badge">Our Work</span>
            <h2 class="section-title">Results we've delivered</h2>
            <p>
                Har case study ek real business problem, uska solution, aur uska <strong>measurable result</strong> dikhati hai —
                taaki aap samajh sakein ki hum aapke liye kya achieve kar sakte hain.
                Sirf hamara portfolio (websites &amp; apps) dekhna hai? <a href="{{ route('projects') }}">Projects &rarr;</a>
            </p>
        </div>

        <div class="cs-list">
            @forelse($caseStudies as $cs)
            @php
                // stats is cast to array, but guard against bad/legacy data (string/null).
                $stats = $cs->stats;
                if (is_string($stats)) { $stats = json_decode($stats, true); }
                if (! is_array($stats)) { $stats = []; }
            @endphp
            <article class="cs-row {{ $loop->index % 2 ? 'cs-row--reverse' : '' }}" data-aos="fade-up">
                <div class="cs-media">
                    <img src="{{ $cs->thumbnail_url }}" alt="{{ $cs->title }}" loading="lazy">
                    @if($cs->industry)<span class="cs-industry">{{ $cs->industry }}</span>@endif
                </div>
                <div class="cs-content">
                    @if($cs->client)<p class="cs-client"><i class="fas fa-building"></i> {{ $cs->client }}</p>@endif
                    <h3>{{ $cs->title }}</h3>

                    @if($cs->challenge)
                    <div class="cs-step">
                        <span class="cs-step-label cs-step-label--challenge"><i class="fas fa-circle-exclamation"></i> Challenge</span>
                        <p>{{ Str::limit(strip_tags($cs->challenge), 170) }}</p>
                    </div>
                    @endif
                    @if($cs->solution)
                    <div class="cs-step">
                        <span class="cs-step-label cs-step-label--solution"><i class="fas fa-lightbulb"></i> Solution</span>
                        <p>{{ Str::limit(strip_tags($cs->solution), 170) }}</p>
                    </div>
                    @endif
                    @if($cs->result)
                    <div class="cs-step">
                        <span class="cs-step-label cs-step-label--result"><i class="fas fa-chart-line"></i> Result</span>
                        <p>{{ Str::limit(strip_tags($cs->result), 170) }}</p>
                    </div>
                    @endif

                    @if(!empty($stats))
                    <div class="cs-metrics">
                        @foreach(array_slice($stats, 0, 3) as $stat)
                        <div class="cs-metric">
                            <strong>{{ is_array($stat) ? ($stat['value'] ?? '') : '' }}</strong>
                            <span>{{ is_array($stat) ? ($stat['label'] ?? '') : '' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <a href="{{ route('our-work.show', $cs->slug) }}" class="btn btn-primary mt-2">
                        Read full case study <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            @empty
            <div class="empty-state"><i class="fas fa-briefcase"></i><p>Case studies coming soon!</p></div>
            @endforelse
        </div>

        <div class="pagination-wrapper">{{ $caseStudies->links() }}</div>
    </div>
</section>
@endsection
