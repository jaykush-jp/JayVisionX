@extends('admin.layouts.app')
@section('title', isset($caseStudy) ? 'Edit Work' : 'Add Work')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($caseStudy) ? 'Edit Work' : 'Add New Work' }}</h2>
    <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    @if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif
    <form action="{{ isset($caseStudy) ? route('admin.case-studies.update',$caseStudy->id) : route('admin.case-studies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($caseStudy)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $caseStudy->title ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Client</label>
                <input type="text" name="client" class="form-control" value="{{ old('client', $caseStudy->client ?? '') }}" placeholder="Client / company name">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Category / Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ old('industry', $caseStudy->industry ?? '') }}" placeholder="e.g. Website, Real Estate, Healthcare">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $caseStudy->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="result" class="form-control" rows="3" placeholder="One or two lines shown on the Our Work card.">{{ old('result', $caseStudy->result ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Full Description</label>
            <textarea name="content" class="form-control" rows="8" placeholder="Details about this work (HTML supported). Optional.">{{ old('content', $caseStudy->content ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
            <small class="text-muted">Max 3 MB. Formats: jpg, png, webp, gif.</small>
            @error('thumbnail')<span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
            @if(isset($caseStudy) && $caseStudy->thumbnail)
            <img src="{{ $caseStudy->thumbnail_url }}" class="preview-img" alt="thumb">
            @endif
        </div>

        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $caseStudy->is_active ?? true) ? 'checked' : '' }}> Active (show on website)
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($caseStudy) ? 'Update' : 'Save' }} Work</button>
            <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
