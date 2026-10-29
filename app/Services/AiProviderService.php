<?php

namespace App\Services;

use App\Models\AiSetting;
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
        'gemini'    => ['label' => 'Google Gemini',      'type' => 'gemini',    'default_model' => 'gemini-2.0-flash', 'key_hint' => 'aistudio.google.com'],
        'openai'    => ['label' => 'OpenAI (GPT)',        'type' => 'openai',    'default_model' => 'gpt-4o-mini',      'base' => 'https://api.openai.com/v1',  'key_hint' => 'platform.openai.com'],
        'deepseek'  => ['label' => 'DeepSeek',            'type' => 'openai',    'default_model' => 'deepseek-chat',    'base' => 'https://api.deepseek.com/v1', 'key_hint' => 'platform.deepseek.com'],
        'grok'      => ['label' => 'Grok (xAI)',          'type' => 'openai',    'default_model' => 'grok-2-latest',    'base' => 'https://api.x.ai/v1',        'key_hint' => 'console.x.ai'],
        'mistral'   => ['label' => 'Mistral',             'type' => 'openai',    'default_model' => 'mistral-small-latest', 'base' => 'https://api.mistral.ai/v1', 'key_hint' => 'console.mistral.ai'],
    ];

    /** The API key for the active provider: admin-entered first, else the .env fallback. */
    public function apiKey(AiSetting $settings): ?string
    {
        if (! empty($settings->api_key)) {
            return $settings->api_key;
        }

        return match ($settings->provider) {
            'anthropic' => config('services.anthropic.key'),
            'gemini'    => config('services.gemini.key'),
            'openai'    => config('services.openai.key'),
            'deepseek'  => config('services.deepseek.key'),
            'grok'      => config('services.grok.key'),
            'mistral'   => config('services.mistral.key'),
            default     => null,
        };
    }

    public function isConfigured(AiSetting $settings): bool
    {
        return (bool) $this->apiKey($settings);
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
    public function ask(AiSetting $settings, string $system, array $messages): ?string
    {
        $provider = $settings->provider ?: 'anthropic';
        $def = self::PROVIDERS[$provider] ?? null;
        $key = $this->apiKey($settings);

        if (! $def || ! $key || empty($messages)) {
            return null;
        }

        $model   = $settings->model ?: $def['default_model'];
        $maxTok  = (int) ($settings->max_response_tokens ?: 1024);

        try {
            return match ($def['type']) {
                'anthropic' => $this->anthropic($key, $model, $system, $messages, $maxTok),
                'gemini'    => $this->gemini($key, $model, $system, $messages, $maxTok),
                'openai'    => $this->openai($def['base'], $key, $model, $system, $messages, $maxTok),
                default     => null,
            };
        } catch (\Throwable $e) {
            Log::warning("AI provider [{$provider}] failed: " . $e->getMessage());
            return null;
        }
    }

    private function anthropic(string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $res = Http::withHeaders([
            'x-api-key'         => $key,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model'      => $model,
            'max_tokens' => $maxTok,
            'system'     => $system,
            'messages'   => $messages, // already role=user/assistant
        ]);

        if (! $res->successful()) {
            Log::warning('Anthropic API error: ' . $res->status() . ' ' . $res->body());
            return null;
        }

        $text = '';
        foreach ($res->json('content', []) as $block) {
            if (($block['type'] ?? null) === 'text') {
                $text .= $block['text'];
            }
        }

        return trim($text) ?: null;
    }

    private function gemini(string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $contents = array_map(fn ($m) => [
            'role'  => $m['role'] === 'user' ? 'user' : 'model',
            'parts' => [['text' => $m['content']]],
        ], $messages);

        $res = Http::timeout(30)
            ->withQueryParameters(['key' => $key])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'system_instruction' => ['parts' => [['text' => $system]]],
                'contents'           => $contents,
                'generationConfig'   => ['maxOutputTokens' => $maxTok, 'temperature' => 0.6],
            ]);

        if (! $res->successful()) {
            Log::warning('Gemini API error: ' . $res->status() . ' ' . $res->body());
            return null;
        }

        $text = '';
        foreach ($res->json('candidates.0.content.parts', []) as $part) {
            $text .= $part['text'] ?? '';
        }

        return trim($text) ?: null;
    }

    /** OpenAI chat-completions format — also used for DeepSeek, Grok, Mistral. */
    private function openai(string $base, string $key, string $model, string $system, array $messages, int $maxTok): ?string
    {
        $payload = array_merge(
            [['role' => 'system', 'content' => $system]],
            $messages
        );

        $res = Http::withToken($key)->timeout(30)->post(rtrim($base, '/') . '/chat/completions', [
            'model'       => $model,
            'max_tokens'  => $maxTok,
            'temperature' => 0.6,
            'messages'    => $payload,
        ]);

        if (! $res->successful()) {
            Log::warning('OpenAI-compatible API error: ' . $res->status() . ' ' . $res->body());
            return null;
        }

        return trim((string) $res->json('choices.0.message.content')) ?: null;
    }
}
