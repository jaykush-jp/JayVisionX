<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_logo'    => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        $data = $request->except(['_token', '_method', 'site_logo', 'site_favicon']);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('site_logo')) {
            $old = Setting::get('site_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $path);
        }

        if ($request->hasFile('site_favicon')) {
            $old = Setting::get('site_favicon');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_favicon')->store('settings', 'public');
            Setting::set('site_favicon', $path);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
