<?php

namespace App\Listeners;

use App\Events\CommentPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAuthorListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentPublished $event): void
    {
        $comment = $event->comment;
        $post = $comment->post;

        // 1. Notify the Post Author (if the commenter isn't the author themselves)
        if ($post->author_id && $post->author_id !== $comment->author_id) {
            $post->author->notify(new \App\Notifications\NewCommentNotification($comment));
        }

        // 2. Notify the Parent Comment Author (if it's a reply and not replying to themselves)
        if ($comment->parent_id) {
            $parent = $comment->parent;
            if ($parent && $parent->author_id && $parent->author_id !== $comment->author_id) {
                // Prevent duplicate notification if the parent author is also the post author
                if ($parent->author_id !== $post->author_id) {
                    $parent->author->notify(new \App\Notifications\CommentReplyNotification($comment));
                }
            }
        }
    }
}
