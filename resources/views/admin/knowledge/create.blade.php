@extends('admin.layouts.app')
@section('title', isset($article) ? 'Edit Knowledge Entry' : 'Add Knowledge Entry')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($article) ? 'Edit Knowledge Entry' : 'Add Knowledge Entry' }}</h2>
    <a href="{{ route('admin.knowledge.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="admin-form-card">
    <form action="{{ isset($article) ? route('admin.knowledge.update', $article->id) : route('admin.knowledge.store') }}" method="POST">
        @csrf
        @if(isset($article)) @method('PUT') @endif

        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $article->title ?? '') }}" required placeholder="e.g. Do you offer website maintenance?">
            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Category *</label>
                <select name="category" class="form-control" required>
                    @php $cat = old('category', $article->category ?? 'faqs'); @endphp
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" @selected($cat===$key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    @php $st = old('status', $article->status ?? 'draft'); @endphp
                    <option value="draft" @selected($st==='draft')>Draft</option>
                    <option value="published" @selected($st==='published')>Published</option>
                    <option value="archived" @selected($st==='archived')>Archived</option>
                </select>
                <small class="form-hint">Only <strong>Published</strong> entries are used by the chatbot.</small>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $article->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" class="form-control" rows="10" placeholder="Write the answer / information the AI should use...">{{ old('content', $article->content ?? '') }}</textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Source URL (optional)</label>
                <input type="url" name="source_url" class="form-control" value="{{ old('source_url', $article->source_url ?? '') }}" placeholder="https://...">
            </div>
            <div class="form-group form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_searchable" value="1" {{ old('is_searchable', $article->is_searchable ?? true) ? 'checked' : '' }}> Usable by chatbot (searchable)
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($article) ? 'Update Entry' : 'Save Entry' }}</button>
            <a href="{{ route('admin.knowledge.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
