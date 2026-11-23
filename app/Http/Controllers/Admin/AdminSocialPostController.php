<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialPost;
use App\Services\GoogleBusinessProfileService;
use App\Services\MetaGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSocialPostController extends Controller
{
    public function index(GoogleBusinessProfileService $gbp, MetaGraphService $meta)
    {
        $posts = SocialPost::latest()->paginate(15);
        $connections = [
            'google'    => $gbp->isConnected(),
            'facebook'  => $meta->isConnected(),
            'instagram' => $meta->isConnected(),
        ];

        return view('admin.posts.index', compact('posts', 'connections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'body'         => 'required|string',
            'hashtags'     => 'nullable|string|max:500',
            'location'     => 'nullable|string|max:120',
            'platforms'    => 'required|array|min:1',
            'platforms.*'  => 'in:google,facebook,instagram',
            'image'        => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'scheduled_at' => 'nullable|date',
            'action'       => 'required|in:draft,publish,schedule',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('social', 'public');
        }

        $status = match ($data['action']) {
            'schedule' => 'scheduled',
            'publish'  => 'published',
            default    => 'draft',
        };

        $post = SocialPost::create([
            'body'         => $data['body'],
            'hashtags'     => $this->normaliseHashtags($data['hashtags'] ?? null),
            'location'     => $data['location'] ?? null,
            'image'        => $imagePath,
            'platforms'    => $data['platforms'],
            'scheduled_at' => $data['action'] === 'schedule' ? $data['scheduled_at'] : null,
            'status'       => $status === 'published' ? 'draft' : $status, // flipped to published only if a channel accepts it
            'created_by'   => session('admin_email'),
        ]);

        if ($data['action'] === 'publish') {
            $this->publish($post, app(GoogleBusinessProfileService::class), app(MetaGraphService::class));
        }

        return redirect()->route('admin.posts.index')->with('success', match ($data['action']) {
            'schedule' => 'Post scheduled.',
            'publish'  => 'Publish attempted — check each channel\'s status below.',
            default    => 'Post saved as draft.',
        });
    }

    public function publishNow($id, GoogleBusinessProfileService $gbp, MetaGraphService $meta)
    {
        $post = SocialPost::findOrFail($id);
        $this->publish($post, $gbp, $meta);

        return back()->with('success', 'Publish attempted — see status.');
    }

    public function destroy($id, GoogleBusinessProfileService $gbp, MetaGraphService $meta)
    {
        $post = SocialPost::findOrFail($id);
        $ids  = $post->external_ids ?: [];
        $notes = [];

        // Remove from the channels it was published to.
        if (! empty($ids['facebook'])) {
            if (! $meta->deleteFacebookPost($ids['facebook'])) {
                $notes[] = 'Facebook post could not be removed automatically — delete it manually if needed.';
            }
        }
        if (! empty($ids['google'])) {
            $gbp->deletePost($ids['google']);
        }
        if (! empty($ids['instagram'])) {
            // Instagram Graph API does not allow deleting published posts.
            $notes[] = 'Instagram does not allow deleting posts via API — please remove it from the Instagram app.';
        }

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        $msg = 'Post deleted.';
        $msg .= $notes ? ' ' . implode(' ', $notes) : ($ids ? ' Removed from connected channels too.' : '');

        return back()->with('success', $msg);
    }

    /**
     * Push a post to each selected channel. Channels that aren't connected are
     * skipped; if nothing publishes the post is marked failed so it stays
     * actionable. Goes fully live once the integrations are connected.
     */
    private function publish(SocialPost $post, GoogleBusinessProfileService $gbp, MetaGraphService $meta): void
    {
        $imageUrl = $post->image ? asset('storage/' . $post->image) : null;
        $caption  = $post->caption; // body + hashtags
        $ids = $post->external_ids ?: [];
        $any = false;
        $errors = [];

        foreach ($post->platforms as $platform) {
            $id = match ($platform) {
                'google'    => $gbp->createPost($caption, $imageUrl),
                'facebook'  => $meta->createFacebookPost($caption, $imageUrl),
                'instagram' => $meta->createInstagramPost($caption, $imageUrl),
                default     => null,
            };
            if ($id) {
                $ids[$platform] = $id;
                $any = true;
            } else {
                $errors[] = $this->publishReason($platform, $imageUrl, $gbp, $meta);
            }
        }

        $post->update([
            'external_ids' => $ids,
            'status'       => $any ? 'published' : 'failed',
            'error'        => $errors ? implode(' ', $errors) : null,
        ]);
    }

    /** A specific, actionable reason why a platform didn't publish. */
    private function publishReason(string $platform, ?string $imageUrl, GoogleBusinessProfileService $gbp, MetaGraphService $meta): string
    {
        return match ($platform) {
            'instagram' => ! $meta->isConnected()
                    ? 'Instagram: not connected — Connect in Integrations.'
                    : (! $imageUrl
                        ? 'Instagram: an image is required.'
                        : (! $meta->igUserId()
                            ? 'Instagram: no Business account linked to your Page — link it in Meta, then disconnect & reconnect.'
                            : 'Instagram: publish was rejected — check permissions / App Review & that the IG account is a Business/Creator account.')),
            'facebook'  => ! $meta->isConnected()
                    ? 'Facebook: not connected — Connect in Integrations.'
                    : (! $meta->pageId()
                        ? 'Facebook: no Page found — disconnect & reconnect and grant Page access.'
                        : 'Facebook: publish was rejected — check pages_manage_posts permission / App Review.'),
            'google'    => ! $gbp->isConnected()
                    ? 'Google: not connected — Connect in Integrations.'
                    : 'Google: business location not linked yet.',
            default     => ucfirst($platform) . ': publish failed.',
        };
    }

    /** Normalise hashtags to space-separated #tags. */
    private function normaliseHashtags(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        $tags = preg_split('/[\s,]+/', trim($raw), -1, PREG_SPLIT_NO_EMPTY);
        $tags = array_map(fn ($t) => '#' . ltrim($t, '#'), $tags);

        return implode(' ', $tags) ?: null;
    }
}
