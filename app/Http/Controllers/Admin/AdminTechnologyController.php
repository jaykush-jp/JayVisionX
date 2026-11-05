<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Manage the home-page "Technologies We Use" grid.
 *
 * The list is stored as a JSON string in the settings table under the
 * `technologies` key. If it was never saved, we fall back to the default
 * list in config/technologies.php so the section always renders.
 *
 * Each item: name, icon (Font Awesome class), color (hex), image (optional
 * uploaded logo path — used instead of the icon when present, for techs like
 * Next.js that have no Font Awesome icon), and active (shown on the site?).
 */
class AdminTechnologyController extends Controller
{
    /** Full list (saved custom list, else the config default). */
    public static function list(): array
    {
        $saved = Setting::get('technologies');

        if ($saved) {
            $decoded = json_decode($saved, true);
            if (is_array($decoded)) {
                return array_map([self::class, 'normalise'], $decoded);
            }
        }

        return array_map([self::class, 'normalise'], config('technologies.default', []));
    }

    /** Only the active items — used on the public site. */
    public static function listActive(): array
    {
        return array_values(array_filter(self::list(), fn ($t) => $t['active'] !== false));
    }

    /** Fill in defaults for older/partial records. */
    private static function normalise(array $t): array
    {
        return [
            'name'   => $t['name'] ?? '',
            'icon'   => $t['icon'] ?? 'fas fa-code',
            'color'  => $t['color'] ?? '#1E5EFF',
            'image'  => $t['image'] ?? null,
            'active' => array_key_exists('active', $t) ? (bool) $t['active'] : true,
        ];
    }

    public function index()
    {
        return view('admin.technologies.index', ['technologies' => self::list()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'array',
            'name.*'   => 'nullable|string|max:60',
            'icon.*'   => 'nullable|string|max:80',
            'color.*'  => 'nullable|string|max:20',
            'image.*'  => 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:1024',
        ], [
            'image.*.image' => 'Icon ek valid image honi chahiye (png, jpg, webp, svg, gif).',
            'image.*.max'   => 'Icon image 1 MB se chhoti honi chahiye.',
        ]);

        $names    = $request->input('name', []);
        $icons    = $request->input('icon', []);
        $colors   = $request->input('color', []);
        $existing = $request->input('existing_image', []);
        $actives  = $request->input('active', []);     // checkbox: present only when checked
        $files    = $request->file('image', []);

        // Images referenced before this save — so we can delete the ones dropped.
        $oldImages = array_values(array_filter(array_map(fn ($t) => $t['image'] ?? null, self::list())));

        $list = [];
        $keptImages = [];

        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue; // skip empty rows
            }

            // Image: a new upload replaces, otherwise keep the existing path.
            $image = $existing[$i] ?? null;
            if (isset($files[$i]) && $files[$i]->isValid()) {
                if ($image) {
                    Storage::disk('public')->delete($image); // replaced
                }
                $image = $files[$i]->store('technologies', 'public');
            }

            if ($image) {
                $keptImages[] = $image;
            }

            $list[] = [
                'name'   => $name,
                'icon'   => trim((string) ($icons[$i] ?? '')) ?: 'fas fa-code',
                'color'  => trim((string) ($colors[$i] ?? '')) ?: '#1E5EFF',
                'image'  => $image ?: null,
                'active' => (bool) ($actives[$i] ?? false),
            ];
        }

        // Clean up images no longer referenced by any row.
        foreach (array_diff($oldImages, $keptImages) as $orphan) {
            Storage::disk('public')->delete($orphan);
        }

        Setting::set('technologies', json_encode(array_values($list)));

        return back()->with('success', 'Technologies updated successfully.');
    }
}
