<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\AuditLog;
use App\Models\Credential;
use App\Models\Setting;
use App\Services\AiProviderService;
use Illuminate\Http\Request;

class AdminAiSettingController extends Controller
{
    public const RESPONSE_STYLES = [
        'friendly'     => 'Friendly',
        'professional' => 'Professional',
        'concise'      => 'Concise',
        'detailed'     => 'Detailed',
    ];

    public const LANGUAGES = [
        'en'       => 'English',
        'hi'       => 'Hindi',
        'hinglish' => 'Hinglish',
    ];

    public function index(AiProviderService $ai)
    {
        $settings = AiSetting::current();

        return view('admin.ai-settings.index', [
            'settings'    => $settings,
            'styles'      => self::RESPONSE_STYLES,
            'languages'   => self::LANGUAGES,
            'providers'   => AiProviderService::PROVIDERS,
            'keyIsSet'    => Credential::has("ai.{$settings->provider}.key") || (bool) $settings->api_key,
            'activeKeyOk' => $ai->isConfigured($settings),
            'followup'    => [
                'enabled'      => (bool) Setting::get('ai_followup_enabled'),
                'max'          => (int) (Setting::get('ai_followup_max') ?: 3),
                'gap_hours'    => (int) (Setting::get('ai_followup_gap_hours') ?: 48),
                'instructions' => (string) Setting::get('ai_followup_instructions'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'provider'             => 'nullable|in:' . implode(',', array_keys(AiProviderService::PROVIDERS)),
            'api_key'              => 'nullable|string|max:500',
            'model'                => 'nullable|string|max:100',
            'business_description' => 'nullable|string|max:2000',
            'system_prompt'        => 'nullable|string|max:5000',
            'welcome_message'      => 'nullable|string|max:1000',
            'suggested_questions'  => 'nullable|string',           // newline-separated
            'languages'            => 'nullable|array',
            'languages.*'          => 'string|max:20',
            'response_style'       => 'nullable|in:' . implode(',', array_keys(self::RESPONSE_STYLES)),
            'max_response_tokens'  => 'nullable|integer|min:128|max:8192',
            'confidence_threshold' => 'nullable|numeric|min:0|max:1',
            'fallback_message'     => 'nullable|string|max:1000',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:120',
            'daily_message_limit'  => 'nullable|integer|min:0',
            'monthly_cost_cap'     => 'nullable|numeric|min:0',
            'ai_followup_max'      => 'nullable|integer|min:1|max:10',
            'ai_followup_gap_hours' => 'nullable|integer|min:1|max:720',
            'ai_followup_instructions' => 'nullable|string|max:1000',
        ]);

        $settings = AiSetting::current();
        $newProvider = $data['provider'] ?? 'anthropic';

        // API key lives in the encrypted credential store, one per provider
        // (Admin → API Credentials manages them all). A new value here saves the
        // active provider's key; blank keeps whatever is already stored for it.
        if (! empty($data['api_key'])) {
            Credential::set("ai.{$newProvider}.key", trim($data['api_key']));
            $settings->api_key = null; // legacy column no longer used for reads
        }

        // Blank model = use the provider's current default (the column is NOT
        // NULL, and a blank box submits as null, so resolve it here).
        $model = trim((string) ($data['model'] ?? ''));
        if ($model === '') {
            $model = AiProviderService::PROVIDERS[$newProvider]['default_model'] ?? 'gpt-4o-mini';
        }

        $settings->fill([
            'provider'             => $newProvider,
            'model'                => $model,
            'business_description' => $data['business_description'] ?? null,
            'system_prompt'        => $data['system_prompt'] ?? null,
            'welcome_message'      => $data['welcome_message'] ?? null,
            'suggested_questions'  => $this->linesToArray($request->input('suggested_questions')),
            'languages'            => $data['languages'] ?? ['en', 'hi', 'hinglish'],
            'response_style'       => $data['response_style'] ?? 'friendly',
            'max_response_tokens'  => $data['max_response_tokens'] ?? 1024,
            'confidence_threshold' => $data['confidence_threshold'] ?? null,
            'fallback_message'     => $data['fallback_message'] ?? null,
            'is_enabled'           => $request->boolean('is_enabled'),
            'rate_limit_per_minute' => $data['rate_limit_per_minute'] ?? 10,
            'daily_message_limit'  => $data['daily_message_limit'] ?? null,
            'monthly_cost_cap'     => $data['monthly_cost_cap'] ?? null,
        ])->save();

        // Lead AI auto follow-up settings (stored in the settings table).
        Setting::set('ai_followup_enabled', $request->boolean('ai_followup_enabled') ? '1' : '');
        Setting::set('ai_followup_max', (string) ($data['ai_followup_max'] ?? 3));
        Setting::set('ai_followup_gap_hours', (string) ($data['ai_followup_gap_hours'] ?? 48));
        Setting::set('ai_followup_instructions', trim((string) ($data['ai_followup_instructions'] ?? '')));

        AuditLog::record('updated', 'ai_setting', $settings->id, ['is_enabled' => $settings->is_enabled, 'model' => $settings->model]);

        return back()->with('success', 'AI configuration saved.');
    }

    /**
     * Live check that the selected provider's saved API key actually works —
     * sends a tiny request using the provider's default model and reports back.
     */
    public function test(AiProviderService $ai)
    {
        // Test the SAVED configuration exactly as the chatbot uses it — same
        // provider, model and key — so the result reflects the real setup.
        $settings = AiSetting::current();
        $provider = $settings->provider ?: 'anthropic';
        $def = AiProviderService::PROVIDERS[$provider] ?? null;

        if (! $def) {
            return response()->json(['ok' => false, 'message' => 'Unknown provider selected.']);
        }
        if (! $ai->isConfigured($settings)) {
            return response()->json([
                'ok'      => false,
                'message' => "No API key saved for {$def['label']}. Add the key and Save first.",
            ]);
        }

        $settings->max_response_tokens = 32; // cheap, fast key/model check (not saved)

        $reply = $ai->ask($settings, 'You are a connectivity test. Reply with exactly: OK', [
            ['role' => 'user', 'content' => 'Reply with OK.'],
        ]);

        if ($reply !== null && trim($reply) !== '') {
            return response()->json([
                'ok'       => true,
                'message'  => 'Connection working',
                'provider' => $def['label'],
                'model'    => $settings->model ?: $def['default_model'],
                'reply'    => mb_substr(trim($reply), 0, 80),
            ]);
        }

        return response()->json([
            'ok'      => false,
            'message' => 'No response from ' . $def['label'] . '. Check the API key is valid & active, and that the Model name ("' . ($settings->model ?: $def['default_model']) . '") is correct.',
        ]);
    }

    private function linesToArray(?string $raw): ?array
    {
        if (! $raw) {
            return null;
        }
        $items = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $raw))));
        return $items ?: null;
    }
}
