@extends('admin.layouts.app')
@section('title', isset($template) ? 'Edit Template' : 'New Template')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($template) ? 'Edit Email Template' : 'New Email Template' }}</h2>
    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($template) ? route('admin.email-templates.update', $template->id) : route('admin.email-templates.store') }}" method="POST">
        @csrf
        @if(isset($template)) @method('PUT') @endif
        <div class="form-group">
            <label>Template Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $template->name ?? '') }}" required placeholder="e.g. First Follow-up">
        </div>
        <div class="form-group">
            <label>Subject *</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Body *</label>
            <textarea name="body" class="form-control" rows="10" required>{{ old('body', $template->body ?? '') }}</textarea>
            <small class="text-muted">Plain text. Line breaks are preserved in the email.</small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($template) ? 'Update' : 'Save' }} Template</button>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
