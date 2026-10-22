@extends('frontend.layouts.app')
@section('meta_title', 'Page Not Found (404) - JayVisionX')
@section('meta_description', 'The page you are looking for could not be found.')
@section('content')

    <section class="error-page">
        <div class="container">
            <div class="error-inner" data-aos="fade-up">
                <div class="error-code">404</div>
                <h1 class="error-title">Oops! Page Not Found</h1>
                <p class="error-text">
                    The page you are looking for might have been moved, renamed, or no longer exists.
                    Let's get you back on track.
                </p>
                <div class="error-actions">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-headset"></i> Contact Us
                    </a>
                </div>
                <div class="error-links">
                    <span>Popular pages:</span>
                    <a href="{{ route('services') }}">Services</a>
                    <a href="{{ route('projects') }}">Projects</a>
                    <a href="{{ route('blog') }}">Blog</a>
                    <a href="{{ route('about') }}">About</a>
                </div>
            </div>
        </div>
    </section>

@endsection
