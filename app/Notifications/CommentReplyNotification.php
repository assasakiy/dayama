<?php

namespace App\Notifications;

use Modules\CMS\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Comment $reply
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
            'type' => 'reply',
            'comment_id' => $this->reply->id,
            'parent_id' => $this->reply->parent_id,
            'post_id' => $this->reply->post_id,
            'post_title' => $this->reply->post->title,
            'author_name' => $this->reply->author?->name ?? $this->reply->guest_name,
            'content_preview' => str($this->reply->content)->limit(100),
            'url' => route('blog.show', $this->reply->post->slug) . '#comment-' . $this->reply->id,
            'message' => ($this->reply->author?->name ?? $this->reply->guest_name) . ' replied to your comment on "' . $this->reply->post->title . '"',
        ];
    }
}
