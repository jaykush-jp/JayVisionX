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

    public function showChangePassword()
    {
        return view('admin.account.password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.different' => 'The new password must be different from the current one.',
        ]);

        $adminHash = config('auth.admin_password_hash');

        if (! is_string($adminHash) || $adminHash === '' || ! Hash::check((string) $request->current_password, $adminHash)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $newHash = Hash::make($request->password);

        if (! $this->writeEnv('ADMIN_PASSWORD_HASH', $newHash)) {
            return back()->with('error', 'Could not update the password file. Check .env permissions.');
        }

        // Make the new hash effective immediately (drop any cached config).
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return back()->with('success', 'Password changed successfully. Use the new password next time you log in.');
    }

    /**
     * Update (or append) a single KEY="value" pair in the project .env file.
     * The value is always double-quoted so bcrypt hashes (which contain $) stay intact.
     */
    private function writeEnv(string $key, string $value): bool
    {
        $path = base_path('.env');

        if (! is_file($path) || ! is_writable($path)) {
            return false;
        }

        // Line-based replace. We must NOT use preg_replace here: a bcrypt hash
        // contains $2, $12 etc. which preg_replace would treat as backreferences
        // in the replacement string and corrupt the value.
        $line  = $key . '="' . $value . '"';
        $lines = preg_split('/\r\n|\r|\n/', file_get_contents($path));
        $found = false;

        foreach ($lines as $i => $existing) {
            if (preg_match('/^' . preg_quote($key, '/') . '=/', $existing)) {
                $lines[$i] = $line;
                $found = true;
                break;
            }
        }

        if (! $found) {
            $lines[] = $line;
        }

        return file_put_contents($path, implode(PHP_EOL, $lines)) !== false;
    }
}
