@extends('admin.layouts.app')
@section('title', isset($member) ? 'Edit Member' : 'Add Member')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($member) ? 'Edit Team Member' : 'Add Team Member' }}</h2>
    <a href="{{ route('admin.team.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    @if($errors->any())
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
        <ul style="margin:0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <form action="{{ isset($member) ? route('admin.team.update',$member->id) : route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($member)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $member->name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Designation</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $member->designation ?? '') }}" placeholder="e.g. Founder &amp; CEO">
            </div>
        </div>

        <div class="form-group">
            <label>Role / What they do <small class="text-muted">(short line shown on card)</small></label>
            <input type="text" name="role" class="form-control" value="{{ old('role', $member->role ?? '') }}" placeholder="e.g. Leads SEO &amp; growth strategy">
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>LinkedIn URL</label>
                <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $member->linkedin_url ?? '') }}" placeholder="https://linkedin.com/in/...">
            </div>
            <div class="form-group">
                <label>Instagram URL</label>
                <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $member->instagram_url ?? '') }}" placeholder="https://instagram.com/...">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $member->sort_order ?? 0) }}">
            </div>
            <div class="form-group">
                <label>Photo</label>
                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                <small class="text-muted">Square photo best. Max 3 MB.</small>
                @error('photo')<span class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                @if(isset($member) && $member->photo)<img src="{{ $member->photo_url }}" class="preview-img" alt="photo" style="border-radius:50%;width:70px;height:70px;object-fit:cover">@endif
            </div>
        </div>

        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }}> Active (show on website)
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($member) ? 'Update' : 'Save' }} Member</button>
            <a href="{{ route('admin.team.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
