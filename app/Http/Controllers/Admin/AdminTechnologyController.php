<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Manage the home-page "Technologies We Use" grid.
 *
 * The list is stored as a JSON string in the settings table under the
 * `technologies` key. If it was never saved, we fall back to the default
 * list in config/technologies.php so the section always renders.
 */
class AdminTechnologyController extends Controller
{
    /** Current list (saved custom list, else the config default). */
    public static function list(): array
    {
        $saved = Setting::get('technologies');

        if ($saved) {
            $decoded = json_decode($saved, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return config('technologies.default', []);
    }

    public function index()
    {
        return view('admin.technologies.index', ['technologies' => self::list()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'array',
            'name.*' => 'nullable|string|max:60',
            'icon.*' => 'nullable|string|max:80',
            'color.*'=> 'nullable|string|max:20',
        ]);

        $names  = $request->input('name', []);
        $icons  = $request->input('icon', []);
        $colors = $request->input('color', []);

        $list = [];
        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue; // skip empty rows
            }
            $list[] = [
                'name'  => $name,
                'icon'  => trim((string) ($icons[$i] ?? '')) ?: 'fas fa-code',
                'color' => trim((string) ($colors[$i] ?? '')) ?: '#1E5EFF',
            ];
        }

        Setting::set('technologies', json_encode(array_values($list)));

        return back()->with('success', 'Technologies updated successfully.');
    }
}
