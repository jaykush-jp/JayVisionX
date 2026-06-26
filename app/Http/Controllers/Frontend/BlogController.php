<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Page;

class BlogController extends Controller
{
    public function index()
    {
        $page  = Page::getPage('blog');
        $blogs = Blog::active()->paginate(9);
        return view('frontend.blog.index', compact('page', 'blogs'));
    }

    public function show($slug)
    {
        $blog    = Blog::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $blog->increment('views');
        $related = Blog::active()->where('id', '!=', $blog->id)->take(3)->get();
        return view('frontend.blog.show', compact('blog', 'related'));
    }
}
