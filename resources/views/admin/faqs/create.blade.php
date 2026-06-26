@extends('admin.layouts.app')
@section('title', isset($faq) ? 'Edit FAQ' : 'Add FAQ')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($faq) ? 'Edit FAQ' : 'Add New FAQ' }}</h2>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($faq) ? route('admin.faqs.update',$faq->id) : route('admin.faqs.store') }}" method="POST">
        @csrf
        @if(isset($faq)) @method('PUT') @endif
        <div class="form-row-2">
            <div class="form-group">
                <label>Category (optional)</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $faq->category ?? '') }}" placeholder="e.g. SEO, Pricing, General">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="form-group">
            <label>Question *</label>
            <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}" required placeholder="Type the frequently asked question...">
        </div>
        <div class="form-group">
            <label>Answer *</label>
            <textarea name="answer" class="form-control" rows="6" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
        </div>
        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}> Active
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($faq) ? 'Update FAQ' : 'Save FAQ' }}</button>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
