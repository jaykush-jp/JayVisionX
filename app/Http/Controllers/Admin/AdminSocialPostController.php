<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialPost;
use App\Services\GoogleBusinessProfileService;
use App\Services\LinkedInService;
use App\Services\MetaGraphService;
use App\Services\SocialPublisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSocialPostController extends Controller
{
    public function index(GoogleBusinessProfileService $gbp, MetaGraphService $meta, LinkedInService $linkedin)
    {
        $posts = SocialPost::latest()->paginate(10);
        $connections = [
            'google'    => $gbp->isConnected(),
            'facebook'  => $meta->isConnected(),
            'instagram' => $meta->isConnected(),
            'linkedin'  => $linkedin->isConnected(),
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
            'platforms.*'  => 'in:google,facebook,instagram,linkedin',
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
            app(SocialPublisher::class)->publish($post);
        }

        return redirect()->route('admin.posts.index')->with('success', match ($data['action']) {
            'schedule' => 'Post scheduled.',
            'publish'  => 'Publish attempted — check each channel\'s status below.',
            default    => 'Post saved as draft.',
        });
    }

    public function publishNow($id, SocialPublisher $publisher)
    {
        $post = SocialPost::findOrFail($id);
        $publisher->publish($post);

        return back()->with('success', 'Publish attempted — see status.');
    }

    public function destroy($id, GoogleBusinessProfileService $gbp, MetaGraphService $meta, LinkedInService $linkedin)
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
        if (! empty($ids['linkedin'])) {
            if (! $linkedin->deletePost($ids['linkedin'])) {
                $notes[] = 'LinkedIn post could not be removed automatically — delete it manually if needed.';
            }
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
