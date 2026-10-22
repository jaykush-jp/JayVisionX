<?php

namespace App\Services;

use App\Mail\NewLeadMail;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a new lead to email + Google Sheets. Every channel is wrapped so a
 * failure (bad SMTP creds, sheet down) never breaks the visitor's submission —
 * the lead is already saved in the database before this runs.
 */
class LeadNotifier
{
    public function dispatch(Contact $lead): void
    {
        $this->sendEmail($lead);
        $this->pushToGoogleSheet($lead);
    }

    private function sendEmail(Contact $lead): void
    {
        $to = config('services.leads.notify_email')
            ?: Setting::get('site_email', config('mail.from.address'));

        if (! $to) {
            return; // no recipient configured
        }

        try {
            Mail::to($to)->send(new NewLeadMail($lead));
        } catch (\Throwable $e) {
            Log::warning('Lead email notification failed: ' . $e->getMessage());
        }
    }

    private function pushToGoogleSheet(Contact $lead): void
    {
        $url = config('services.google_sheets.webhook_url');

        if (! $url) {
            return; // integration not configured
        }

        try {
            Http::timeout(8)->asForm()->post($url, [
                'name'               => $lead->name,
                'phone'              => $lead->phone,
                'email'              => $lead->email,
                'service_interested' => $lead->service_interested,
                'budget'             => $lead->budget,
                'message'            => $lead->message,
                'ip_address'         => $lead->ip_address,
                'submitted_at'       => $lead->created_at->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Lead Google Sheet push failed: ' . $e->getMessage());
        }
    }
}
