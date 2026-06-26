<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Page;

class ProjectController extends Controller
{
    public function index()
    {
        $page     = Page::getPage('projects');
        $projects = Project::active()->paginate(12);
        return view('frontend.projects.index', compact('page', 'projects'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related = Project::active()->where('id', '!=', $project->id)->take(3)->get();
        return view('frontend.projects.show', compact('project', 'related'));
    }
}
