@extends('frontend.layouts.app')
@section('meta_title', 'Grow Your Business Online - JayVisionX Digital Marketing & Web Development')
@section('meta_description', 'Get more leads and sales with JayVisionX. SEO, Google & Meta Ads, Social Media, Website & App Development in Delhi NCR. Get your free consultation today.')
@section('content')

    @php
        $phone    = \App\Models\Setting::get('site_phone', '+91-9999999999');
        $whatsapp = \App\Models\Setting::get('whatsapp_number', '919999999999');
        $email    = \App\Models\Setting::get('site_email', 'info@jayvisionx.com');
    @endphp

    {{-- ============ HERO + LEAD FORM ============ --}}
    <section class="lp-hero" id="lead-form">
        <div class="lp-hero-overlay"></div>
        <div class="container">
            <div class="lp-hero-grid">

                {{-- Left: pitch --}}
                <div class="lp-hero-content" data-aos="fade-right">
                    <span class="lp-badge"><i class="fas fa-bolt"></i> Delhi NCR's Trusted Digital Growth Partner</span>
                    <h1 class="lp-title">Grow Your Business with <span class="lp-grad">Websites, SEO &amp; Ads</span> That Actually Convert</h1>
                    <p class="lp-subtitle">We build high-performing websites and run data-driven marketing campaigns
                        that bring you more leads, more customers, and measurable growth.</p>

                    <ul class="lp-usp">
                        <li><i class="fas fa-check-circle"></i> 360° Digital Marketing &amp; Web Development</li>
                        <li><i class="fas fa-check-circle"></i> Transparent reporting &amp; real results</li>
                        <li><i class="fas fa-check-circle"></i> Dedicated team &amp; fast turnaround</li>
                    </ul>

                    <div class="lp-hero-stats">
                        <div class="lp-stat"><strong>40+</strong><span>Projects Delivered</span></div>
                        <div class="lp-stat"><strong>50+</strong><span>Happy Clients</span></div>
                        <div class="lp-stat"><strong>3+</strong><span>Years Experience</span></div>
                    </div>

                    <div class="lp-hero-contact">
                        <a href="tel:{{ $phone }}" class="lp-contact-link"><i class="fas fa-phone"></i> {{ $phone }}</a>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="lp-contact-link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a>
                    </div>
                </div>

                {{-- Right: lead form --}}
                <div class="lp-form-wrap" data-aos="fade-left" data-aos-delay="150">
                    <div class="lp-form-card">
                        <div class="lp-form-head">
                            <h2>Get Your Free Consultation</h2>
                            <p>Fill the form &amp; our team will call you within 24 hours.</p>
                        </div>

                        @if (session('success'))
                            <div class="lp-alert lp-alert-success">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="lp-alert lp-alert-error">
                                <i class="fas fa-exclamation-circle"></i> Please check the highlighted fields and try again.
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="lp-form">
                            @csrf
                            <div class="lp-field">
                                <label>Your Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                                @error('name') <span class="lp-err">{{ $message }}</span> @enderror
                            </div>

                            <div class="lp-field-row">
                                <div class="lp-field">
                                    <label>Phone *</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Mobile number" required>
                                    @error('phone') <span class="lp-err">{{ $message }}</span> @enderror
                                </div>
                                <div class="lp-field">
                                    <label>Email *</label>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@email.com" required>
                                    @error('email') <span class="lp-err">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="lp-field">
                                <label>Service You Need</label>
                                <select name="service_interested">
                                    <option value="">Select a service</option>
                                    @foreach ($services as $s)
                                        <option value="{{ $s->title }}" @selected(old('service_interested') === $s->title)>{{ $s->title }}</option>
                                    @endforeach
                                    <option value="Other">Other / Not sure</option>
                                </select>
                            </div>

                            <div class="lp-field">
                                <label>Budget (optional)</label>
                                <select name="budget">
                                    <option value="">Select budget range</option>
                                    <option value="Under ₹25,000">Under ₹25,000</option>
                                    <option value="₹25,000 - ₹50,000">₹25,000 - ₹50,000</option>
                                    <option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
                                    <option value="₹1,00,000+">₹1,00,000+</option>
                                </select>
                            </div>

                            <div class="lp-field">
                                <label>Tell us about your project</label>
                                <textarea name="message" rows="3" placeholder="What do you need help with?">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-full btn-lg">
                                <i class="fas fa-paper-plane"></i> Get Free Consultation
                            </button>
                            <p class="lp-form-note"><i class="fas fa-lock"></i> Your information is 100% safe &amp; confidential.</p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ TRUST STRIP ============ --}}
    <section class="lp-trust">
        <div class="container">
            <div class="lp-trust-inner">
                <span><i class="fas fa-star"></i> 5-Star Rated Agency</span>
                <span><i class="fas fa-users"></i> 50+ Businesses Served</span>
                <span><i class="fas fa-award"></i> 100% Client Satisfaction</span>
                <span><i class="fas fa-headset"></i> Dedicated Support</span>
            </div>
        </div>
    </section>

    {{-- ============ SERVICES ============ --}}
    <section class="section">
        <div class="container">
            <div class="lp-section-head" data-aos="fade-up">
                <span class="section-badge">What We Do</span>
                <h2>Everything You Need to Grow Online</h2>
                <p>End-to-end digital solutions under one roof — from a stunning website to campaigns that drive sales.</p>
            </div>
            <div class="lp-services-grid">
                @forelse ($services as $s)
                    <div class="lp-service-card" data-aos="fade-up">
                        <div class="lp-service-icon"><i class="fas {{ $s->icon ?? 'fa-star' }}"></i></div>
                        <h3>{{ $s->title }}</h3>
                        <p>{{ $s->short_description ?? 'Professional, result-driven solutions tailored to your business.' }}</p>
                        <a href="{{ route('services.show', $s->slug) }}" class="lp-service-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                @empty
                    <p>Services coming soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============ WHY CHOOSE US ============ --}}
    <section class="section lp-why">
        <div class="container">
            <div class="lp-section-head lp-section-head-light" data-aos="fade-up">
                <span class="section-badge">Why JayVisionX</span>
                <h2>Why Businesses Choose Us</h2>
            </div>
            <div class="lp-why-grid">
                @php
                    $why = [
                        ['fa-chart-line', 'Result-Driven Approach', 'We focus on leads, sales and ROI — not vanity metrics.'],
                        ['fa-rocket', 'Fast Delivery', 'Agile process and quick turnaround without compromising quality.'],
                        ['fa-eye', 'Full Transparency', 'Clear reporting and regular updates at every step.'],
                        ['fa-user-shield', 'Dedicated Team', 'A committed team that understands your business goals.'],
                        ['fa-rupee-sign', 'Affordable Pricing', 'Premium quality at prices that fit growing businesses.'],
                        ['fa-sync-alt', 'Ongoing Support', 'We stay with you long after the project goes live.'],
                    ];
                @endphp
                @foreach ($why as $w)
                    <div class="lp-why-card" data-aos="fade-up">
                        <div class="lp-why-icon"><i class="fas {{ $w[0] }}"></i></div>
                        <h4>{{ $w[1] }}</h4>
                        <p>{{ $w[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PROCESS ============ --}}
    <section class="section">
        <div class="container">
            <div class="lp-section-head" data-aos="fade-up">
                <span class="section-badge">How It Works</span>
                <h2>Our Simple 4-Step Process</h2>
            </div>
            <div class="lp-process">
                @php
                    $steps = [
                        ['01', 'fa-comments', 'Consultation', 'We understand your goals, audience and requirements.'],
                        ['02', 'fa-pencil-ruler', 'Strategy &amp; Plan', 'We craft a tailored roadmap for your growth.'],
                        ['03', 'fa-cogs', 'Execution', 'Our team builds and launches with precision.'],
                        ['04', 'fa-chart-pie', 'Results &amp; Scale', 'We track, optimize and scale what works.'],
                    ];
                @endphp
                @foreach ($steps as $st)
                    <div class="lp-step" data-aos="fade-up">
                        <div class="lp-step-num">{{ $st[0] }}</div>
                        <div class="lp-step-icon"><i class="fas {{ $st[1] }}"></i></div>
                        <h4>{!! $st[2] !!}</h4>
                        <p>{{ $st[3] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ RESULTS BAND ============ --}}
    <section class="lp-results">
        <div class="container">
            <div class="lp-results-grid">
                <div class="lp-result" data-aos="zoom-in"><strong>40+</strong><span>Projects Completed</span></div>
                <div class="lp-result" data-aos="zoom-in"><strong>50+</strong><span>Satisfied Clients</span></div>
                <div class="lp-result" data-aos="zoom-in"><strong>15+</strong><span>Services Offered</span></div>
                <div class="lp-result" data-aos="zoom-in"><strong>24/7</strong><span>Support</span></div>
            </div>
        </div>
    </section>

    {{-- ============ PORTFOLIO ============ --}}
    @if ($projects->count())
        <section class="section">
            <div class="container">
                <div class="lp-section-head" data-aos="fade-up">
                    <span class="section-badge">Our Work</span>
                    <h2>Recent Projects</h2>
                </div>
                <div class="lp-portfolio-grid">
                    @foreach ($projects as $p)
                        <a href="{{ route('projects.show', $p->slug) }}" class="lp-portfolio-card" data-aos="fade-up">
                            <img src="{{ $p->thumbnail_url }}" alt="{{ $p->title }}" loading="lazy">
                            <div class="lp-portfolio-info">
                                @if ($p->category)<span>{{ $p->category }}</span>@endif
                                <h4>{{ $p->title }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ TESTIMONIALS ============ --}}
    @if ($testimonials->count())
        <section class="section lp-testi-section">
            <div class="container">
                <div class="lp-section-head" data-aos="fade-up">
                    <span class="section-badge">Testimonials</span>
                    <h2>What Our Clients Say</h2>
                </div>
                <div class="lp-testi-grid">
                    @foreach ($testimonials->take(6) as $t)
                        <div class="lp-testi-card" data-aos="fade-up">
                            <div class="lp-testi-stars">
                                @for ($i = 0; $i < ($t->rating ?? 5); $i++)<i class="fas fa-star"></i>@endfor
                            </div>
                            <p class="lp-testi-text">"{{ $t->review }}"</p>
                            <div class="lp-testi-author">
                                <div class="lp-testi-avatar">{{ strtoupper(substr($t->client_name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $t->client_name }}</strong>
                                    @if ($t->company)<span>{{ $t->designation ? $t->designation . ', ' : '' }}{{ $t->company }}</span>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FAQ ============ --}}
    @if ($faqs->count())
        <section class="section">
            <div class="container">
                <div class="lp-section-head" data-aos="fade-up">
                    <span class="section-badge">FAQ</span>
                    <h2>Frequently Asked Questions</h2>
                </div>
                <div class="lp-faq">
                    @foreach ($faqs as $faq)
                        <details class="lp-faq-item" data-aos="fade-up">
                            <summary>{{ $faq->question }}<i class="fas fa-plus"></i></summary>
                            <div class="lp-faq-answer">{{ $faq->answer }}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FINAL CTA ============ --}}
    <section class="lp-cta">
        <div class="container">
            <div class="lp-cta-inner" data-aos="zoom-in">
                <h2>Ready to Grow Your Business?</h2>
                <p>Get a free, no-obligation consultation with our experts today.</p>
                <div class="lp-cta-actions">
                    <a href="#lead-form" class="btn btn-white btn-lg"><i class="fas fa-paper-plane"></i> Get Free Consultation</a>
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" class="btn btn-outline-white btn-lg"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
                </div>
            </div>
        </div>
    </section>

@endsection
