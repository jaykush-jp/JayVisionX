<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order')->paginate(20);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:2048']);
        $data = $request->except(['thumbnail', '_token', 'technologies_input']);
        $data['slug'] = Str::slug($request->title);
        $data['technologies'] = $request->technologies_input ? explode(',', $request->technologies_input) : null;
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('projects', 'public');
        }
        Project::create($data);
        return redirect()->route('admin.projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:2048']);
        $data = $request->except(['thumbnail', '_token', '_method', 'technologies_input']);
        $data['slug'] = Str::slug($request->title);
        $data['technologies'] = $request->technologies_input ? explode(',', $request->technologies_input) : null;
        if ($request->hasFile('thumbnail')) {
            if ($project->thumbnail) Storage::disk('public')->delete($project->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('projects', 'public');
        }
        $project->update($data);
        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        if ($project->thumbnail) Storage::disk('public')->delete($project->thumbnail);
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    public function toggleStatus($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['is_active' => !$project->is_active]);
        return response()->json(['success' => true]);
    }
}
