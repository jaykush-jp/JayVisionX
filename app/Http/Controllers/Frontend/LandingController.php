<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;

class LandingController extends Controller
{
    public function index()
    {
        $services     = Service::active()->get();
        $projects     = Project::active()->take(6)->get();
        $testimonials = Testimonial::active()->get();
        $faqs         = Faq::active()->take(8)->get();
        $blogs        = Blog::active()->take(3)->get();

        return view('frontend.landing.index', compact(
            'services', 'projects', 'testimonials', 'faqs', 'blogs'
        ));
    }
}
