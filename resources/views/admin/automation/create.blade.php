@extends('admin.layouts.app')
@section('title', isset($rule) ? 'Edit Rule' : 'New Rule')
@section('content')
<div class="page-header-row">
    <h2>{{ isset($rule) ? 'Edit Automation Rule' : 'New Automation Rule' }}</h2>
    <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="admin-form-card">
    <form action="{{ isset($rule) ? route('admin.automation.update', $rule->id) : route('admin.automation.store') }}" method="POST">
        @csrf
        @if(isset($rule)) @method('PUT') @endif
        <div class="form-group">
            <label>Rule name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $rule->name ?? '') }}" required placeholder="e.g. Notify team on human handover">
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>When (trigger) *</label>
                <select name="trigger" class="form-control">
                    @foreach($triggers as $k => $v)
                    <option value="{{ $k }}" {{ old('trigger', $rule->trigger ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Action *</label>
                <select name="action" class="form-control">
                    @foreach($actions as $k => $v)
                    <option value="{{ $k }}" {{ old('action', $rule->action ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Message / note <small class="text-muted">(for auto-message, or appended to the notification)</small></label>
            <textarea name="message" class="form-control" rows="3">{{ old('message', $rule->message ?? '') }}</textarea>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label>Notify email <small class="text-muted">(blank = site email)</small></label>
                <input type="email" name="notify_email" class="form-control" value="{{ old('notify_email', $rule->notify_email ?? '') }}">
            </div>
            <div class="form-group">
                <label>Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $rule->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="form-check-group">
            <label class="form-check-label">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }}> Active
            </label>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> {{ isset($rule) ? 'Update' : 'Save' }} Rule</button>
            <a href="{{ route('admin.automation.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection
