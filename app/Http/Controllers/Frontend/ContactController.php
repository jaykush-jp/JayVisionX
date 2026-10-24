<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\Service;
use App\Services\LeadNotifier;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $page     = Page::getPage('contact');
        $services = Service::active()->get();
        return view('frontend.contact.index', compact('page', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'phone'   => 'nullable|string|max:20',
            'message' => 'nullable|string|max:2000',
        ]);

        // Where the lead came from — a hidden form field, limited to known values.
        $source = in_array($request->input('source'), ['contact_form', 'landing'], true)
            ? $request->input('source')
            : 'contact_form';

        $lead = Contact::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'service_interested' => $request->service_interested,
            'budget'             => $request->budget,
            'message'            => $request->message,
            'ip_address'         => $request->ip(),
            'source'             => $source,
            'status'             => 'new',
        ]);

        // Notify via email + push to Google Sheet (both are failure-tolerant).
        app(LeadNotifier::class)->dispatch($lead);

        return back()->with('success', 'Thank you! Your enquiry has been submitted. Our team will contact you within 24 hours.');
    }

    public function newsletter(Request $request)
    {
        $request->validate(['email' => 'required|email|max:100']);

        Newsletter::updateOrCreate(
            ['email' => $request->email],
            ['is_active' => true]
        );

        return back()->with('newsletter_success', 'You have successfully subscribed to our newsletter!');
    }
}
