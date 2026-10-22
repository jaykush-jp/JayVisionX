<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('sort_order')->orderBy('created_at','desc')->paginate(20);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:2048']);
        $data = $request->except(['thumbnail', '_token']);
        $data['slug'] = Str::slug($request->title);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('blogs', 'public');
        }
        Blog::create($data);
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:2048']);
        $data = $request->except(['thumbnail', '_token', '_method']);
        $data['slug'] = Str::slug($request->title);
        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) Storage::disk('public')->delete($blog->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('blogs', 'public');
        }
        $blog->update($data);
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->thumbnail) Storage::disk('public')->delete($blog->thumbnail);
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted.');
    }

    public function toggleStatus($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->update(['is_active' => !$blog->is_active]);
        return response()->json(['success' => true]);
    }
}
