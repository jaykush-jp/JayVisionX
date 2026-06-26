@extends('admin.layouts.app')
@section('title', isset($project) ? 'Edit Project' : 'Add Project')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($project) ? 'Edit Project' : 'Add New Project' }}</h2>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($project) ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($project)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Project Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $project->title ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $project->category ?? '') }}" placeholder="e.g. Website, Mobile App, SEO">
            </div>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Client Name</label>
                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $project->client_name ?? '') }}">
            </div>
            <div class="form-group">
                <label>Project URL</label>
                <input type="url" name="project_url" class="form-control" value="{{ old('project_url', $project->project_url ?? '') }}" placeholder="https://...">
            </div>
        </div>
        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description" class="form-control" rows="3">{{ old('short_description', $project->short_description ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Full Description</label>
            <textarea name="description" class="form-control" rows="8">{{ old('description', $project->description ?? '') }}</textarea>
        </div>
        <div class="form-row-3">
            <div class="form-group">
                <label>Thumbnail Image</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                @if(isset($project) && $project->thumbnail)
                <img src="{{ $project->thumbnail_url }}" alt="thumb" class="preview-img">
                @endif
            </div>
            <div class="form-group">
                <label>Completion Date</label>
                <input type="date" name="completion_date" class="form-control" value="{{ old('completion_date', $project->completion_date ?? '') }}">
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $project->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="form-group">
            <label>Technologies Used (comma separated)</label>
            <input type="text" name="technologies_input" class="form-control"
                value="{{ isset($project) && $project->technologies ? implode(', ', $project->technologies) : old('technologies_input','') }}"
                placeholder="Laravel, React.js, MySQL, Flutter">
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Meta Title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $project->meta_title ?? '') }}">
            </div>
            <div class="form-group">
                <label>Meta Description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $project->meta_description ?? '') }}">
            </div>
        </div>
        <div class="form-row-2">
            <div class="form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active ?? true) ? 'checked' : '' }}> Active
                </label>
            </div>
            <div class="form-check-group">
                <label class="form-check-label">
                    <input type="checkbox" name="show_on_home" value="1" {{ old('show_on_home', $project->show_on_home ?? true) ? 'checked' : '' }}> Show on Home Page
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> {{ isset($project) ? 'Update Project' : 'Save Project' }}
            </button>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
