<?php

namespace App\Services;

use App\Models\OauthToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Business Profile client — reviews + local posts.
 *
 * This is the structured integration point. It is SAFE to call before the
 * business's Google Cloud app is approved: every method first checks
 * isConnected() / isConfigured() and returns an empty/neutral result instead
 * of throwing. Once an OAuth token exists, the live API calls run.
 *
 * Going live additionally requires an approved Google Business Profile API
 * access request tied to the verified location (see the plan's
 * "What you must provide" section).
 */
class GoogleBusinessProfileService
{
    private ?OauthToken $token;

    public function __construct()
    {
        $this->token = OauthToken::where('provider', 'google')->first();
    }

    /** Are the app credentials present in config/env? */
    public function isConfigured(): bool
    {
        return (bool) (config('services.google.client_id') && config('services.google.client_secret'));
    }

    /** Has an admin completed the OAuth connect flow? */
    public function isConnected(): bool
    {
        return $this->token !== null && ! empty($this->token->access_token);
    }

    public function accountLabel(): ?string
    {
        return $this->token?->account_label;
    }

    /**
     * Pull reviews for the connected location.
     * Returns [] until connected — callers render an empty state.
     *
     * @return array<int, array{id:string,author:string,rating:int,comment:string,created:string,reply:?string}>
     */
    public function listReviews(): array
    {
        if (! $this->isConnected()) {
            return [];
        }

        $locationId = $this->token->meta['location_id'] ?? null;
        $accountId  = $this->token->meta['account_id'] ?? null;
        if (! $locationId || ! $accountId) {
            return [];
        }

        try {
            $res = Http::withToken($this->token->access_token)->get(
                "https://mybusiness.googleapis.com/v4/accounts/{$accountId}/locations/{$locationId}/reviews"
            );

            return collect($res->json('reviews', []))->map(fn ($r) => [
                'id'      => $r['reviewId'] ?? '',
                'author'  => $r['reviewer']['displayName'] ?? 'Anonymous',
                'rating'  => (int) str_replace(['STAR_RATING_UNSPECIFIED', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE'], [0, 1, 2, 3, 4, 5], $r['starRating'] ?? 'ONE'),
                'comment' => $r['comment'] ?? '',
                'created' => $r['createTime'] ?? '',
                'reply'   => $r['reviewReply']['comment'] ?? null,
            ])->all();
        } catch (\Throwable $e) {
            Log::warning('GBP listReviews failed: ' . $e->getMessage());
            return [];
        }
    }

    /** Post a public reply to a review. No-op (false) until connected. */
    public function replyToReview(string $reviewId, string $comment): bool
    {
        if (! $this->isConnected()) {
            return false;
        }

        $locationId = $this->token->meta['location_id'] ?? null;
        $accountId  = $this->token->meta['account_id'] ?? null;
        if (! $locationId || ! $accountId) {
            return false;
        }

        try {
            $res = Http::withToken($this->token->access_token)->put(
                "https://mybusiness.googleapis.com/v4/accounts/{$accountId}/locations/{$locationId}/reviews/{$reviewId}/reply",
                ['comment' => $comment]
            );

            return $res->successful();
        } catch (\Throwable $e) {
            Log::warning('GBP replyToReview failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a local post (Google Post). Returns the remote post id or null.
     */
    public function createPost(string $body, ?string $imageUrl = null): ?string
    {
        if (! $this->isConnected()) {
            return null;
        }

        $locationId = $this->token->meta['location_id'] ?? null;
        $accountId  = $this->token->meta['account_id'] ?? null;
        if (! $locationId || ! $accountId) {
            return null;
        }

        $payload = ['summary' => $body, 'topicType' => 'STANDARD', 'languageCode' => 'en'];
        if ($imageUrl) {
            $payload['media'] = [['mediaFormat' => 'PHOTO', 'sourceUrl' => $imageUrl]];
        }

        try {
            $res = Http::withToken($this->token->access_token)->post(
                "https://mybusiness.googleapis.com/v4/accounts/{$accountId}/locations/{$locationId}/localPosts",
                $payload
            );

            return $res->successful() ? ($res->json('name') ?? 'ok') : null;
        } catch (\Throwable $e) {
            Log::warning('GBP createPost failed: ' . $e->getMessage());
            return null;
        }
    }
}
