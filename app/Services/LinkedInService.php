<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\OauthToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LinkedIn client — publishes a text/image post to the connected member's
 * feed using the versioned Posts API (/rest/posts).
 *
 * SAFE before setup: every method checks isConnected() and returns null/false
 * until an OAuth token exists. Going live needs a LinkedIn app with the
 * "Sign In with LinkedIn using OpenID Connect" and "Share on LinkedIn"
 * products enabled (scopes: openid profile w_member_social).
 */
class LinkedInService
{
    private ?OauthToken $token;

    public function __construct()
    {
        $this->token = OauthToken::where('provider', 'linkedin')->first();
    }

    public function isConfigured(): bool
    {
        return (bool) (Credential::get('linkedin.client_id') && Credential::get('linkedin.client_secret'));
    }

    public function isConnected(): bool
    {
        return $this->token !== null && ! empty($this->token->access_token);
    }

    public function accountLabel(): ?string
    {
        return $this->token?->account_label;
    }

    public function personUrn(): ?string
    {
        return $this->token->meta['person_urn'] ?? null;
    }

    private function apiVersion(): string
    {
        return Credential::get('linkedin.api_version') ?: '202411';
    }

    private function headers(): array
    {
        return [
            'LinkedIn-Version'          => $this->apiVersion(),
            'X-Restli-Protocol-Version' => '2.0.0',
        ];
    }

    /**
     * Publish a post to the member's feed. Returns the post URN on success.
     * If an image is supplied it is uploaded first; on image failure it falls
     * back to a text-only post so the content still goes out.
     */
    public function createPost(string $body, ?string $imageUrl = null): ?string
    {
        if (! $this->isConnected()) {
            return null;
        }
        $urn   = $this->personUrn();
        $token = $this->token->access_token;
        if (! $urn) {
            return null;
        }

        $payload = [
            'author'                    => $urn,
            'commentary'                => $this->escapeCommentary($body),
            'visibility'                => 'PUBLIC',
            'distribution'              => [
                'feedDistribution'              => 'MAIN_FEED',
                'targetEntities'                => [],
                'thirdPartyDistributionChannels'=> [],
            ],
            'lifecycleState'            => 'PUBLISHED',
            'isReshareDisabledByAuthor' => false,
        ];

        // Attach an image when one was uploaded (best effort).
        if ($imageUrl) {
            $imageUrn = $this->uploadImage($imageUrl, $urn, $token);
            if ($imageUrn) {
                $payload['content'] = ['media' => ['id' => $imageUrn]];
            }
        }

        try {
            $res = Http::withToken($token)->withHeaders($this->headers())
                ->post('https://api.linkedin.com/rest/posts', $payload);

            if ($res->successful()) {
                return $res->header('x-restli-id') ?: $res->header('x-linkedin-id') ?: 'ok';
            }
            Log::warning('LinkedIn createPost rejected: ' . $res->status() . ' ' . $res->body());
            return null;
        } catch (\Throwable $e) {
            Log::warning('LinkedIn createPost failed: ' . $e->getMessage());
            return null;
        }
    }

    /** Delete a published post by its URN. Returns true on success. */
    public function deletePost(string $urn): bool
    {
        if (! $this->isConnected() || $urn === 'ok') {
            return false;
        }
        try {
            $res = Http::withToken($this->token->access_token)->withHeaders($this->headers())
                ->delete('https://api.linkedin.com/rest/posts/' . rawurlencode($urn));
            return $res->successful();
        } catch (\Throwable $e) {
            Log::warning('LinkedIn deletePost failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload an image and return its URN (urn:li:image:...), or null on failure.
     * 1) initializeUpload  2) PUT the raw bytes to the returned uploadUrl.
     */
    private function uploadImage(string $imageUrl, string $urn, string $token): ?string
    {
        try {
            $init = Http::withToken($token)->withHeaders($this->headers())
                ->post('https://api.linkedin.com/rest/images?action=initializeUpload', [
                    'initializeUploadRequest' => ['owner' => $urn],
                ]);
            if (! $init->successful()) {
                Log::warning('LinkedIn image init failed: ' . $init->body());
                return null;
            }
            $uploadUrl = $init->json('value.uploadUrl');
            $imageUrn  = $init->json('value.image');
            if (! $uploadUrl || ! $imageUrn) {
                return null;
            }

            $bytes = @file_get_contents($imageUrl);
            if ($bytes === false) {
                return null;
            }

            $put = Http::withToken($token)->withBody($bytes, 'application/octet-stream')->put($uploadUrl);
            return $put->successful() ? $imageUrn : null;
        } catch (\Throwable $e) {
            Log::warning('LinkedIn uploadImage failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * The Posts API commentary uses "Little Text Format": a handful of
     * characters must be escaped with a backslash or the post is rejected.
     * Hashtags (#) are left intact so they stay clickable.
     */
    private function escapeCommentary(string $text): string
    {
        return preg_replace('/([\\\\\(\)\[\]\{\}<>@|~])/', '\\\\$1', $text);
    }
}
