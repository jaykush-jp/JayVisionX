@extends('admin.layouts.app')
@section('title', isset($page) ? 'Edit Page SEO' : 'Add Page')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($page) ? 'Edit SEO: '.ucfirst($page->page_name) : 'Add Page' }}</h2>
    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($page) ? route('admin.pages.update',$page->id) : route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($page)) @method('PUT') @endif

        @if(!isset($page))
        <div class="form-group">
            <label>Page Name (unique identifier) *</label>
            <input type="text" name="page_name" class="form-control" value="{{ old('page_name') }}" required placeholder="e.g. home, about, services">
        </div>
        @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Page Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $page->title ?? '') }}">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $page->subtitle ?? '') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Meta Title <small style="color:#6B7FA3;font-size:0.75rem">(Recommended: 50-60 chars)</small></label>
            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title ?? '') }}" placeholder="SEO page title">
        </div>
        <div class="form-group">
            <label>Meta Description <small style="color:#6B7FA3;font-size:0.75rem">(Recommended: 150-160 chars)</small></label>
            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3">
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Banner / OG Image</label>
                <input type="file" name="og_image" class="form-control" accept="image/*">
                @if(isset($page) && $page->og_image)
                <img src="{{ asset('storage/'.$page->og_image) }}" alt="OG" class="preview-img">
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($page) ? 'Update SEO' : 'Save Page' }}</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
