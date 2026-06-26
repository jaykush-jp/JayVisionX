<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $adminEmail    = config('app.admin_email', env('ADMIN_EMAIL', 'admin@jayvisionx.com'));
        $adminPassword = config('app.admin_password', env('ADMIN_PASSWORD', 'JayVisionX@2024'));

        if ($request->email === $adminEmail && $request->password === $adminPassword) {
            session([
                'admin_logged_in' => true,
                'admin_email'     => $request->email,
                'admin_name'      => 'Administrator',
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }

        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    public function logout(Request $request)
    {
        session()->forget(['admin_logged_in', 'admin_email', 'admin_name']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
