@extends('frontend.layouts.app')

@section('meta_title', $page->meta_title ?? 'JayVisionX - Digital Marketing Agency & Website Development Company in Delhi NCR')
@section('meta_description', $page->meta_description ?? '')
@section('meta_keywords', $page->meta_keywords ?? '')

@section('head_extra')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "JayVisionX",
  "description": "Digital Marketing Agency & Website Development Company in Delhi NCR",
  "url": "{{ url('/') }}",
  "telephone": "{{ \App\Models\Setting::get('site_phone') }}",
  "address": { "@type": "PostalAddress", "addressLocality": "Delhi NCR", "addressCountry": "IN" },
  "openingHours": "Mo-Sa 09:00-19:00",
  "priceRange": "₹₹"
}
</script>
@endsection

@section('content')

<!-- ===== HERO SECTION ===== -->
<section class="hero-section">
    <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content" data-aos="fade-right" data-aos-duration="900">
                <div class="hero-badge">
                    <i class="fas fa-star"></i>
                    <span>Top Rated Digital Agency in Delhi NCR</span>
                </div>
                <h1 class="hero-title">
                    {{ \App\Models\Setting::get('hero_title', 'Grow Your Business with Website Development, SEO, Ads & Creative Digital Solutions') }}
                </h1>
                <p class="hero-subtitle">
                    {{ \App\Models\Setting::get('hero_subtitle', 'We deliver measurable results through powerful Digital Marketing, SEO, PPC, Social Media Marketing, and cutting-edge Website & App Development.') }}
                </p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-number" data-count="40">0</span><span>+</span>
                        <span class="stat-label">Projects Done</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="stat-number" data-count="50">0</span><span>+</span>
                        <span class="stat-label">Happy Clients</span>
                    </div>
                    <div class="hero-stat-divider"></div>
                    <div class="hero-stat">
                        <span class="stat-number" data-count="3">0</span><span>+</span>
                        <span class="stat-label">Years Experience</span>
                    </div>
                </div>
                <div class="hero-actions">
                    <a href="{{ \App\Models\Setting::get('hero_cta_link', '/contact') }}" class="btn btn-primary btn-lg">
                        {{ \App\Models\Setting::get('hero_cta_text', 'Get Free Consultation') }}
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('projects') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-play-circle"></i> View Our Work
                    </a>
                </div>
            </div>
            <div class="hero-visual" data-aos="fade-left" data-aos-duration="900" data-aos-delay="200">
                <div class="hero-card-group">
                    <div class="hero-card hero-card-1">
                        <i class="fas fa-chart-line"></i>
                        <div>
                            <strong>300%</strong>
                            <span>Traffic Growth</span>
                        </div>
                    </div>
                    <div class="hero-card hero-card-2">
                        <i class="fas fa-trophy"></i>
                        <div>
                            <strong>#1</strong>
                            <span>Google Rankings</span>
                        </div>
                    </div>
                    <div class="hero-card hero-card-3">
                        <i class="fas fa-users"></i>
                        <div>
                            <strong>5x</strong>
                            <span>Lead Generation</span>
                        </div>
                    </div>
                    <div class="hero-main-image">
                        <img src="{{ asset('images/hero-visual.svg') }}" alt="Digital Marketing Agency JayVisionX" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <span>Scroll Down</span>
        <i class="fas fa-chevron-down bounce"></i>
    </div>
</section>

<!-- ===== TRUSTED BY SECTION ===== -->
<section class="trusted-section">
    <div class="container">
        <p class="trusted-label" data-aos="fade-up">Trusted by 200+ businesses across India</p>
        <div class="trusted-logos" data-aos="fade-up" data-aos-delay="100">
            <div class="trusted-logo"><span>Real Estate</span></div>
            <div class="trusted-logo"><span>Healthcare</span></div>
            <div class="trusted-logo"><span>E-Commerce</span></div>
            <div class="trusted-logo"><span>Education</span></div>
            <div class="trusted-logo"><span>Fashion</span></div>
            <div class="trusted-logo"><span>Finance</span></div>
            <div class="trusted-logo"><span>Hospitality</span></div>
        </div>
    </div>
</section>

<!-- ===== SERVICES SECTION ===== -->
<section class="section services-section" id="services">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">What We Do</span>
            <h2 class="section-title">Complete Digital Solutions <br>For Your Business Growth</h2>
            <p class="section-subtitle">From SEO to App Development, we provide end-to-end digital services that help your business generate leads, increase revenue, and dominate your market.</p>
        </div>
        <div class="services-grid" data-aos="fade-up" data-aos-delay="100">
            @foreach($services as $service)
            <div class="service-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="service-card-icon">
                    <i class="fas {{ $service->icon ?? 'fa-star' }}"></i>
                </div>
                <h3 class="service-card-title">{{ $service->title }}</h3>
                <p class="service-card-desc">{{ $service->short_description }}</p>
                <a href="{{ route('services.show', $service->slug) }}" class="service-card-link">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endforeach
        </div>
        <div class="section-cta" data-aos="fade-up">
            <a href="{{ route('services') }}" class="btn btn-primary">View All Services <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- ===== ABOUT PREVIEW ===== -->
<section class="section about-preview-section">
    <div class="container">
        <div class="about-preview-grid">
            <div class="about-preview-image" data-aos="fade-right">
                <div class="about-image-wrapper">
                    <img src="{{ asset('images/about-preview.png') }}" alt="JayVisionX Team - Digital Marketing Agency Delhi NCR" loading="lazy">
                    <div class="about-exp-badge">
                        <strong>3+</strong>
                        <span>Years of Excellence</span>
                    </div>
                </div>
            </div>
            <div class="about-preview-content" data-aos="fade-left" data-aos-delay="150">
                <span class="section-badge">About JayVisionX</span>
                <h2>Delhi NCR's Most Trusted Digital Marketing & IT Company</h2>
                <p>JayVisionX is a full-service Digital Marketing Agency and IT Company based in Delhi NCR. We combine creativity, technology, and data-driven strategies to deliver measurable business results for our clients.</p>
                <p>Our expert team of digital marketers, developers, designers, and content creators work together to help businesses of all sizes grow online — from startups to established enterprises.</p>
                <div class="about-features-list">
                    <div class="about-feature-item"><i class="fas fa-check-circle"></i> 40+ Successful Projects Delivered</div>
                    <div class="about-feature-item"><i class="fas fa-check-circle"></i> 50+ Happy Clients Across India</div>
                    <div class="about-feature-item"><i class="fas fa-check-circle"></i> Dedicated Team of 3+ Experts</div>
                    <div class="about-feature-item"><i class="fas fa-check-circle"></i> 100% Transparent Reporting</div>
                </div>
                <a href="{{ route('about') }}" class="btn btn-primary mt-4">Know More About Us <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ===== TECH STACK ===== -->
<section class="section tech-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Technologies</span>
            <h2 class="section-title">Technologies We Use</h2>
            <p class="section-subtitle">We work with the latest and most powerful technologies to build robust, scalable, and high-performance digital solutions.</p>
        </div>
        <div class="tech-grid" data-aos="fade-up" data-aos-delay="100">
            @php
            $techs = [
                ['name'=>'PHP','icon'=>'fab fa-php','color'=>'#777BB4'],
                ['name'=>'Laravel','icon'=>'fab fa-laravel','color'=>'#FF2D20'],
                ['name'=>'Bootstrap','icon'=>'fab fa-bootstrap','color'=>'#7952B3'],
                ['name'=>'HTML5','icon'=>'fab fa-html5','color'=>'#E34F26'],
                ['name'=>'CSS3','icon'=>'fab fa-css3-alt','color'=>'#1572B6'],
                ['name'=>'JavaScript','icon'=>'fab fa-js-square','color'=>'#F7DF1E'],
                ['name'=>'React.js','icon'=>'fab fa-react','color'=>'#61DAFB'],
                ['name'=>'Node.js','icon'=>'fab fa-node-js','color'=>'#339933'],
                ['name'=>'Vue.js','icon'=>'fab fa-vuejs','color'=>'#4FC08D'],
                ['name'=>'Flutter','icon'=>'fas fa-mobile-alt','color'=>'#54C5F8'],
                ['name'=>'Java','icon'=>'fab fa-java','color'=>'#ED8B00'],
                ['name'=>'Android','icon'=>'fab fa-android','color'=>'#3DDC84'],
            ];
            @endphp
            @foreach($techs as $tech)
            <div class="tech-item" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 40 }}">
                <div class="tech-icon" style="color: {{ $tech['color'] }}">
                    <i class="{{ $tech['icon'] }}"></i>
                </div>
                <span class="tech-name">{{ $tech['name'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== PROJECTS / PORTFOLIO ===== -->
@if($projects->count() > 0)
<section class="section projects-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Portfolio</span>
            <h2 class="section-title">Our Recent Projects</h2>
            <p class="section-subtitle">Take a look at some of our recent work. We've helped businesses across various industries build a strong digital presence.</p>
        </div>
        <div class="projects-grid" data-aos="fade-up" data-aos-delay="100">
            @foreach($projects as $project)
            <div class="project-card">
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
                    <h3>{{ $project->title }}</h3>
                    <p>{{ Str::limit($project->short_description, 100) }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="section-cta" data-aos="fade-up">
            <a href="{{ route('projects') }}" class="btn btn-primary">View All Projects <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

<!-- ===== WHY CHOOSE US ===== -->
<section class="section why-section">
    <div class="container">
        <div class="why-grid">
            <div class="why-content" data-aos="fade-right">
                <span class="section-badge">Why JayVisionX</span>
                <h2>Why Businesses Choose Us Over Other Agencies</h2>
                <p>We don't just promise results — we deliver them. Our data-driven approach, transparent communication, and dedicated team make us the top choice for businesses in Delhi NCR and beyond.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary mt-3">Start Your Project <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="why-features" data-aos="fade-left" data-aos-delay="150">
                @php
                $whyItems = [
                    ['icon'=>'fa-chart-bar','title'=>'Data-Driven Results','desc'=>'Every decision we make is backed by data, analytics, and proven strategies that deliver measurable ROI for your business.'],
                    ['icon'=>'fa-users','title'=>'Expert Team','desc'=>'Our team of 25+ specialists in SEO, ads, development, and design brings deep expertise to every project.'],
                    ['icon'=>'fa-clock','title'=>'On-Time Delivery','desc'=>'We respect your time. Every project is delivered on schedule with complete transparency throughout the process.'],
                    ['icon'=>'fa-headset','title'=>'Dedicated Support','desc'=>'Get dedicated account manager, regular reports, and 24/7 support to ensure your campaigns are always optimized.'],
                    ['icon'=>'fa-rupee-sign','title'=>'Affordable Packages','desc'=>'Premium quality services at competitive prices. We offer flexible packages for businesses of all sizes and budgets.'],
                    ['icon'=>'fa-shield-alt','title'=>'Transparent Reporting','desc'=>'Full access to campaign dashboards with weekly and monthly reports showing exactly what we\'re doing and the results achieved.'],
                ];
                @endphp
                @foreach($whyItems as $item)
                <div class="why-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    <div class="why-icon"><i class="fas {{ $item['icon'] }}"></i></div>
                    <div class="why-text">
                        <h4>{{ $item['title'] }}</h4>
                        <p>{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ===== RESULTS / NUMBERS ===== -->
<section class="section results-section">
    <div class="results-bg-overlay"></div>
    <div class="container">
        <div class="section-header light" data-aos="fade-up">
            <span class="section-badge light">Our Results</span>
            <h2 class="section-title">Digital Marketing Results That Speak For Themselves</h2>
        </div>
        <div class="results-grid" data-aos="fade-up" data-aos-delay="100">
            <div class="result-item" data-aos="zoom-in" data-aos-delay="0">
                <div class="result-icon"><i class="fas fa-chart-line"></i></div>
                <div class="result-number"><span class="count-num" data-count="300">0</span>%</div>
                <div class="result-label">Average Traffic Growth</div>
            </div>
            <div class="result-item" data-aos="zoom-in" data-aos-delay="100">
                <div class="result-icon"><i class="fas fa-bullseye"></i></div>
                <div class="result-number"><span class="count-num" data-count="5">0</span>x</div>
                <div class="result-label">ROI on Ad Campaigns</div>
            </div>
            <div class="result-item" data-aos="zoom-in" data-aos-delay="200">
                <div class="result-icon"><i class="fas fa-star"></i></div>
                <div class="result-number"><span class="count-num" data-count="98">0</span>%</div>
                <div class="result-label">Client Satisfaction Rate</div>
            </div>
            <div class="result-item" data-aos="zoom-in" data-aos-delay="300">
                <div class="result-icon"><i class="fas fa-trophy"></i></div>
                <div class="result-number"><span class="count-num" data-count="40">0</span>+</div>
                <div class="result-label">Projects Delivered</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PROCESS SECTION ===== -->
<section class="section process-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Our Process</span>
            <h2 class="section-title">How We Build Your Digital Success</h2>
            <p class="section-subtitle">A proven, structured approach that ensures every project is delivered with quality, on time and on budget.</p>
        </div>
        <div class="process-steps" data-aos="fade-up" data-aos-delay="100">
            @php
            $steps = [
                ['num'=>'01','icon'=>'fa-comments','title'=>'Discovery & Consultation','desc'=>'We start by understanding your business goals, target audience, competition, and current digital presence through a detailed consultation.'],
                ['num'=>'02','icon'=>'fa-pencil-ruler','title'=>'Strategy & Planning','desc'=>'Our team creates a customized digital strategy and project plan with clear timelines, deliverables, and KPIs tailored to your goals.'],
                ['num'=>'03','icon'=>'fa-code','title'=>'Design & Development','desc'=>'Our designers and developers bring your vision to life with stunning designs and clean, scalable code that performs flawlessly.'],
                ['num'=>'04','icon'=>'fa-rocket','title'=>'Launch & Optimization','desc'=>'After rigorous testing, we launch your project and continuously monitor, analyze, and optimize for maximum performance and ROI.'],
            ];
            @endphp
            @foreach($steps as $step)
            <div class="process-step" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="process-step-num">{{ $step['num'] }}</div>
                <div class="process-step-icon"><i class="fas {{ $step['icon'] }}"></i></div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['desc'] }}</p>
                @if(!$loop->last)<div class="process-arrow"><i class="fas fa-arrow-right"></i></div>@endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
@if($testimonials->count() > 0)
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Testimonials</span>
            <h2 class="section-title">What Our Clients Say About Us</h2>
            <p class="section-subtitle">Don't just take our word for it. Here's what our happy clients say about working with JayVisionX.</p>
        </div>
        <div class="swiper testimonials-swiper" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper-wrapper">
                @foreach($testimonials as $t)
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star {{ $i <= $t->rating ? 'active' : '' }}"></i>
                            @endfor
                        </div>
                        <div class="testimonial-quote"><i class="fas fa-quote-left"></i></div>
                        <p class="testimonial-text">{{ $t->review }}</p>
                        <div class="testimonial-author">
                            @if($t->photo)
                            <img src="{{ asset('storage/'.$t->photo) }}" alt="{{ $t->client_name }}" loading="lazy">
                            @else
                            <div class="testimonial-avatar">{{ strtoupper(substr($t->client_name,0,1)) }}</div>
                            @endif
                            <div class="testimonial-info">
                                <strong>{{ $t->client_name }}</strong>
                                <span>{{ $t->designation }}{{ $t->company ? ', '.$t->company : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>
@endif

<!-- ===== BLOGS ===== -->
@if($blogs->count() > 0)
<section class="section blogs-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Blog & Insights</span>
            <h2 class="section-title">Latest Digital Marketing Insights</h2>
            <p class="section-subtitle">Stay ahead with our latest tips, strategies, and insights on digital marketing, SEO, web development, and business growth.</p>
        </div>
        <div class="blogs-grid" data-aos="fade-up" data-aos-delay="100">
            @foreach($blogs as $blog)
            <article class="blog-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <div class="blog-card-image">
                    <a href="{{ route('blog.show', $blog->slug) }}">
                        <img src="{{ $blog->thumbnail_url }}" alt="{{ $blog->title }}" loading="lazy">
                    </a>
                    @if($blog->category)
                    <span class="blog-category">{{ $blog->category }}</span>
                    @endif
                </div>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span><i class="fas fa-user"></i> {{ $blog->author }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                    </div>
                    <h3><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a></h3>
                    <p>{{ Str::limit($blog->excerpt, 120) }}</p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="blog-read-more">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            @endforeach
        </div>
        <div class="section-cta" data-aos="fade-up">
            <a href="{{ route('blog') }}" class="btn btn-primary">View All Posts <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

<!-- ===== FAQs ===== -->
@if($faqs->count() > 0)
<section class="section faqs-section">
    <div class="container">
        <div class="faqs-grid">
            <div class="faqs-header" data-aos="fade-right">
                <span class="section-badge">FAQs</span>
                <h2>Frequently Asked Questions</h2>
                <p>Got questions? We've got answers. If you don't find what you're looking for, feel free to contact us directly.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary mt-3">Ask Us Anything <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="faqs-list" data-aos="fade-left" data-aos-delay="150">
                <script type="application/ld+json">
                {
                  "@context": "https://schema.org",
                  "@type": "FAQPage",
                  "mainEntity": [
                    @foreach($faqs as $faq)
                    {
                      "@type": "Question",
                      "name": "{{ addslashes($faq->question) }}",
                      "acceptedAnswer": { "@type": "Answer", "text": "{{ addslashes($faq->answer) }}" }
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                  ]
                }
                </script>
                @foreach($faqs as $faq)
                <div class="faq-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <button class="faq-question">
                        {{ $faq->question }}
                        <i class="fas fa-plus faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- ===== CTA SECTION ===== -->
<section class="section cta-section">
    <div class="cta-bg-overlay"></div>
    <div class="container">
        <div class="cta-inner" data-aos="zoom-in">
            <div class="cta-content">
                <h2>Ready to Grow Your Business Online?</h2>
                <p>Let's discuss your project and create a customized digital strategy that delivers real results. Get a FREE consultation with our experts today.</p>
                <div class="cta-actions">
                    <a href="{{ route('contact') }}" class="btn btn-white btn-lg">Get Free Consultation <i class="fas fa-arrow-right"></i></a>
                    <a href="tel:{{ \App\Models\Setting::get('site_phone') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-phone"></i> {{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
// Testimonials Swiper
new Swiper('.testimonials-swiper', {
    loop: true,
    slidesPerView: 1,
    spaceBetween: 30,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
    }
});
</script>
@endsection
