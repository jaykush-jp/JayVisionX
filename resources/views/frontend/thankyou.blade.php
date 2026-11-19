@extends('frontend.layouts.app')
@section('meta_title', 'Thank You — JayVisionX')
@section('meta_description', 'Thank you for contacting JayVisionX. Our team will get back to you within 24 hours.')
@section('content')

<section class="section thankyou-section">
    <div class="container">
        <div class="thankyou-card" data-aos="zoom-in">
            <div class="thankyou-check"><i class="fas fa-check"></i></div>
            <h1>Thank You! 🎉</h1>
            <p class="thankyou-lead">Your enquiry has been submitted successfully.</p>
            <p class="thankyou-sub">Our team will contact you within <strong>24 hours</strong>. For anything urgent, reach us on WhatsApp or call.</p>
            <div class="thankyou-actions">
                <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-home"></i> Back to Home</a>
                <a href="{{ route('services') }}" class="btn btn-outline-primary">Explore Services</a>
                <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_number', '919999999999') }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@if($justSubmitted)
<script>
    (function () {
        // Google Ads / GA4 conversion (fires via gtag if configured)
        try {
            if (typeof gtag === 'function') {
                gtag('event', 'generate_lead', { event_category: 'contact', event_label: 'enquiry_form' });
                @php $adsId = \App\Models\Setting::get('google_ads_id'); $adsLabel = \App\Models\Setting::get('google_ads_conversion_label'); @endphp
                @if($adsId && $adsLabel)
                gtag('event', 'conversion', { send_to: '{{ $adsId }}/{{ $adsLabel }}' });
                @endif
            }
        } catch (e) {}
        // Google Tag Manager dataLayer event (use this as a GTM trigger)
        try { window.dataLayer = window.dataLayer || []; window.dataLayer.push({ event: 'generate_lead', form: 'enquiry' }); } catch (e) {}
        // Meta Pixel Lead conversion
        try { if (typeof fbq === 'function') { fbq('track', 'Lead'); } } catch (e) {}
    })();
</script>
@endif
@endsection
