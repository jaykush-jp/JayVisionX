@extends('frontend.layouts.app')
@section('meta_title', $page->meta_title ?? 'Blog - Digital Marketing Insights | JayVisionX')
@section('meta_description', $page->meta_description ?? 'Read our latest blog posts on digital marketing, SEO, Google Ads, social media marketing, and website development.')
@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Digital Marketing Blog</h1>
            <p>Tips, insights, and strategies to grow your business online</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Blog</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blogs-grid-full" data-aos="fade-up">
            @forelse($blogs as $blog)
            <article class="blog-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="blog-card-image">
                    <a href="{{ route('blog.show', $blog->slug) }}">
                        <img src="{{ $blog->thumbnail_url }}" alt="{{ $blog->title }}" loading="lazy">
                    </a>
                    @if($blog->category)<span class="blog-category">{{ $blog->category }}</span>@endif
                </div>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span><i class="fas fa-user"></i> {{ $blog->author }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                        <span><i class="fas fa-eye"></i> {{ $blog->views }} views</span>
                    </div>
                    <h3><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h3>
                    <p>{{ Str::limit($blog->excerpt, 150) }}</p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="blog-read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            @empty
            <div class="empty-state">
                <i class="fas fa-rss"></i>
                <p>No blog posts yet. Check back soon!</p>
            </div>
            @endforelse
        </div>
        <div class="pagination-wrapper">{{ $blogs->links() }}</div>
    </div>
</section>
@endsection
