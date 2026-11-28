<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\LeadFollowupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendLeadFollowups extends Command
{
    protected $signature = 'leads:ai-followup';

    protected $description = 'Send AI-written follow-up emails to open leads that are due';

    public function handle(LeadFollowupService $svc): int
    {
        if (! $svc->enabled()) {
            $this->info('AI lead follow-up is disabled.');
            return self::SUCCESS;
        }

        $max = $svc->maxFollowups();

        $leads = Contact::query()
            ->whereNotNull('ai_followup_at')
            ->where('ai_followup_at', '<=', now())
            ->where('ai_followup_opt_out', false)
            ->whereNotIn('status', ['won', 'lost'])
            ->whereNotNull('email')->where('email', '!=', '')
            ->where('ai_followups_sent', '<', $max)
            ->orderBy('ai_followup_at')
            ->limit(25)
            ->get();

        if ($leads->isEmpty()) {
            $this->info('No leads are due for follow-up.');
            return self::SUCCESS;
        }

        foreach ($leads as $lead) {
            try {
                $svc->sendFollowup($lead);
                $this->info("Lead #{$lead->id}: follow-up #{$lead->ai_followups_sent} sent.");
            } catch (\Throwable $e) {
                Log::warning("AI follow-up for lead #{$lead->id} failed: " . $e->getMessage());
                $this->error("Lead #{$lead->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
