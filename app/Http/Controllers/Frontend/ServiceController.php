<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Page;

class ServiceController extends Controller
{
    public function index()
    {
        $page     = Page::getPage('services');
        $services = Service::active()->get();
        return view('frontend.services.index', compact('page', 'services'));
    }

    public function show($slug)
    {
        $service  = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $related  = Service::active()->where('id', '!=', $service->id)->take(4)->get();
        return view('frontend.services.show', compact('service', 'related'));
    }
}
