<?php

namespace App\Services;

use App\Models\OauthToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Meta Graph API client — publishes to a Facebook Page and a linked
 * Instagram Business account.
 *
 * SAFE before App Review: methods check isConnected() and return null/false
 * until an OAuth token exists. Going live needs a Meta app with Business
 * Verification + App Review for pages/IG publishing, and an IG Business
 * account linked to the Page (see the plan).
 */
class MetaGraphService
{
    private ?OauthToken $token;
    private string $graph;

    public function __construct()
    {
        $this->token = OauthToken::where('provider', 'meta')->first();
        $this->graph = 'https://graph.facebook.com/' . config('services.meta.graph_version', 'v21.0');
    }

    public function isConfigured(): bool
    {
        return (bool) (config('services.meta.app_id') && config('services.meta.app_secret'));
    }

    public function isConnected(): bool
    {
        return $this->token !== null && ! empty($this->token->access_token);
    }

    public function accountLabel(): ?string
    {
        return $this->token?->account_label;
    }

    /** Page access token with leads_retrieval — env override, else OAuth token. */
    public function pageToken(): ?string
    {
        return config('services.meta.page_access_token')
            ?: ($this->token->meta['page_access_token'] ?? null)
            ?: $this->token?->access_token;
    }

    /**
     * Fetch a single Lead Ads lead by its id and normalise the field data.
     * @return array{name:?string,email:?string,phone:?string,raw:array}|null
     */
    public function fetchLead(string $leadgenId): ?array
    {
        $token = $this->pageToken();
        if (! $token) {
            return null;
        }

        try {
            $res = Http::get("{$this->graph}/{$leadgenId}", [
                'fields'       => 'field_data,created_time,ad_id,form_id',
                'access_token' => $token,
            ]);
            if (! $res->successful()) {
                Log::warning('Meta fetchLead failed: ' . $res->body());
                return null;
            }

            return $this->normaliseFields($res->json('field_data', []));
        } catch (\Throwable $e) {
            Log::warning('Meta fetchLead error: ' . $e->getMessage());
            return null;
        }
    }

    /** Pull recent leads for a Lead Ads form (manual sync). */
    public function listFormLeads(string $formId, int $limit = 50): array
    {
        $token = $this->pageToken();
        if (! $token) {
            return [];
        }

        try {
            $res = Http::get("{$this->graph}/{$formId}/leads", [
                'limit'        => $limit,
                'access_token' => $token,
            ]);

            return collect($res->json('data', []))
                ->map(fn ($l) => $this->normaliseFields($l['field_data'] ?? []) + ['id' => $l['id'] ?? null])
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Meta listFormLeads error: ' . $e->getMessage());
            return [];
        }
    }

    /** Map Meta's field_data array to name/email/phone. */
    private function normaliseFields(array $fieldData): array
    {
        $map = [];
        foreach ($fieldData as $f) {
            $map[strtolower($f['name'] ?? '')] = $f['values'][0] ?? null;
        }

        return [
            'name'  => $map['full_name'] ?? $map['name'] ?? trim(($map['first_name'] ?? '') . ' ' . ($map['last_name'] ?? '')) ?: null,
            'email' => $map['email'] ?? null,
            'phone' => $map['phone_number'] ?? $map['phone'] ?? null,
            'raw'   => $map,
        ];
    }

    /** Publish to the connected Facebook Page. Returns remote post id or null. */
    public function createFacebookPost(string $body, ?string $imageUrl = null): ?string
    {
        if (! $this->isConnected()) {
            return null;
        }

        $pageId    = $this->token->meta['page_id'] ?? null;
        $pageToken = $this->token->meta['page_access_token'] ?? $this->token->access_token;
        if (! $pageId) {
            return null;
        }

        try {
            if ($imageUrl) {
                $res = Http::post("{$this->graph}/{$pageId}/photos", [
                    'url'          => $imageUrl,
                    'caption'      => $body,
                    'access_token' => $pageToken,
                ]);
            } else {
                $res = Http::post("{$this->graph}/{$pageId}/feed", [
                    'message'      => $body,
                    'access_token' => $pageToken,
                ]);
            }

            return $res->successful() ? ($res->json('id') ?? 'ok') : null;
        } catch (\Throwable $e) {
            Log::warning('Meta createFacebookPost failed: ' . $e->getMessage());
            return null;
        }
    }

    /** Publish to the linked Instagram Business account. Needs an image. */
    public function createInstagramPost(string $body, ?string $imageUrl = null): ?string
    {
        if (! $this->isConnected() || ! $imageUrl) {
            return null;
        }

        $igId = $this->token->meta['ig_user_id'] ?? null;
        $token = $this->token->meta['page_access_token'] ?? $this->token->access_token;
        if (! $igId) {
            return null;
        }

        try {
            $container = Http::post("{$this->graph}/{$igId}/media", [
                'image_url'    => $imageUrl,
                'caption'      => $body,
                'access_token' => $token,
            ]);
            $creationId = $container->json('id');
            if (! $creationId) {
                return null;
            }

            $publish = Http::post("{$this->graph}/{$igId}/media_publish", [
                'creation_id'  => $creationId,
                'access_token' => $token,
            ]);

            return $publish->successful() ? ($publish->json('id') ?? 'ok') : null;
        } catch (\Throwable $e) {
            Log::warning('Meta createInstagramPost failed: ' . $e->getMessage());
            return null;
        }
    }
}
