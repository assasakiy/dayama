<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Post;

#[Signature('app:publish-scheduled-posts')]
#[Description('Publish scheduled posts that are due')]
class PublishScheduledPosts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($posts as $post) {
            $post->status = 'published';
            $post->published_at = $post->scheduled_at ?? now();
            $post->save();
            $count++;
        }

        $this->info("Published {$count} scheduled post(s).");
    }
}
