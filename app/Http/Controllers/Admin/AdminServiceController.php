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
            'banner_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data          = $request->except(['image', 'banner_image', '_token', 'features_input']);
        $data['slug']  = Str::slug($request->title);
        $data['features'] = $request->features_input ? json_decode($request->features_input, true) : null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('services', 'public');
        }

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
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except(['image', 'banner_image', '_token', '_method', 'features_input']);
        $data['slug'] = Str::slug($request->title);
        $data['features'] = $request->features_input ? json_decode($request->features_input, true) : null;

        if ($request->hasFile('image')) {
            if ($service->image) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }
        if ($request->hasFile('banner_image')) {
            if ($service->banner_image) Storage::disk('public')->delete($service->banner_image);
            $data['banner_image'] = $request->file('banner_image')->store('services', 'public');
        }

        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) Storage::disk('public')->delete($service->image);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted.');
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => !$service->is_active]);
        return response()->json(['success' => true, 'status' => $service->is_active]);
    }
}
