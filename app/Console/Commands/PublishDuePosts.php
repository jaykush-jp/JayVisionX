<?php

namespace App\Console\Commands;

use App\Models\SocialPost;
use App\Services\SocialPublisher;
use Illuminate\Console\Command;

class PublishDuePosts extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = 'Publish scheduled social posts whose scheduled time has arrived';

    public function handle(SocialPublisher $publisher): int
    {
        $due = SocialPost::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($due->isEmpty()) {
            $this->info('No scheduled posts are due.');
            return self::SUCCESS;
        }

        foreach ($due as $post) {
            try {
                $publisher->publish($post);
                $this->info("Post #{$post->id}: {$post->status}" . ($post->error ? " — {$post->error}" : ''));
            } catch (\Throwable $e) {
                $post->update(['status' => 'failed', 'error' => 'Scheduler error: ' . $e->getMessage()]);
                $this->error("Post #{$post->id} failed: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
