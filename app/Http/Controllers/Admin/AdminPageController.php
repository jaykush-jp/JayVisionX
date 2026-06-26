<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    public function index() { return view('admin.pages.index', ['pages' => Page::all()]); }
    public function create() { return view('admin.pages.create'); }
    public function store(Request $request)
    {
        $request->validate(['page_name' => 'required|unique:pages,page_name']);
        Page::create($request->except('_token'));
        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }
    public function edit(Page $page) { return view('admin.pages.edit', compact('page')); }
    public function update(Request $request, Page $page)
    {
        $page->update($request->except(['_token', '_method']));
        return redirect()->route('admin.pages.index')->with('success', 'Page SEO updated.');
    }
    public function destroy(Page $page) { $page->delete(); return redirect()->route('admin.pages.index')->with('success', 'Page deleted.'); }
    public function show(Page $page) { return view('admin.pages.show', compact('page')); }
}
