<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Page;

class CaseStudyController extends Controller
{
    public function index()
    {
        $page    = Page::getPage('our-work');
        $stories = CaseStudy::active()->get(); // real "Featured Work Stories"

        return view('frontend.our-work.index', compact('page', 'stories'));
    }

    public function show($slug)
    {
        $caseStudy = CaseStudy::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related   = CaseStudy::active()->where('id', '!=', $caseStudy->id)->take(3)->get();
        return view('frontend.our-work.show', compact('caseStudy', 'related'));
    }
}
