<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('frontend.legal.privacy');
    }

    public function terms()
    {
        return view('frontend.legal.terms');
    }
}
