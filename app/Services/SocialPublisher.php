<?php

namespace App\Services;

use App\Models\SocialPost;

class SocialPublisher
{
    public function __construct(
        private GoogleBusinessProfileService $gbp,
        private MetaGraphService $meta,
    ) {}

    /**
     * Push a post to each selected channel. Channels that aren't connected are
     * skipped; if nothing publishes the post is marked failed so it stays
     * actionable.
     */
    public function publish(SocialPost $post): void
    {
        $imageUrl = $post->image ? asset('storage/' . $post->image) : null;
        $caption  = $post->caption; // styled body + hashtags
        $ids      = $post->external_ids ?: [];
        $any      = false;
        $errors   = [];

        foreach ($post->platforms as $platform) {
            $id = match ($platform) {
                'google'    => $this->gbp->createPost($caption, $imageUrl),
                'facebook'  => $this->meta->createFacebookPost($caption, $imageUrl),
                'instagram' => $this->meta->createInstagramPost($caption, $imageUrl),
                default     => null,
            };
            if ($id) {
                $ids[$platform] = $id;
                $any = true;
            } else {
                $errors[] = $this->reason($platform, $imageUrl);
            }
        }

        $post->update([
            'external_ids' => $ids,
            'status'       => $any ? 'published' : 'failed',
            'error'        => $errors ? implode(' ', $errors) : null,
        ]);
    }

    /** A specific, actionable reason why a platform didn't publish. */
    public function reason(string $platform, ?string $imageUrl): string
    {
        return match ($platform) {
            'instagram' => ! $this->meta->isConnected()
                    ? 'Instagram: not connected — Connect in Integrations.'
                    : (! $imageUrl
                        ? 'Instagram: an image is required.'
                        : (! $this->meta->igUserId()
                            ? 'Instagram: no Business account linked to your Page — link it in Meta, then disconnect & reconnect.'
                            : 'Instagram: publish was rejected — check permissions / App Review & that the IG account is a Business/Creator account.')),
            'facebook'  => ! $this->meta->isConnected()
                    ? 'Facebook: not connected — Connect in Integrations.'
                    : (! $this->meta->pageId()
                        ? 'Facebook: no Page found — disconnect & reconnect and grant Page access.'
                        : 'Facebook: publish was rejected — check pages_manage_posts permission / App Review.'),
            'google'    => ! $this->gbp->isConnected()
                    ? 'Google: not connected — Connect in Integrations.'
                    : 'Google: business location not linked yet.',
            default     => ucfirst($platform) . ': publish failed.',
        };
    }
}
