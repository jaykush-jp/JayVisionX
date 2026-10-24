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

    public function destroy($id)
    {
        $post = SocialPost::findOrFail($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    /**
     * Push a post to each selected channel. Channels that aren't connected are
     * skipped; if nothing publishes the post is marked failed so it stays
     * actionable. Goes fully live once the integrations are connected.
     */
    private function publish(SocialPost $post, GoogleBusinessProfileService $gbp, MetaGraphService $meta): void
    {
        $imageUrl = $post->image ? asset('storage/' . $post->image) : null;
        $ids = $post->external_ids ?: [];
        $any = false;

        foreach ($post->platforms as $platform) {
            $id = match ($platform) {
                'google'    => $gbp->createPost($post->body, $imageUrl),
                'facebook'  => $meta->createFacebookPost($post->body, $imageUrl),
                'instagram' => $meta->createInstagramPost($post->body, $imageUrl),
                default     => null,
            };
            if ($id) {
                $ids[$platform] = $id;
                $any = true;
            }
        }

        $post->update([
            'external_ids' => $ids,
            'status'       => $any ? 'published' : 'failed',
            'error'        => $any ? null : 'No connected channel accepted this post. Connect the integration and retry.',
        ]);
    }
}
