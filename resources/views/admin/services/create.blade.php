@extends('admin.layouts.app')
@section('title', isset($service) ? 'Edit Service' : 'Add Service')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($service) ? 'Edit Service' : 'Add New Service' }}</h2>
    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="admin-form-card">
    <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Service Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $service->title ?? '') }}" required placeholder="e.g. SEO Services in Delhi NCR">
                @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Icon Class (Font Awesome)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $service->icon ?? '') }}" placeholder="e.g. fa-search">
                <small class="form-hint">Browse icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com</a></small>
            </div>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description" class="form-control" rows="3" placeholder="Brief description shown in service cards...">{{ old('short_description', $service->short_description ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Full Description (HTML supported)</label>
            <textarea name="description" class="form-control wysiwyg" rows="10" id="description">{{ old('description', $service->description ?? '') }}</textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Service Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if(isset($service) && $service->image)
                <img src="{{ $service->image_url }}" alt="Current" class="preview-img">
                @endif
            </div>
            <div class="form-group">
                <label>Banner Image</label>
                <input type="file" name="banner_image" class="form-control" accept="image/*">
                @if(isset($service) && $service->banner_image)
                <img src="{{ asset('storage/'.$service->banner_image) }}" alt="Banner" class="preview-img">
                @endif
            </div>
        </div>

        <div class="form-group">
            <label>Features / Benefits (one per line)</label>
            <textarea name="features_raw" class="form-control" rows="6" id="featuresRaw" placeholder="Enter one feature per line">{{ isset($service) && $service->features ? implode("\n", $service->features) : old('features_raw','') }}</textarea>
            <input type="hidden" name="features_input" id="featuresInput">
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $service->meta_title ?? '') }}" placeholder="SEO title for this service page">
            </div>
            <div class="form-group">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $service->meta_keywords ?? '') }}" placeholder="keyword1, keyword2">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Meta Description</label>
            <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $service->meta_description ?? '') }}</textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}> Active
                </label>
            </div>
            <div class="form-group form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="show_on_home" value="1" {{ old('show_on_home', $service->show_on_home ?? true) ? 'checked' : '' }}> Show on Home Page
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> {{ isset($service) ? 'Update Service' : 'Save Service' }}
            </button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
document.querySelector('form').addEventListener('submit', function() {
    const raw = document.getElementById('featuresRaw').value;
    const features = raw.split('\n').map(f => f.trim()).filter(f => f.length > 0);
    document.getElementById('featuresInput').value = JSON.stringify(features);
});
</script>
@endsection
