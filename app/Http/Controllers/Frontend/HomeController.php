<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        $page         = Page::getPage('home');
        $services     = Service::active()->where('show_on_home', true)->take(9)->get();
        $projects     = Project::active()->where('show_on_home', true)->take(6)->get();
        $blogs        = Blog::active()->take(3)->get();
        $testimonials = Testimonial::active()->get();
        $faqs         = Faq::active()->take(8)->get();

        return view('frontend.home.index', compact(
            'page', 'services', 'projects', 'blogs', 'testimonials', 'faqs'
        ));
    }
}
