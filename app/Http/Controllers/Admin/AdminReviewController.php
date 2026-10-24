<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleBusinessProfileService;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(GoogleBusinessProfileService $gbp)
    {
        $connected = $gbp->isConnected();
        $reviews = $gbp->listReviews();

        return view('admin.reviews.index', compact('connected', 'reviews'));
    }

    public function reply(Request $request, string $reviewId, GoogleBusinessProfileService $gbp)
    {
        $request->validate(['comment' => 'required|string']);

        $ok = $gbp->replyToReview($reviewId, $request->comment);

        return back()->with(
            $ok ? 'success' : 'error',
            $ok ? 'Reply posted to Google.' : 'Could not post reply — connect Google Business Profile first.'
        );
    }
}
