<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    /**
     * Settings keys the admin panel is allowed to write.
     * Anything not listed here is ignored, so crafted POSTs can't inject
     * arbitrary settings (e.g. the google_analytics script snippet).
     */
    private const ALLOWED_KEYS = [
        'site_name', 'site_tagline', 'site_description', 'site_email', 'site_phone', 'site_address',
        'site_logo', 'site_favicon',
        'whatsapp_number',
        'facebook_url', 'instagram_url', 'linkedin_url', 'twitter_url', 'youtube_url',
        'meta_title', 'meta_description', 'meta_keywords',
        'footer_about',
        'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_link',
        'google_analytics', 'google_tag_manager',
    ];

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

        $data = $request->only(self::ALLOWED_KEYS);
        unset($data['site_logo'], $data['site_favicon']); // handled as file uploads below

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
