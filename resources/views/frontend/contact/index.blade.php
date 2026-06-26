@extends('frontend.layouts.app')

@section('meta_title', $page->meta_title ?? 'Contact JayVisionX - Get Free Digital Marketing Consultation')
@section('meta_description', $page->meta_description ?? 'Contact JayVisionX for SEO, Google Ads, website development, and digital marketing services in Delhi NCR.')

@section('content')

<section class="page-banner">
    <div class="page-banner-overlay"></div>
    <div class="container">
        <div class="page-banner-content" data-aos="fade-up">
            <h1>Contact Us</h1>
            <p>Let's discuss how we can help grow your business digitally</p>
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Contact</span>
            </nav>
        </div>
    </div>
</section>

<section class="section contact-section">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success" data-aos="fade-down">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info-col" data-aos="fade-right">
                <h2>Get In Touch</h2>
                <p>Fill out the form and our team will get back to you within 24 hours. Or reach us directly via phone or WhatsApp.</p>

                <div class="contact-info-cards">
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <strong>Our Location</strong>
                            <span>{{ \App\Models\Setting::get('site_address', 'Delhi NCR, India') }}</span>
                        </div>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <strong>Call Us</strong>
                            <a href="tel:{{ \App\Models\Setting::get('site_phone') }}">{{ \App\Models\Setting::get('site_phone', '+91-9999999999') }}</a>
                        </div>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <strong>Email Us</strong>
                            <a href="mailto:{{ \App\Models\Setting::get('site_email') }}">{{ \App\Models\Setting::get('site_email', 'info@jayvisionx.com') }}</a>
                        </div>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon whatsapp-icon"><i class="fab fa-whatsapp"></i></div>
                        <div>
                            <strong>WhatsApp</strong>
                            <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number','919999999999') }}" target="_blank">Chat with us instantly</a>
                        </div>
                    </div>
                </div>

                <div class="contact-quick-btns">
                    <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number','919999999999') }}?text=Hello%20JayVisionX!%20I%20need%20your%20services." target="_blank" class="btn btn-whatsapp-lg">
                        <i class="fab fa-whatsapp"></i> WhatsApp Us
                    </a>
                    <a href="tel:{{ \App\Models\Setting::get('site_phone') }}" class="btn btn-call-lg">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-col" data-aos="fade-left" data-aos-delay="150">
                <div class="contact-form-card">
                    <h3>Send Us Your Requirements</h3>
                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your full name" value="{{ old('name') }}" required>
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+91-XXXXXXXXXX" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                            @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="service_interested">Service Interested In</label>
                                <select id="service_interested" name="service_interested" class="form-control">
                                    <option value="">-- Select Service --</option>
                                    @foreach($services as $s)
                                    <option value="{{ $s->title }}" {{ old('service_interested') == $s->title ? 'selected' : '' }}>{{ $s->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="budget">Budget Range</label>
                                <select id="budget" name="budget" class="form-control">
                                    <option value="">-- Select Budget --</option>
                                    <option value="Under ₹10,000">Under ₹10,000</option>
                                    <option value="₹10,000 - ₹25,000">₹10,000 - ₹25,000</option>
                                    <option value="₹25,000 - ₹50,000">₹25,000 - ₹50,000</option>
                                    <option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
                                    <option value="₹1,00,000+">₹1,00,000+</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tell us about your project, requirements, or any questions...">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-full">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
