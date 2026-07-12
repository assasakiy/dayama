<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentMentionedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Comment $comment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'mention',
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post->title,
            'author_name' => $this->comment->author?->name ?? $this->comment->guest_name,
            'content_preview' => str($this->comment->content)->limit(100),
            'url' => route('blog.show', $this->comment->post->slug) . '#comment-' . $this->comment->id,
            'message' => ($this->comment->author?->name ?? $this->comment->guest_name) . ' mentioned you in a comment on "' . $this->comment->post->title . '"',
        ];
    }
}
