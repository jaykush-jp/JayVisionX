<?php

use App\Models\AiSetting;
use App\Models\Credential;
use Illuminate\Database\Migrations\Migration;

/**
 * One-time move of existing API credentials into the encrypted `credentials`
 * table so nothing breaks when the keys are removed from .env. Idempotent: a
 * key already present in the store is left untouched. Values are written through
 * the Credential model, so they are stored encrypted.
 */
return new class extends Migration
{
    public function up(): void
    {
        // AI: the active provider's key is currently stored (encrypted) on the
        // ai_settings row — move it to ai.<provider>.key.
        $ai = AiSetting::first();
        if ($ai && ! empty($ai->api_key)) {
            $provider = $ai->provider ?: 'anthropic';
            if (! Credential::has("ai.{$provider}.key")) {
                Credential::set("ai.{$provider}.key", $ai->api_key);
            }
        }

        // Anything still provided via config/.env.
        $map = [
            'ai.anthropic.key' => config('services.anthropic.key'),
            'ai.gemini.key'    => config('services.gemini.key'),
            'ai.openai.key'    => config('services.openai.key'),
            'ai.deepseek.key'  => config('services.deepseek.key'),
            'ai.grok.key'      => config('services.grok.key'),
            'ai.mistral.key'   => config('services.mistral.key'),

            'google.client_id'          => config('services.google.client_id'),
            'google.client_secret'      => config('services.google.client_secret'),
            'google.scopes'             => config('services.google.scopes'),
            'google.sheets_webhook_url' => config('services.google_sheets.webhook_url'),
            'google.lead_webhook_key'   => config('services.google.lead_webhook_key'),

            'meta.app_id'            => config('services.meta.app_id'),
            'meta.app_secret'        => config('services.meta.app_secret'),
            'meta.graph_version'     => config('services.meta.graph_version'),
            'meta.scopes'            => config('services.meta.scopes'),
            'meta.page_access_token' => config('services.meta.page_access_token'),
            'meta.lead_verify_token' => config('services.meta.lead_verify_token'),
        ];

        foreach ($map as $key => $value) {
            if ($value !== null && $value !== '' && ! Credential::has($key)) {
                Credential::set($key, (string) $value);
            }
        }
    }

    public function down(): void
    {
        // No-op: keep the migrated credentials on rollback to avoid data loss.
    }
};
