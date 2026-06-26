@extends('frontend.layouts.app')
@section('meta_title', $page->meta_title ?? 'About JayVisionX - Digital Marketing Agency Delhi NCR')
@section('meta_description', $page->meta_description ?? 'Learn about JayVisionX - A trusted Digital Marketing Agency & IT Company in Delhi NCR.')
@section('content')

    <section class="page-banner">
        <div class="page-banner-overlay"></div>
        <div class="container">
            <div class="page-banner-content" data-aos="fade-up">
                <h1>About JayVisionX</h1>
                <p>Delhi NCR's Most Trusted Digital Marketing Agency & IT Company</p>
                <nav class="breadcrumb-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>About Us</span>
                </nav>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="about-detail-grid">
                <div class="about-detail-image" data-aos="fade-right">
                    <img src="{{ asset('images/about-preview.png') }}" alt="JayVisionX Team" loading="lazy">
                    <div class="about-stats-overlay">
                        <div class="about-stat"><strong>3+</strong><span>Years</span></div>
                        <div class="about-stat"><strong>40+</strong><span>Projects</span></div>
                        <div class="about-stat"><strong>50+</strong><span>Clients</span></div>
                    </div>
                </div>
                <div class="about-detail-content" data-aos="fade-left" data-aos-delay="150">
                    <span class="section-badge">About Us</span>
                    <h2>See Beyond. Create Impact.</h2>
                    <p>JayVisionX is a full-service <strong>Digital Marketing Agency & IT Company</strong> based in Delhi
                        NCR. Founded with a vision to empower businesses through technology and creativity, we have grown
                        into a team of 25+ passionate professionals.</p>
                    <p>We offer a comprehensive suite of digital services — from SEO and Google Ads to website development,
                        app development, social media marketing, graphic design, and video production. Our data-driven
                        approach and transparent processes ensure you always see measurable results.</p>
                    <p>We serve businesses across Delhi, Noida, Gurugram, Faridabad, Ghaziabad, and all of India. Whether
                        you're a startup looking to establish your online presence or an established business seeking to
                        scale, we have the expertise and tools to get you there.</p>

                    <div class="about-values">
                        <div class="about-value-item">
                            <i class="fas fa-eye"></i>
                            <div><strong>Our Vision</strong>
                                <p>To be the most trusted digital growth partner for businesses across India.</p>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-bullseye"></i>
                            <div><strong>Our Mission</strong>
                                <p>To deliver innovative, result-oriented digital solutions that create real business
                                    impact.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section team-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-badge">Our Expertise</span>
                <h2 class="section-title">Core Areas of Excellence</h2>
            </div>
            <div class="expertise-grid" data-aos="fade-up" data-aos-delay="100">
                @php
                    $expertise = [
                        ['icon' => 'fa-search', 'title' => 'SEO & Content Marketing', 'value' => '95'],
                        ['icon' => 'fa-ad', 'title' => 'PPC & Google Ads', 'value' => '92'],
                        ['icon' => 'fa-share-alt', 'title' => 'Social Media Marketing', 'value' => '90'],
                        ['icon' => 'fa-laptop-code', 'title' => 'Website Development', 'value' => '98'],
                        ['icon' => 'fa-mobile-alt', 'title' => 'App Development', 'value' => '88'],
                        ['icon' => 'fa-paint-brush', 'title' => 'Graphic Design', 'value' => '94'],
                    ];
                @endphp
                @foreach($expertise as $exp)
                    <div class="expertise-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                        <div class="expertise-icon"><i class="fas {{ $exp['icon'] }}"></i></div>
                        <h4>{{ $exp['title'] }}</h4>
                        <div class="expertise-bar">
                            <div class="expertise-bar-fill" style="width: {{ $exp['value'] }}%"></div>
                        </div>
                        <span class="expertise-percent">{{ $exp['value'] }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials on About Page -->
    @if($testimonials->count() > 0)
        <section class="section testimonials-section">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-badge">Client Reviews</span>
                    <h2 class="section-title">What Our Clients Say</h2>
                </div>
                <div class="swiper testimonials-swiper" data-aos="fade-up">
                    <div class="swiper-wrapper">
                        @foreach($testimonials as $t)
                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-rating">
                                        @for($i = 1; $i <= 5; $i++)<i
                                        class="fas fa-star {{ $i <= $t->rating ? 'active' : '' }}"></i>@endfor
                                    </div>
                                    <p class="testimonial-text">{{ $t->review }}</p>
                                    <div class="testimonial-author">
                                        <div class="testimonial-avatar">{{ strtoupper(substr($t->client_name, 0, 1)) }}</div>
                                        <div class="testimonial-info">
                                            <strong>{{ $t->client_name }}</strong>
                                            <span>{{ $t->designation }}{{ $t->company ? ', ' . $t->company : '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>
    @endif

    <section class="section cta-section">
        <div class="cta-bg-overlay"></div>
        <div class="container">
            <div class="cta-inner" data-aos="zoom-in">
                <div class="cta-content">
                    <h2>Let's Work Together</h2>
                    <p>Ready to take your business to the next level? Let's create something amazing together.</p>
                    <a href="{{ route('contact') }}" class="btn btn-white btn-lg">Get Free Consultation <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>new Swiper('.testimonials-swiper', { loop: true, autoplay: { delay: 5000 }, pagination: { el: '.swiper-pagination', clickable: true }, breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } } });</script>
@endsection