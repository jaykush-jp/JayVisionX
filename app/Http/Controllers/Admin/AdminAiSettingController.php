<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\AuditLog;
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
            'keyIsSet'    => (bool) $settings->api_key,
            'activeKeyOk' => $ai->isConfigured($settings),
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
        ]);

        $settings = AiSetting::current();
        $oldProvider = $settings->provider;
        $newProvider = $data['provider'] ?? 'anthropic';

        // API key: a new value overwrites; blank keeps the saved key — except when
        // the provider changed, where a blank key is cleared (it was for the old one).
        if (! empty($data['api_key'])) {
            $settings->api_key = trim($data['api_key']);
        } elseif ($newProvider !== $oldProvider) {
            $settings->api_key = null;
        }

        $settings->fill([
            'provider'             => $newProvider,
            'model'                => $data['model'] ?? null,
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

        AuditLog::record('updated', 'ai_setting', $settings->id, ['is_enabled' => $settings->is_enabled, 'model' => $settings->model]);

        return back()->with('success', 'AI configuration saved.');
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
