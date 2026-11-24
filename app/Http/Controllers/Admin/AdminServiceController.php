<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:200',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'starting_price'    => 'nullable|numeric|min:0',
            'price_type'        => 'nullable|in:starting_from,fixed,hourly,monthly,custom',
        ]);

        $data          = $request->except(['image', 'banner_image', 'gallery', 'remove_gallery', '_token', 'features_input', 'deliverables_raw', 'tech_stack_raw']);
        $data['slug']  = Str::slug($request->title);
        $data['features'] = $request->features_input ? json_decode($request->features_input, true) : null;
        $data['deliverables'] = $this->linesToArray($request->deliverables_raw);
        $data['tech_stack']   = $this->linesToArray($request->tech_stack_raw);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        $data['gallery'] = $this->storeGallery($request, []);

        Service::create($data);
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'title'   => 'required|string|max:200',
            'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'starting_price' => 'nullable|numeric|min:0',
            'price_type'     => 'nullable|in:starting_from,fixed,hourly,monthly,custom',
        ]);

        $data = $request->except(['image', 'banner_image', 'gallery', 'remove_gallery', '_token', '_method', 'features_input', 'deliverables_raw', 'tech_stack_raw']);
        $data['slug'] = Str::slug($request->title);
        $data['features'] = $request->features_input ? json_decode($request->features_input, true) : null;
        $data['deliverables'] = $this->linesToArray($request->deliverables_raw);
        $data['tech_stack']   = $this->linesToArray($request->tech_stack_raw);

        if ($request->hasFile('image')) {
            if ($service->image) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        // Gallery: drop removed images, then append new uploads.
        $existing = is_array($service->gallery) ? $service->gallery : [];
        foreach ((array) $request->input('remove_gallery', []) as $rm) {
            Storage::disk('public')->delete($rm);
            $existing = array_filter($existing, fn ($p) => $p !== $rm);
        }
        $data['gallery'] = $this->storeGallery($request, array_values($existing));

        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) Storage::disk('public')->delete($service->image);
        if ($service->banner_image) Storage::disk('public')->delete($service->banner_image);
        foreach ((array) $service->gallery as $g) {
            Storage::disk('public')->delete($g);
        }
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    /** Append any uploaded gallery[] files to the existing paths, return the merged list. */
    private function storeGallery(Request $request, array $existing): array
    {
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file && $file->isValid()) {
                    $existing[] = $file->store('services/gallery', 'public');
                }
            }
        }
        return array_values($existing);
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
        return response()->json(['success' => true, 'status' => $service->is_active]);
    }

    /** Split a textarea (one item per line) into a clean array, or null if empty. */
    private function linesToArray(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }
        $items = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $raw))));
        return $items ?: null;
    }
}

