@extends('admin.layouts.app')
@section('title', 'Website Settings')
@section('content')
<div class="page-header-row"><h2>Website Settings</h2></div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="settings-tabs">
        <button type="button" class="tab-btn active" data-tab="general">General</button>
        <button type="button" class="tab-btn" data-tab="hero">Hero Section</button>
        <button type="button" class="tab-btn" data-tab="social">Social Media</button>
        <button type="button" class="tab-btn" data-tab="seo">SEO</button>
        <button type="button" class="tab-btn" data-tab="analytics">Analytics</button>
    </div>

    <!-- General Tab -->
    <div class="tab-content active" id="tab-general">
        <div class="admin-form-card">
            <h3 class="form-section-title">General Information</h3>
            <div class="form-row-2">
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'JayVisionX' }}">
                </div>
                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="{{ $settings['site_tagline'] ?? '' }}">
                </div>
            </div>
            <div class="form-group">
                <label>Site Description</label>
                <textarea name="site_description" class="form-control" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="site_email" class="form-control" value="{{ $settings['site_email'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="site_phone" class="form-control" value="{{ $settings['site_phone'] ?? '' }}">
                </div>
                <div class="form-group">
                    <label>WhatsApp Number (with country code)</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '' }}" placeholder="919999999999">
                </div>
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="site_address" class="form-control" value="{{ $settings['site_address'] ?? '' }}">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label>Site Logo</label>
                    <input type="file" name="site_logo" class="form-control" accept="image/*">
                    @if(!empty($settings['site_logo']))
                    <img src="{{ asset('storage/'.$settings['site_logo']) }}" alt="Logo" class="preview-img">
                    @endif
                </div>
                <div class="form-group">
                    <label>Favicon</label>
                    <input type="file" name="site_favicon" class="form-control" accept="image/*,.ico">
                    @if(!empty($settings['site_favicon']))
                    <img src="{{ asset('storage/'.$settings['site_favicon']) }}" alt="Favicon" class="preview-img" style="max-width:64px">
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label>Footer About Text</label>
                <textarea name="footer_about" class="form-control" rows="4">{{ $settings['footer_about'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Hero Tab -->
    <div class="tab-content" id="tab-hero">
        <div class="admin-form-card">
            <h3 class="form-section-title">Hero Section</h3>
            <div class="form-group">
                <label>Hero Title</label>
                <input type="text" name="hero_title" class="form-control" value="{{ $settings['hero_title'] ?? '' }}">
            </div>
            <div class="form-group">
                <label>Hero Subtitle</label>
                <textarea name="hero_subtitle" class="form-control" rows="3">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label>CTA Button Text</label>
                    <input type="text" name="hero_cta_text" class="form-control" value="{{ $settings['hero_cta_text'] ?? 'Get Free Consultation' }}">
                </div>
                <div class="form-group">
                    <label>CTA Button Link</label>
                    <input type="text" name="hero_cta_link" class="form-control" value="{{ $settings['hero_cta_link'] ?? '/contact' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Social Tab -->
    <div class="tab-content" id="tab-social">
        <div class="admin-form-card">
            <h3 class="form-section-title">Social Media Links</h3>
            @foreach(['facebook_url'=>'Facebook','instagram_url'=>'Instagram','linkedin_url'=>'LinkedIn','twitter_url'=>'Twitter / X','youtube_url'=>'YouTube'] as $key => $label)
            <div class="form-group">
                <label>{{ $label }} URL</label>
                <input type="url" name="{{ $key }}" class="form-control" value="{{ $settings[$key] ?? '' }}" placeholder="https://...">
            </div>
            @endforeach
        </div>
    </div>

    <!-- SEO Tab -->
    <div class="tab-content" id="tab-seo">
        <div class="admin-form-card">
            <h3 class="form-section-title">Default SEO Settings</h3>
            <div class="form-group">
                <label>Default Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ $settings['meta_title'] ?? '' }}">
            </div>
            <div class="form-group">
                <label>Default Meta Description</label>
                <textarea name="meta_description" class="form-control" rows="3">{{ $settings['meta_description'] ?? '' }}</textarea>
            </div>
            <div class="form-group">
                <label>Default Meta Keywords</label>
                <textarea name="meta_keywords" class="form-control" rows="3">{{ $settings['meta_keywords'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Analytics Tab -->
    <div class="tab-content" id="tab-analytics">
        <div class="admin-form-card">
            <h3 class="form-section-title">Analytics & Tracking</h3>
            <div class="form-group">
                <label>Google Analytics ID (e.g. G-XXXXXXXXXX)</label>
                <input type="text" name="google_analytics" class="form-control" value="{{ $settings['google_analytics'] ?? '' }}">
            </div>
            <div class="form-group">
                <label>Google Tag Manager ID (e.g. GTM-XXXXXXX)</label>
                <input type="text" name="google_tag_manager" class="form-control" value="{{ $settings['google_tag_manager'] ?? '' }}">
            </div>
        </div>
    </div>

    <div class="form-actions sticky-actions">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save All Settings</button>
    </div>
</form>
@endsection
@section('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});
</script>
@endsection
