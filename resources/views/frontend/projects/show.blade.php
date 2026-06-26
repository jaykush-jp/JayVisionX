@extends('frontend.layouts.app')
@section('meta_title', ($project->meta_title ?? $project->title) . ' - JayVisionX Portfolio')
@section('meta_description', $project->meta_description ?? $project->short_description)
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>{{ $project->title }}</h1>
            <p>{{ $project->category }} — {{ $project->client_name }}</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('projects') }}">Projects</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ $project->title }}</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="project-detail-grid">
            <div class="project-detail-main" data-aos="fade-right">
                <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }}" class="project-detail-img" loading="lazy">
                <div class="project-detail-content">{!! $project->description !!}</div>
                @if($project->technologies)
                <div class="project-tech-box">
                    <h4>Technologies Used</h4>
                    <div class="project-tags">
                        @foreach($project->technologies as $tech)
                        <span class="tag">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="project-detail-sidebar" data-aos="fade-left" data-aos-delay="150">
                <div class="sidebar-card project-info-card">
                    <h4>Project Info</h4>
                    <ul class="project-info-list">
                        @if($project->client_name)<li><strong>Client:</strong> {{ $project->client_name }}</li>@endif
                        @if($project->category)<li><strong>Category:</strong> {{ $project->category }}</li>@endif
                        @if($project->completion_date)<li><strong>Completed:</strong> {{ \Carbon\Carbon::parse($project->completion_date)->format('M Y') }}</li>@endif
                        @if($project->project_url)<li><strong>URL:</strong> <a href="{{ $project->project_url }}" target="_blank" rel="noopener">Visit Website</a></li>@endif
                    </ul>
                </div>
                <div class="sidebar-card cta-sidebar">
                    <h3>Have a similar project?</h3>
                    <p>Let us build something amazing for your business too.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-full">Start Your Project</a>
                </div>
            </div>
        </div>

        @if($related->count() > 0)
        <div class="related-projects mt-5" data-aos="fade-up">
            <h3 class="related-title">Related Projects</h3>
            <div class="projects-grid">
                @foreach($related as $rp)
                <div class="project-card">
                    <div class="project-card-image">
                        <img src="{{ $rp->thumbnail_url }}" alt="{{ $rp->title }}" loading="lazy">
                        <div class="project-overlay">
                            <a href="{{ route('projects.show', $rp->slug) }}" class="project-view-btn"><i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="project-card-body">
                        <h3><a href="{{ route('projects.show', $rp->slug) }}">{{ $rp->title }}</a></h3>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
