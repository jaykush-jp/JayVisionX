<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;

class CaseStudyController extends Controller
{
    public function index()
    {
        $caseStudies = CaseStudy::active()->paginate(9);
        return view('frontend.our-work.index', compact('caseStudies'));
    }

    public function show($slug)
    {
        $caseStudy = CaseStudy::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related   = CaseStudy::active()->where('id', '!=', $caseStudy->id)->take(3)->get();
        return view('frontend.our-work.show', compact('caseStudy', 'related'));
    }
}
