<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }
    public function create() { return view('admin.testimonials.create'); }
    public function store(Request $request)
    {
        $request->validate(['client_name' => 'required', 'review' => 'required']);
        $data = $request->except(['photo', '_token']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }
        Testimonial::create($data);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial added.');
    }
    public function edit(Testimonial $testimonial) { return view('admin.testimonials.edit', compact('testimonial')); }
    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->except(['photo', '_token', '_method']);
        if ($request->hasFile('photo')) {
            if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
            $data['photo'] = $request->file('photo')->store('testimonials', 'public');
        }
        $testimonial->update($data);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }
    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->photo) Storage::disk('public')->delete($testimonial->photo);
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted.');
    }
    public function show(Testimonial $testimonial) { return view('admin.testimonials.show', compact('testimonial')); }
}
