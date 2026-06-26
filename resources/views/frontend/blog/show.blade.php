@extends('frontend.layouts.app')
@section('meta_title', ($blog->meta_title ?? $blog->title) . ' | JayVisionX Blog')
@section('meta_description', $blog->meta_description ?? $blog->excerpt)
@section('meta_keywords', $blog->meta_keywords ?? $blog->tags)
@section('content')

<section class="page-banner small-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            @if($blog->category)<span class="page-banner-badge">{{ $blog->category }}</span>@endif
            <h1>{{ $blog->title }}</h1>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ route('blog') }}">Blog</a>
                <i class="fas fa-chevron-right"></i>
                <span>{{ Str::limit($blog->title, 40) }}</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blog-detail-grid">
            <div class="blog-detail-main" data-aos="fade-right">
                <div class="blog-detail-meta">
                    <span><i class="fas fa-user"></i> {{ $blog->author }}</span>
                    <span><i class="fas fa-calendar"></i> {{ $blog->created_at->format('F d, Y') }}</span>
                    <span><i class="fas fa-eye"></i> {{ $blog->views }} views</span>
                </div>
                @if($blog->thumbnail)
                <img src="{{ $blog->thumbnail_url }}" alt="{{ $blog->title }}" class="blog-detail-img" loading="lazy">
                @endif
                <div class="blog-detail-content">
                    {!! $blog->content !!}
                </div>
                @if($blog->tags)
                <div class="blog-tags">
                    <strong>Tags:</strong>
                    @foreach(explode(',', $blog->tags) as $tag)
                    <span class="tag">{{ trim($tag) }}</span>
                    @endforeach
                </div>
                @endif
                <div class="blog-share">
                    <strong>Share:</strong>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="share-btn twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->title) }}" target="_blank" class="share-btn linkedin"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($blog->title.' '.url()->current()) }}" target="_blank" class="share-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="blog-detail-sidebar" data-aos="fade-left" data-aos-delay="150">
                <div class="sidebar-card cta-sidebar">
                    <h3>Need Digital Marketing Help?</h3>
                    <p>Get a free consultation with our experts today.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary btn-full">Get Free Consultation</a>
                </div>
                @if($related->count() > 0)
                <div class="sidebar-card">
                    <h4>Related Posts</h4>
                    <ul class="related-blogs-list">
                        @foreach($related as $rb)
                        <li>
                            <a href="{{ route('blog.show', $rb->slug) }}">
                                <img src="{{ $rb->thumbnail_url }}" alt="{{ $rb->title }}" loading="lazy">
                                <div>
                                    <span>{{ $rb->title }}</span>
                                    <small>{{ $rb->created_at->format('M d, Y') }}</small>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
