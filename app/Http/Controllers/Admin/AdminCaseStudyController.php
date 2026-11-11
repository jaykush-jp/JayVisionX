<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminCaseStudyController extends Controller
{
    public function index() { return view('admin.case-studies.index', ['caseStudies' => CaseStudy::orderBy('sort_order')->orderByDesc('id')->paginate(20)]); }
    public function create() { return view('admin.case-studies.create'); }
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:3072'], [
            'thumbnail.max'      => 'Image ka size bahut bada hai (max 3 MB). Chhoti image use karein.',
            'thumbnail.image'    => 'File ek valid image honi chahiye (jpg, png, webp, gif).',
            'thumbnail.uploaded' => 'Image upload nahi ho payi — size 3 MB se bada hai. Chhoti image use karein.',
        ]);
        $data = $request->except(['thumbnail', '_token', 'stats']);
        $data['slug'] = Str::slug($request->title);
        $data['stats'] = $this->cleanStats($request);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('case-studies', 'public');
        }
        CaseStudy::create($data);
        return redirect()->route('admin.case-studies.index')->with('success', 'Case study created.');
    }
    public function edit(CaseStudy $caseStudy) { return view('admin.case-studies.edit', compact('caseStudy')); }
    public function update(Request $request, CaseStudy $caseStudy)
    {
        $request->validate(['title' => 'required|string|max:200', 'thumbnail' => 'nullable|image|max:3072'], [
            'thumbnail.max'      => 'Image ka size bahut bada hai (max 3 MB). Chhoti image use karein.',
            'thumbnail.image'    => 'File ek valid image honi chahiye (jpg, png, webp, gif).',
            'thumbnail.uploaded' => 'Image upload nahi ho payi — size 3 MB se bada hai. Chhoti image use karein.',
        ]);
        $data = $request->except(['thumbnail', '_token', '_method', 'stats']);
        $data['slug'] = Str::slug($request->title);
        $data['stats'] = $this->cleanStats($request);
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

    /**
     * Build a clean metrics array from the repeater fields (value + label),
     * dropping empty rows. Returns null when there are no metrics, so the
     * stored value is always a proper array|null — never a stray string.
     */
    private function cleanStats(Request $request): ?array
    {
        $stats = [];
        foreach ((array) $request->input('stats', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $value = trim((string) ($row['value'] ?? ''));
            $label = trim((string) ($row['label'] ?? ''));
            if ($value !== '' || $label !== '') {
                $stats[] = ['value' => $value, 'label' => $label];
            }
        }

        return $stats ?: null;
    }
}
