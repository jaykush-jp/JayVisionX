<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Project;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Testimonial;
use App\Models\Newsletter;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'services'      => Service::count(),
            'projects'      => Project::count(),
            'blogs'         => Blog::count(),
            'contacts'      => Contact::count(),
            'unread'        => Contact::where('is_read', false)->count(),
            'testimonials'  => Testimonial::count(),
            'newsletters'   => Newsletter::count(),
        ];

        $recentContacts = Contact::latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentContacts'));
    }
}
