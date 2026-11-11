@extends('frontend.layouts.app')
@section('meta_title', $page->meta_title ?? 'Projects & Portfolio - JayVisionX')
@section('meta_description', $page->meta_description ?? 'Explore our successful projects in website development, app development, digital marketing, and creative design.')
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Projects & Portfolio</h1>
            <p>The websites, apps and designs we've built for our clients</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Projects</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cs-intro" data-aos="fade-up">
            <span class="section-badge">Portfolio</span>
            <h2 class="section-title">What we've built</h2>
            <p>
                Yeh hamare delivered projects hain — websites, mobile apps, web apps aur designs.
                Inke peeche ke <strong>results aur numbers</strong> dekhne ke liye? <a href="{{ route('our-work') }}">Case Studies &rarr;</a>
            </p>
        </div>
        <div class="projects-grid-full" data-aos="fade-up">
            @forelse($projects as $project)
            <div class="project-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="project-card-image">
                    <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }}" loading="lazy">
                    <div class="project-overlay">
                        <span class="project-category">{{ $project->category }}</span>
                        <a href="{{ route('projects.show', $project->slug) }}" class="project-view-btn">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="project-card-body">
                    <h3><a href="{{ route('projects.show', $project->slug) }}">{{ $project->title }}</a></h3>
                    <p>{{ Str::limit($project->short_description, 100) }}</p>
                    @if($project->technologies)
                    <div class="project-tags">
                        @foreach(array_slice($project->technologies, 0, 4) as $tech)
                        <span class="tag">{{ $tech }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>No projects available yet. Check back soon!</p>
            </div>
            @endforelse
        </div>
        <div class="pagination-wrapper">
            {{ $projects->links() }}
        </div>
    </div>
</section>
@endsection
