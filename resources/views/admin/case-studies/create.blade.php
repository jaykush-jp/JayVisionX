@extends('admin.layouts.app')
@section('title', isset($caseStudy) ? 'Edit Case Study' : 'Add Case Study')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($caseStudy) ? 'Edit Case Study' : 'Add Case Study' }}</h2>
    <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
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
                <input type="text" name="client" class="form-control" value="{{ old('client', $caseStudy->client ?? '') }}">
            </div>
        </div>
        <div class="form-row-3">
            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ old('industry', $caseStudy->industry ?? '') }}" placeholder="e.g. Real Estate, Healthcare">
            </div>
            <div class="form-group">
                <label>Thumbnail</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                @if(isset($caseStudy) && $caseStudy->thumbnail)
                <img src="{{ $caseStudy->thumbnail_url }}" class="preview-img" alt="thumb">
                @endif
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $caseStudy->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="form-group">
            <label>Challenge</label>
            <textarea name="challenge" class="form-control" rows="4" placeholder="What was the client's challenge?">{{ old('challenge', $caseStudy->challenge ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Our Solution</label>
            <textarea name="solution" class="form-control" rows="4" placeholder="How did JayVisionX solve it?">{{ old('solution', $caseStudy->solution ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Results Achieved</label>
            <textarea name="result" class="form-control" rows="4" placeholder="What results were achieved?">{{ old('result', $caseStudy->result ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Stats (JSON format) <small style="color:#6B7FA3">e.g. [{"label":"Traffic Growth","value":"300%"},{"label":"Leads","value":"5x"}]</small></label>
            <textarea name="stats" class="form-control" rows="4" placeholder='[{"label":"Traffic Growth","value":"300%"}]'>{{ old('stats', isset($caseStudy) && $caseStudy->stats ? json_encode($caseStudy->stats, JSON_PRETTY_PRINT) : '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Full Content (HTML supported)</label>
            <textarea name="content" class="form-control" rows="8">{{ old('content', $caseStudy->content ?? '') }}</textarea>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $caseStudy->meta_title ?? '') }}">
            </div>
            <div class="form-group">
                <label>Meta Description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $caseStudy->meta_description ?? '') }}">
            </div>
        </div>
        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $caseStudy->is_active ?? true) ? 'checked' : '' }}> Active
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($caseStudy) ? 'Update' : 'Save' }} Case Study</button>
            <a href="{{ route('admin.case-studies.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
