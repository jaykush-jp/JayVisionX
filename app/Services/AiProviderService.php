<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\AiUsage;
use App\Models\Credential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * One place to talk to any supported AI provider. The admin picks a provider in
 * AI Configuration and enters its API key + model; this service routes the chat
 * to the right API. DeepSeek, Grok and Mistral all speak the OpenAI chat format,
 * so they share one implementation.
 *
 * Adding a provider = one entry in PROVIDERS (plus an env fallback if wanted).
 */
class AiProviderService
{
    /**
     * type: how to call it — "anthropic" | "gemini" | "openai" (chat-completions).
     * base: API base URL for openai-compatible providers.
     */
    public const PROVIDERS = [
        'anthropic' => ['label' => 'Claude (Anthropic)', 'type' => 'anthropic', 'default_model' => 'claude-haiku-5-5', 'key_hint' => 'console.anthropic.com'],
        'gemini'    => ['label' => 'Google Gemini',      'type' => 'gemini',    'default_model' => 'gemini-3.8-flash', 'key_hint' => 'aistudio.google.com'],
        'openai'    => ['label' => 'OpenAI (GPT)',        'type' => 'openai',    'default_model' => 'gpt-4o-mini',      'base' => 'https://api.openai.com/v1',  'key_hint' => 'platform.openai.com'],
        'deepseek'  => ['label' => 'DeepSeek',            'type' => 'openai',    'default_model' => 'deepseek-chat',    'base' => 'https://api.deepseek.com/v1', 'key_hint' => 'platform.deepseek.com'],
        'grok'      => ['label' => 'Grok (xAI)',          'type' => 'openai',    'default_model' => 'grok-2-latest',    'base' => 'https://api.x.ai/v1',        'key_hint' => 'console.x.ai'],
        'mistral'   => ['label' => 'Mistral',             'type' => 'openai',    'default_model' => 'mistral-small-latest', 'base' => 'https://api.mistral.ai/v1', 'key_hint' => 'console.mistral.ai'],
    ];

    /**
     * The API key for the active provider. Source of truth is the encrypted
     * DB credential store (Admin → API Credentials); falls back to the legacy
     * key saved directly on the ai_settings row. No .env is used.
     */
    public function apiKey(AiSetting $settings): ?string
    {
        $provider = $settings->provider ?: 'anthropic';

        $key = Credential::get("ai.{$provider}.key");
        if (! empty($key)) {
            return $key;
        }

        // Legacy: active-provider key stored on the ai_settings row.
        return ! empty($settings->api_key) ? $settings->api_key : null;
    }

    public function isConfigured(AiSetting $settings): bool
    {
        return (bool) $this->apiKey($settings);
    }

    /**
     * Guzzle "verify" value for outbound HTTPS. Returns an explicit CA-bundle
     * path when configured (needed on local Windows/PHP where php.ini has no
     * curl.cainfo), otherwise true so the OS cert store is used (production).
     */
    private function sslVerify(): string|bool
    {
        $bundle = config('services.ai_ssl.ca_bundle');
        if ($bundle && is_file($bundle)) {
            return $bundle;
        }

        // Auto-detect a CA bundle — needed on local Windows/XAMPP where php.ini
        // has no curl.cainfo. On Linux/production none of these resolve and we
        // return true so the OS cert store is used.
        foreach ($this->caCandidates() as $path) {
            if ($path && is_file($path)) {
                return $path;
            }
        }

        return true;
    }

    /** @return array<int,?string> Possible CA-bundle paths, best first. */
    private function caCandidates(): array
    {
        $paths = [
            ini_get('curl.cainfo') ?: null,
            ini_get('openssl.cafile') ?: null,
        ];

        if (function_exists('openssl_get_cert_locations')) {
            $loc = openssl_get_cert_locations();
            $paths[] = $loc['default_cert_file'] ?? null;
        }

        // Common XAMPP / standalone-PHP bundle locations on Windows.
        $paths[] = 'C:/xampp/apache/bin/curl-ca-bundle.crt';
        $paths[] = 'C:/xampp/php/extras/ssl/cacert.pem';

        return $paths;
    }

    public function providerLabel(?string $provider): string
    {
        return self::PROVIDERS[$provider]['label'] ?? ($provider ?: '—');
    }

    /**
     * Send the conversation and get a reply. $messages is a normalised list of
     * ['role' => 'user'|'assistant', 'content' => '...']. Returns null on any
     * failure so the caller can fall back to the DB/FAQ answer.
     */
    /** Token usage from the last successful call: ['input'=>int,'output'=>int]. */
    private ?array $lastUsage = null;

    /** Human-readable reason the last call failed (for the Test button). */
    private ?string $lastError = null;

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function ask(AiSetting $settings, string $system, array $messages): ?string
    {
        $provider = $settings->provider ?: 'anthropic';
        $def = self::PROVIDERS[$provider] ?? null;
        $key = $this->apiKey($settings);
        $this->lastError = null;

        if (! $def || ! $key || empty($messages)) {
            $this->lastError = ! $key ? 'No API key saved for this provider.' : 'Provider not available.';
            return null;
        }

        $model   = $settings->model ?: $def['default_model'];
        $maxTok  = (int) ($settings->max_response_tokens ?: 1024);
        $this->lastUsage = null;

        try {
            $text = match ($def['type']) {
                'anthropic' => $this->anthropic($key, $model, $system, $messages, $maxTok),
                'gemini'    => $this->gemini($key, $model, $system, $messages, $maxTok),
                'openai'    => $this->openai($def['base'], $key, $model, $system, $messages, $maxTok),
                default     => null,
            };

            if ($text !== null) {
                $this->recordUsage($provider, $model);
            }

            return $text;
        } catch (\Throwable $e) {
            Log::warning("AI provider [{$provider}] failed: " . $e->getMessage());
            $this->lastError = 'Connection error: ' . $e->getMessage();
            return null;
        }
    }

    /** A short, actionable description of a failed provider HTTP response. */
    private function describeError($res): string
    {
        $status = $res->status();
        $msg = $res->json('error.message') ?? $res->json('error') ?? '';
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $hint = match (true) {
            in_array($status, [401, 403], true) => 'API key is invalid, inactive or lacks access.',
            $status === 404                     => 'Model name not found for this provider.',
            $status === 429                     => 'Rate limit or quota exceeded.',
            $status >= 500                      => 'Provider server error — try again shortly.',
            $status === 400                     => 'Bad request — often a wrong model name.',
            default                             => '',
        };
        $this->lastError = trim("HTTP {$status}. {$hint}" . ($msg ? " ({$msg})" : ''));
        return $this->lastError;
    }

    /**
     * Fetch the live list of models the active provider's key can use.
     * Returns model id strings (empty on failure; see lastError()).
     */
    public function listModels(AiSetting $settings): array
    {
        $provider = $settings->provider ?: 'anthropic';
        $def = self::PROVIDERS[$provider] ?? null;
        $key = $this->apiKey($settings);
        $this->lastError = null;

        if (! $def || ! $key) {
            $this->lastError = 'No API key saved for this provider.';
            return [];
        }

        try {
            return match ($def['type']) {
                'gemini'    => $this->geminiModels($key),
                'anthropic' => $this->anthropicModels($key),
                'openai'    => $this->openaiModels($def['base'], $key),
                default     => [],
            };
        } catch (\Throwable $e) {
            $this->lastError = 'Connection error: ' . $e->getMessage();
            return [];
        }
    }

    private function geminiModels(string $key): array
    {
        $res = Http::timeout(20)->withOptions(['verify' => $this->sslVerify()])
            ->get('https://generativelanguage.googleapis.com/v1beta/models', ['key' => $key, 'pageSize' => 200]);
        if (! $res->successful()) {
            $this->describeError($res);
            return [];
        }
        $out = [];
        foreach ($res->json('models', []) as $m) {
            if (in_array('generateContent', $m['supportedGenerationMethods'] ?? [], true)) {
                $out[] = preg_replace('#^models/#', '', $m['name'] ?? '');
            }
        }
        return $this->tidyModels($out);
    }

    private function anthropicModels(string $key): array
    {
        $res = Http::withHeaders(['x-api-key' => $key, 'anthropic-version' => '2023-06-01'])
            ->withOptions(['verify' => $this->sslVerify()])->timeout(20)
            ->get('https://api.anthropic.com/v1/models', ['limit' => 100]);
        if (! $res->successful()) {
            $this->describeError($res);
            return [];
        }
        return $this->tidyModels(array_map(fn ($m) => $m['id'] ?? '', $res->json('data', [])));
    }

    private function openaiModels(string $base, string $key): array
    {
        $res = Http::withToken($key)->withOptions(['verify' => $this->sslVerify()])->timeout(20)
            ->get(rtrim($base, '/') . '/models');
        if (! $res->successful()) {
            $this->describeError($res);
            return [];
        }
        return $this->tidyModels(array_map(fn ($m) => $m['id'] ?? '', $res->json('data', [])));
    }

    private function tidyModels(array $models): array
    {
        $models = array_values(array_filter(array_unique($models)));
        sort($models);
        return $models;
    }

    /** Store an estimated-cost usage row for the last call (best-effort). */
    private function recordUsage(string $provider, string $model): void
    {
        try {
            $in  = (int) ($this->lastUsage['input'] ?? 0);
            $out = (int) ($this->lastUsage['output'] ?? 0);

            // Index by the literal model key (model names contain dots, which
            // config() dot-notation would mis-split), falling back per provider.
            $models = config('ai_pricing.models', []);
            $rates  = $models[$model]
                ?? config('ai_pricing.defaults.' . $provider)
                ?? ['in' => 0, 'out' => 0];

            $cost = ($in / 1_000_000) * ($rates['in'] ?? 0)
                  + ($out / 1_000_000) * ($rates['out'] ?? 0);

            AiUsage::create([
                'provider'      => $provider,
                'model'         => $model,
                'kind'          => 'text',
                'input_tokens'  => $in,
                'output_tokens' => $out,
                'images'        => 0,
                'cost'          => round($cost, 6),
            ]);
        } catch (\Throwable $e) {
            // Usage logging must never break a reply.
        }
    }

    private function anthropic(string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $res = Http::withHeaders([
            'x-api-key'         => $key,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->withOptions(['verify' => $this->sslVerify()])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model'      => $model,
            'max_tokens' => $maxTok,
            'system'     => $system,
            'messages'   => $messages, // already role=user/assistant
        ]);

        if (! $res->successful()) {
            Log::warning('Anthropic API error: ' . $res->status() . ' ' . $res->body());
            $this->describeError($res);
            return null;
        }

        $text = '';
        foreach ($res->json('content', []) as $block) {
            if (($block['type'] ?? null) === 'text') {
                $text .= $block['text'];
            }
        }

        $this->lastUsage = [
            'input'  => (int) $res->json('usage.input_tokens', 0),
            'output' => (int) $res->json('usage.output_tokens', 0),
        ];

        return trim($text) ?: null;
    }

    private function gemini(string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $contents = array_map(fn ($m) => [
            'role'  => $m['role'] === 'user' ? 'user' : 'model',
            'parts' => [['text' => $m['content']]],
        ], $messages);

        $res = Http::timeout(30)
            ->withOptions(['verify' => $this->sslVerify()])
            ->withQueryParameters(['key' => $key])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'system_instruction' => ['parts' => [['text' => $system]]],
                'contents'           => $contents,
                'generationConfig'   => ['maxOutputTokens' => $maxTok, 'temperature' => 0.6],
            ]);

        if (! $res->successful()) {
            Log::warning('Gemini API error: ' . $res->status() . ' ' . $res->body());
            $this->describeError($res);
            return null;
        }

        $text = '';
        foreach ($res->json('candidates.0.content.parts', []) as $part) {
            $text .= $part['text'] ?? '';
        }

        $this->lastUsage = [
            'input'  => (int) $res->json('usageMetadata.promptTokenCount', 0),
            'output' => (int) $res->json('usageMetadata.candidatesTokenCount', 0),
        ];

        return trim($text) ?: null;
    }

    /** OpenAI chat-completions format — also used for DeepSeek, Grok, Mistral. */
    private function openai(string $base, string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $payload = array_merge(
            [['role' => 'system', 'content' => $system]],
            $messages
        );

        $res = Http::withToken($key)->withOptions(['verify' => $this->sslVerify()])->timeout(30)->post(rtrim($base, '/') . '/chat/completions', [
            'model'       => $model,
            'max_tokens'  => $maxTok,
            'temperature' => 0.6,
            'messages'    => $payload,
        ]);

        if (! $res->successful()) {
            Log::warning('OpenAI-compatible API error: ' . $res->status() . ' ' . $res->body());
            $this->describeError($res);
            return null;
        }

        $this->lastUsage = [
            'input'  => (int) $res->json('usage.prompt_tokens', 0),
            'output' => (int) $res->json('usage.completion_tokens', 0),
        ];

        return trim((string) $res->json('choices.0.message.content')) ?: null;
    }
}
