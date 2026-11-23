<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        // Distinct work categories (for the filter tabs).
        $categories = CaseStudy::where('is_active', true)
            ->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category');

        $activeCat = $request->get('category');
        $query = CaseStudy::active();
        if ($activeCat && $categories->contains($activeCat)) {
            $query->where('category', $activeCat);
        } else {
            $activeCat = null;
        }

        $caseStudies = $query->paginate(9)->withQueryString();

        return view('frontend.our-work.index', compact('caseStudies', 'categories', 'activeCat'));
    }

    public function show($slug)
    {
        $caseStudy = CaseStudy::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related   = CaseStudy::active()->where('id', '!=', $caseStudy->id)->take(3)->get();
        return view('frontend.our-work.show', compact('caseStudy', 'related'));
    }
}
