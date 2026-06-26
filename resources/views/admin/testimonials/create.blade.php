@extends('admin.layouts.app')
@section('title', isset($testimonial) ? 'Edit Testimonial' : 'Add Testimonial')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($testimonial) ? 'Edit Testimonial' : 'Add Testimonial' }}</h2>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($testimonial) ? route('admin.testimonials.update',$testimonial->id) : route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($testimonial)) @method('PUT') @endif

        <div class="form-row-2">
            <div class="form-group">
                <label>Client Name *</label>
                <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $testimonial->client_name ?? '') }}" required>
            </div>
            <div class="form-group">
                <label>Company</label>
                <input type="text" name="company" class="form-control" value="{{ old('company', $testimonial->company ?? '') }}">
            </div>
        </div>
        <div class="form-row-3">
            <div class="form-group">
                <label>Designation</label>
                <input type="text" name="designation" class="form-control" value="{{ old('designation', $testimonial->designation ?? '') }}" placeholder="CEO, Director, Manager...">
            </div>
            <div class="form-group">
                <label>Rating (1-5)</label>
                <select name="rating" class="form-control">
                    @for($i=5;$i>=1;$i--)
                    <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="form-group">
            <label>Review *</label>
            <textarea name="review" class="form-control" rows="5" required>{{ old('review', $testimonial->review ?? '') }}</textarea>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Client Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                @if(isset($testimonial) && $testimonial->photo)
                <img src="{{ asset('storage/'.$testimonial->photo) }}" alt="photo" class="preview-img">
                @endif
            </div>
            <div class="form-check-group" style="align-items:flex-end;padding-bottom:18px">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}> Active
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($testimonial) ? 'Update' : 'Save' }}</button>
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
