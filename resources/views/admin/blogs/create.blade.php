@extends('admin.layouts.app')
@section('title', isset($blog) ? 'Edit Blog Post' : 'Add Blog Post')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($blog) ? 'Edit Blog Post' : 'Add New Blog Post' }}</h2>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($blog)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Post Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" class="form-control" value="{{ old('author', $blog->author ?? 'JayVisionX Team') }}">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $blog->category ?? '') }}" placeholder="e.g. SEO, PPC, Web Development">
            </div>
            <div class="form-group">
                <label>Tags (comma-separated)</label>
                <input type="text" name="tags" class="form-control" value="{{ old('tags', $blog->tags ?? '') }}" placeholder="SEO, Delhi, Digital Marketing">
            </div>
        </div>

        <div class="form-group">
            <label>Excerpt / Short Description</label>
            <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $blog->excerpt ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" class="form-control wysiwyg" rows="15" id="blogContent">{{ old('content', $blog->content ?? '') }}</textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Thumbnail Image</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                @if(isset($blog) && $blog->thumbnail)
                <img src="{{ $blog->thumbnail_url }}" alt="Current" class="preview-img">
                @endif
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $blog->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $blog->meta_title ?? '') }}">
            </div>
            <div class="form-group">
                <label>Meta Keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $blog->meta_keywords ?? '') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Meta Description</label>
            <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
        </div>

        <div class="form-row-2">
            <div class="form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blog->is_active ?? true) ? 'checked' : '' }}> Active
                </label>
            </div>
            <div class="form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $blog->is_featured ?? false) ? 'checked' : '' }}> Featured Post
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> {{ isset($blog) ? 'Update Post' : 'Publish Post' }}
            </button>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
