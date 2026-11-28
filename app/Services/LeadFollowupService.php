<?php

namespace App\Services;

use App\Mail\LeadReplyMail;
use App\Models\AiSetting;
use App\Models\Contact;
use App\Models\LeadEmail;
use App\Models\LeadNote;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends AI-written follow-up emails to open leads on a cadence set by the admin
 * in AI Configuration. Works even without an AI key by falling back to warm,
 * templated messages. Every send is logged to the lead's timeline (LeadEmail +
 * LeadNote) exactly like a manual reply.
 */
class LeadFollowupService
{
    public function __construct(private AiProviderService $ai) {}

    public function enabled(): bool
    {
        return (bool) Setting::get('ai_followup_enabled');
    }

    public function maxFollowups(): int
    {
        return max(1, (int) (Setting::get('ai_followup_max') ?: 3));
    }

    public function gapHours(): int
    {
        return max(1, (int) (Setting::get('ai_followup_gap_hours') ?: 48));
    }

    /** Send the next follow-up for a lead and advance its schedule. */
    public function sendFollowup(Contact $lead): void
    {
        $n       = (int) $lead->ai_followups_sent + 1;
        $service = $lead->service_interested ?: 'our services';
        $subject = $this->subjectFor($n, $service);
        $body    = $this->aiBody($lead, $n, $service) ?: $this->templateBody($lead, $n, $service);

        $status = 'sent';
        try {
            Mail::to($lead->email)->send(new LeadReplyMail($subject, $body, $lead->name));
        } catch (\Throwable $e) {
            $status = 'failed';
            Log::warning('AI follow-up email failed for lead #' . $lead->id . ': ' . $e->getMessage());
        }

        LeadEmail::create([
            'contact_id' => $lead->id,
            'subject'    => $subject,
            'body'       => $body,
            'sent_by'    => 'AI Auto Follow-up',
            'status'     => $status,
        ]);
        LeadNote::create([
            'contact_id' => $lead->id,
            'type'       => 'email',
            'body'       => "AI follow-up #{$n} " . ($status === 'sent' ? 'sent' : 'failed') . ": {$subject}",
            'created_by' => 'AI',
        ]);

        $max = $this->maxFollowups();
        $lead->forceFill([
            'ai_followups_sent' => $n,
            'ai_followup_at'    => $n < $max ? now()->addHours($this->gapHours()) : null,
            'last_contacted_at' => now(),
            'status'            => $lead->status === 'new' ? 'contacted' : $lead->status,
        ])->save();
    }

    private function subjectFor(int $n, string $service): string
    {
        $biz = Setting::get('site_name', 'JayVisionX');
        return match ($n) {
            1       => "Quick follow-up on your {$service} enquiry — {$biz}",
            2       => "Still happy to help with {$service}",
            default => "One more note about your {$service} enquiry",
        };
    }

    /** Ask the configured AI provider for a short, warm follow-up email body. */
    private function aiBody(Contact $lead, int $n, string $service): ?string
    {
        $settings = AiSetting::current();
        if (! $this->ai->isConfigured($settings)) {
            return null;
        }

        $biz   = Setting::get('site_name', 'JayVisionX');
        $phone = Setting::get('whatsapp_number') ?: Setting::get('contact_phone');
        $email = Setting::get('site_email', 'info@jayvisionx.com');
        $extra = trim((string) Setting::get('ai_followup_instructions'));

        $system = "You are a polite sales assistant for {$biz}, a digital marketing & web development agency in Delhi NCR. "
            . "Write a SHORT follow-up email body (plain text, no subject line, under 110 words) to a lead. "
            . "Be warm and human, reference their interest, give ONE clear call to action (reply to this email"
            . ($phone ? ", or WhatsApp/call {$phone}" : '') . "). "
            . "Do not use markdown, asterisks or placeholders. Sign off as the {$biz} team. "
            . ($extra ? "Extra instructions: {$extra}. " : '')
            . ($settings->business_description ? "About us: {$settings->business_description}" : '');

        $prompt = "Lead name: " . ($lead->name ?: 'there') . "\n"
            . "Interested in: {$service}\n"
            . ($lead->message ? "Their message: {$lead->message}\n" : '')
            . "This is follow-up number {$n} of {$this->maxFollowups()}. "
            . ($n === 1 ? "Thank them for reaching out and offer help." : "They haven't replied yet; gently nudge without being pushy.")
            . " Write only the email body.";

        $text = $this->ai->ask($settings, $system, [['role' => 'user', 'content' => $prompt]]);

        return $text ? trim($text) : null;
    }

    /** Warm fallback used when no AI key is configured or the call fails. */
    private function templateBody(Contact $lead, int $n, string $service): string
    {
        $biz   = Setting::get('site_name', 'JayVisionX');
        $phone = Setting::get('whatsapp_number') ?: Setting::get('contact_phone');
        $name  = $lead->name ?: 'there';
        $cta   = $phone ? "reply to this email or WhatsApp/call us at {$phone}" : 'simply reply to this email';

        $intro = match ($n) {
            1       => "Thank you for reaching out to {$biz} about {$service}. We'd love to help you grow.",
            2       => "Just following up on your enquiry about {$service} — we're still here and happy to help.",
            default => "We don't want you to miss out on growing your business with {$service}.",
        };

        return "Hi {$name},\n\n{$intro}\n\n"
            . "If you have a few minutes, {$cta} and we'll share ideas and a clear plan tailored to your business — no obligation.\n\n"
            . "Looking forward to hearing from you.\n\nWarm regards,\nThe {$biz} Team";
    }
}
