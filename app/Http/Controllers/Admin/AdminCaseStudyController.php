<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminCaseStudyController extends Controller
{
    public function index() { return view('admin.case-studies.index', ['caseStudies' => CaseStudy::orderBy('sort_order')->paginate(20)]); }
    public function create() { return view('admin.case-studies.create'); }
    public function store(Request $request)
    {
        $request->validate(['title' => 'required']);
        $data = $request->except(['thumbnail', '_token']);
        $data['slug'] = Str::slug($request->title);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('case-studies', 'public');
        }
        CaseStudy::create($data);
        return redirect()->route('admin.case-studies.index')->with('success', 'Case study created.');
    }
    public function edit(CaseStudy $caseStudy) { return view('admin.case-studies.edit', compact('caseStudy')); }
    public function update(Request $request, CaseStudy $caseStudy)
    {
        $data = $request->except(['thumbnail', '_token', '_method']);
        $data['slug'] = Str::slug($request->title);
        if ($request->hasFile('thumbnail')) {
            if ($caseStudy->thumbnail) Storage::disk('public')->delete($caseStudy->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('case-studies', 'public');
        }
        $caseStudy->update($data);
        return redirect()->route('admin.case-studies.index')->with('success', 'Case study updated.');
    }
    public function destroy(CaseStudy $caseStudy)
    {
        if ($caseStudy->thumbnail) Storage::disk('public')->delete($caseStudy->thumbnail);
        $caseStudy->delete();
        return redirect()->route('admin.case-studies.index')->with('success', 'Case study deleted.');
    }
    public function show(CaseStudy $caseStudy) { return view('admin.case-studies.show', compact('caseStudy')); }
}
