<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = 'admin-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Too many login attempts. Please try again in {$seconds} seconds.");
        }

        $adminEmail = config('auth.admin_email');
        $adminHash  = config('auth.admin_password_hash');

        $emailMatches = $adminEmail !== null
            && hash_equals((string) $adminEmail, (string) $request->email);
        $passwordMatches = is_string($adminHash) && $adminHash !== ''
            && Hash::check((string) $request->password, $adminHash);

        if ($emailMatches && $passwordMatches) {
            RateLimiter::clear($throttleKey);

            // Prevent session fixation: issue a fresh session id on login.
            $request->session()->regenerate();

            session([
                'admin_logged_in' => true,
                'admin_email'     => $request->email,
                'admin_name'      => 'Administrator',
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    public function logout(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_email', 'admin_name']);

        // Fully invalidate the session and rotate the CSRF token on logout.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
