<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - JayVisionX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-login-body">
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="{{ \App\Models\Setting::get('site_logo') ? asset('storage/'.\App\Models\Setting::get('site_logo')) : asset('images/logo.png') }}" alt="JayVisionX">
        </div>
        <h2>Admin Login</h2>
        <p class="login-subtitle">Sign in to manage your website</p>

        @if(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="login-form">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@jayvisionx.com" value="{{ old('email') }}" required autofocus>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="passwordField" class="form-control" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full btn-lg">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-footer">
            <a href="{{ route('home') }}" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    const f = document.getElementById('passwordField');
    const i = document.getElementById('passwordIcon');
    if (f.type === 'password') { f.type = 'text'; i.className = 'fas fa-eye-slash'; }
    else { f.type = 'password'; i.className = 'fas fa-eye'; }
}
</script>
</body>
</html>
