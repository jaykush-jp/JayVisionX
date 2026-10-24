<?php

namespace App\Services;

use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Runs the admin-configured automation rules for a trigger and records an
 * execution log. Every action is failure-tolerant — a broken rule never
 * breaks the visitor's request.
 */
class AutomationEngine
{
    /**
     * @param array{session?:ChatSession, summary?:string, data?:array} $context
     */
    public function run(string $trigger, array $context = []): void
    {
        $rules = AutomationRule::active()->where('trigger', $trigger)->get();

        if ($rules->isEmpty()) {
            // Still log the event itself so the Reports/overview have signal.
            $this->log(null, $trigger, $context['summary'] ?? $trigger, $context);
            return;
        }

        foreach ($rules as $rule) {
            try {
                $this->execute($rule, $context);
                $this->log($rule->id, $trigger, $rule->actionLabel(), $context);
            } catch (\Throwable $e) {
                Log::warning("Automation rule #{$rule->id} failed: " . $e->getMessage());
            }
        }
    }

    private function execute(AutomationRule $rule, array $context): void
    {
        $session = $context['session'] ?? null;

        switch ($rule->action) {
            case 'notify_admin':
                $this->notify($rule, $context);
                break;

            case 'auto_message':
                if ($session instanceof ChatSession && $rule->message) {
                    ChatMessage::create([
                        'chat_session_id' => $session->id,
                        'role'            => 'assistant',
                        'content'         => $rule->message,
                        'meta'            => ['automation' => $rule->id],
                    ]);
                }
                break;

            case 'set_pending':
                if ($session instanceof ChatSession) {
                    $session->update(['status' => 'pending']);
                }
                break;
        }
    }

    private function notify(AutomationRule $rule, array $context): void
    {
        $to = $rule->notify_email
            ?: config('services.leads.notify_email')
            ?: Setting::get('site_email', config('mail.from.address'));

        if (! $to) {
            return;
        }

        $summary = $context['summary'] ?? $rule->triggerLabel();
        $body = ($rule->message ? $rule->message . "\n\n" : '') . $summary;

        try {
            Mail::raw($body, function ($m) use ($to, $rule) {
                $m->to($to)->subject('[JayVisionX] ' . $rule->triggerLabel());
            });
        } catch (\Throwable $e) {
            Log::warning('Automation notify failed: ' . $e->getMessage());
        }
    }

    private function log(?int $ruleId, string $trigger, ?string $summary, array $context): void
    {
        // Don't store model instances in the JSON context.
        $ctx = $context;
        unset($ctx['session']);

        AutomationLog::create([
            'automation_rule_id' => $ruleId,
            'trigger'            => $trigger,
            'summary'            => $summary,
            'context'            => $ctx ?: null,
        ]);
    }
}
