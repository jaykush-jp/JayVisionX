<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Page;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::getPage('projects');

        // Distinct project categories (for the filter tabs).
        $categories = Project::where('is_active', true)
            ->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category');

        $activeCat = $request->get('category');
        $query = Project::active();
        if ($activeCat && $categories->contains($activeCat)) {
            $query->where('category', $activeCat);
        } else {
            $activeCat = null;
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('frontend.projects.index', compact('page', 'projects', 'categories', 'activeCat'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related = Project::active()->where('id', '!=', $project->id)->take(3)->get();
        return view('frontend.projects.show', compact('project', 'related'));
    }
}
