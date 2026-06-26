<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Testimonial;

class AboutController extends Controller
{
    public function index()
    {
        $page         = Page::getPage('about');
        $testimonials = Testimonial::active()->get();
        return view('frontend.about.index', compact('page', 'testimonials'));
    }
}
