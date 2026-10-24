<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\QuoteRequest;
use App\Services\QuoteBuilder;
use Illuminate\Http\Request;

class AdminQuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = QuoteRequest::with('contact')->latest();
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.quotes.index', [
            'quotes'   => $query->paginate(20)->withQueryString(),
            'statuses' => QuoteRequest::STATUSES,
            'builder'  => null,
        ]);
    }

    public function show(QuoteRequest $quote)
    {
        return view('admin.quotes.show', [
            'quote'    => $quote->load('contact', 'session'),
            'statuses' => QuoteRequest::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, QuoteRequest $quote)
    {
        $request->validate([
            'status'      => 'required|in:' . implode(',', array_keys(QuoteRequest::STATUSES)),
            'admin_notes' => 'nullable|string',
        ]);
        $quote->update($request->only('status', 'admin_notes'));

        return back()->with('success', 'Quote updated.');
    }

    /** Interactive quote builder — deterministic estimate from approved packages. */
    public function build(Request $request, QuoteBuilder $qb)
    {
        $projectTypes = QuoteRequest::PROJECT_TYPES;
        $estimate = null;

        if ($request->filled('project_type')) {
            $request->validate(['project_type' => 'in:' . implode(',', array_keys($projectTypes))]);
            $estimate = $qb->estimate($request->project_type);
        }

        return view('admin.quotes.build', [
            'projectTypes' => $projectTypes,
            'estimate'     => $estimate,
            'selected'     => $request->project_type,
            'packages'     => Package::active()->with('service')->get(),
        ]);
    }
}
