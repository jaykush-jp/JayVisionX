@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('content')
<div class="page-header-row">
    <h2>Change Password</h2>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <ul style="margin:0;padding-left:18px">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<div class="admin-form-card" style="max-width:520px">
    <p class="text-muted" style="margin-top:0">Apna admin password yahan se badlein. Current password confirm karna zaroori hai.</p>
    <form action="{{ route('admin.account.password.update') }}" method="POST" autocomplete="off">
        @csrf
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
            <small class="text-muted">Kam se kam 8 characters.</small>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-key"></i> Update Password</button>
        </div>
    </form>
</div>
@endsection
