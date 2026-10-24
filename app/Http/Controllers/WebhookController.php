<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\AutomationEngine;
use App\Services\LeadNotifier;
use App\Services\MetaGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Inbound lead webhooks — Meta Lead Ads (Facebook/Instagram) and Google Ads
 * lead-form extensions. Each verified lead becomes a CRM Contact so the admin
 * can manage it in Leads, tagged by source (meta / google).
 */
class WebhookController extends Controller
{
    /** Meta webhook verification handshake (GET). */
    public function metaVerify(Request $request)
    {
        $verify = config('services.meta.lead_verify_token');

        if ($request->get('hub_mode') === 'subscribe'
            && $verify
            && hash_equals((string) $verify, (string) $request->get('hub_verify_token'))) {
            return response($request->get('hub_challenge'), 200);
        }

        return response('Verification failed', 403);
    }

    /** Meta Lead Ads notification (POST) — fetch each lead and store it. */
    public function metaLeads(Request $request, MetaGraphService $meta)
    {
        foreach ($request->input('entry', []) as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                if (($change['field'] ?? null) !== 'leadgen') {
                    continue;
                }
                $leadgenId = $change['value']['leadgen_id'] ?? null;
                if (! $leadgenId) {
                    continue;
                }

                $lead = $meta->fetchLead($leadgenId);
                if ($lead && ($lead['email'] || $lead['phone'])) {
                    $this->ingest(
                        'meta',
                        $lead['name'] ?: 'Facebook/Instagram lead',
                        $lead['email'],
                        $lead['phone'],
                        'Lead from Meta Lead Ads (form ' . ($change['value']['form_id'] ?? '—') . ').',
                        $request->ip()
                    );
                }
            }
        }

        // Meta expects a fast 200 acknowledgement.
        return response('EVENT_RECEIVED', 200);
    }

    /** Google Ads lead-form webhook (POST). */
    public function googleLeads(Request $request)
    {
        $key = config('services.google.lead_webhook_key');
        if ($key && ! hash_equals((string) $key, (string) $request->input('google_key'))) {
            return response()->json(['status' => 'unauthorized'], 403);
        }

        // Google posts user_column_data: [{column_id, string_value}, ...]
        $fields = [];
        foreach ($request->input('user_column_data', []) as $col) {
            $fields[strtoupper($col['column_id'] ?? '')] = $col['string_value'] ?? null;
        }

        $name  = $fields['FULL_NAME'] ?? trim(($fields['FIRST_NAME'] ?? '') . ' ' . ($fields['LAST_NAME'] ?? '')) ?: 'Google Ads lead';
        $email = $fields['EMAIL'] ?? null;
        $phone = $fields['PHONE_NUMBER'] ?? null;

        if ($email || $phone) {
            $this->ingest('google', $name, $email, $phone, 'Lead from Google Ads lead form.', $request->ip());
        }

        return response()->json(['status' => 'ok']);
    }

    /** Create (or reuse) a Contact lead and fire notifications + automation. */
    private function ingest(string $source, string $name, ?string $email, ?string $phone, string $message, ?string $ip): void
    {
        // Dedupe: same source + email/phone within the last day.
        $existing = Contact::where('source', $source)
            ->where(function ($w) use ($email, $phone) {
                if ($email) $w->orWhere('email', $email);
                if ($phone) $w->orWhere('phone', $phone);
            })
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existing) {
            return;
        }

        $lead = Contact::create([
            'name'       => $name,
            'email'      => $email ?: ($phone . '@no-email.local'),
            'phone'      => $phone,
            'message'    => $message,
            'source'     => $source,
            'status'     => 'new',
            'is_read'    => false,
            'ip_address' => $ip,
        ]);

        try {
            app(LeadNotifier::class)->dispatch($lead);
            app(AutomationEngine::class)->run('lead_created', [
                'summary' => "New {$source} lead: {$lead->name} ({$lead->email})",
                'data'    => ['lead_id' => $lead->id, 'source' => $source],
            ]);
        } catch (\Throwable $e) {
            Log::warning("Lead ingest post-processing failed: " . $e->getMessage());
        }
    }
}
