<?php

namespace App\Listeners;

use App\Events\CommentPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MentionListener
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
        preg_match_all('/(?<=^|\s)@([A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+)*)/', $comment->content, $matches);
        $usernames = array_unique($matches[1] ?? []);

        if (empty($usernames)) {
            return;
        }

        $users = \Modules\Core\Models\User::whereIn('username', $usernames)->get();

        foreach ($users as $user) {
            // Don't notify the author of the comment themselves
            if ($user->id !== $comment->author_id) {
                // Prevent duplicate notifications if they are the post author or parent comment author
                $isPostAuthor = $user->id === $comment->post->author_id;
                $isParentAuthor = $comment->parent_id && $user->id === $comment->parent->author_id;
                
                if (!$isPostAuthor && !$isParentAuthor) {
                    $user->notify(new \App\Notifications\CommentMentionedNotification($comment));
                }
            }
        }
    }
}
