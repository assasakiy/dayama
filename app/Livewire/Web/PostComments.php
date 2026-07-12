<?php

namespace App\Livewire\Web;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\CommentReaction;
use App\Services\CommentService;
use App\Services\IdentityService;
use Livewire\Component;

class PostComments extends Component
{
    public \App\Models\Post $post;

    public string $content = '';
    public string $guest_name = '';
    public string $guest_email = '';

    public ?string $replyingTo = null;
    public string $reply_content = '';

    public $comments;
    public int $perPage = 5;
    public int $totalComments = 0;
    public bool $hasCommentedAsGuest = false;

    public function mount(\App\Models\Post $post)
    {
        $this->post = $post;
        
        $identityKey = IdentityService::current()['key'] ?? null;
        if (!auth()->check() && $identityKey) {
            $this->hasCommentedAsGuest = Comment::where('identity_key', $identityKey)
                ->where('post_id', $post->id)
                ->exists();
        }

        $this->loadComments();
    }

    public function loadComments()
    {
        $identityKey = IdentityService::current()['key'] ?? null;

        $this->comments = $this->post->comments()
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Published->value)
            ->with([
                'author',
                'children' => fn ($q) => $q
                    ->where('status', CommentStatus::Published->value)
                    ->with('author'),
            ])
            ->when($identityKey, fn ($q) => $q->withExists([
                'reactions' => fn ($rq) => $rq->where('identity_key', $identityKey)
            ]))
            ->latest()
            ->take($this->perPage)
            ->get()
            // Propagate is_liked_by_me to parents and children
            ->each(function ($comment) use ($identityKey) {
                if ($identityKey) {
                    $comment->is_liked_by_me = $comment->reactions_exists ?? false;
                } else {
                    $comment->is_liked_by_me = false;
                }

                $comment->children->each(function ($child) use ($identityKey) {
                    if ($identityKey) {
                        $child->is_liked_by_me = CommentReaction::where('comment_id', $child->id)
                            ->where('identity_key', $identityKey)
                            ->exists();
                    } else {
                        $child->is_liked_by_me = false;
                    }
                });
            });

        $this->totalComments = $this->post->comments()
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Published->value)
            ->count();
    }

    public function loadMore()
    {
        $this->perPage += 5;
        $this->loadComments();
    }

    public function loadLess()
    {
        $this->perPage = 5;
        $this->loadComments();
    }

    public function submitComment(CommentService $commentService)
    {
        $rules = [
            'content' => 'required|string|max:5000',
        ];

        if (!auth()->check()) {
            if ($this->hasCommentedAsGuest) {
                $this->addError('content', __('Anda sudah mengirimkan komentar tamu. Silakan Login untuk berkomentar lagi.'));
                return;
            }
            $rules['guest_name'] = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:160';
        }

        $validated = $this->validate($rules);

        $identity = IdentityService::current();

        $comment = $commentService->addComment(
            $this->post,
            $identity,
            [
                'content'     => $validated['content'],
                'guest_name'  => $validated['guest_name'] ?? null,
                'guest_email' => $validated['guest_email'] ?? null,
            ],
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer')
        );

        $this->reset(['content']);
        $this->loadComments();

        $msg = $comment->status === CommentStatus::Published->value
            ? 'Komentar berhasil dikirim.'
            : 'Komentar terkirim dan menunggu moderasi.';

        session()->flash('success', $msg);
    }

    public function submitReply(CommentService $commentService)
    {
        $rules = [
            'reply_content' => 'required|string|max:5000',
            'replyingTo'    => 'required|exists:comments,id',
        ];

        if (!auth()->check()) {
            if ($this->hasCommentedAsGuest) {
                $this->addError('reply_content', __('Anda sudah mengirimkan komentar tamu. Silakan Login untuk membalas.'));
                return;
            }
            $rules['guest_name'] = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:160';
        }

        $validated = $this->validate($rules);

        $target   = Comment::findOrFail($validated['replyingTo']);
        $identity = IdentityService::current();

        $comment = $commentService->replyTo(
            $target,
            $identity,
            [
                'content'     => $validated['reply_content'],
                'guest_name'  => $validated['guest_name'] ?? null,
                'guest_email' => $validated['guest_email'] ?? null,
            ],
            request()->ip(),
            request()->userAgent(),
            request()->headers->get('referer')
        );

        $this->reset(['reply_content', 'replyingTo']);
        $this->loadComments();

        $msg = $comment->status === CommentStatus::Published->value
            ? 'Balasan berhasil dikirim.'
            : 'Balasan terkirim dan menunggu moderasi.';

        session()->flash('success', $msg);
    }

    public function setReplyingTo(?string $commentId)
    {
        $this->replyingTo = $commentId;
        $this->resetValidation();
    }

    public function cancelReply()
    {
        $this->replyingTo    = null;
        $this->reply_content = '';
    }

    public function toggleLike(string $commentId)
    {
        $identity = IdentityService::current();
        $identityKey = $identity['key'] ?? null;

        if (!$identityKey) {
            return;
        }

        $existing = CommentReaction::where('comment_id', $commentId)
            ->where('identity_key', $identityKey)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            CommentReaction::create([
                'comment_id'   => $commentId,
                'identity_key' => $identityKey,
                'user_id'      => $identity['user_id'] ?? null,
            ]);
            $liked = true;
        }

        $count = CommentReaction::where('comment_id', $commentId)->count();

        // Dispatch browser event so Alpine components update immediately
        $this->dispatch('like-toggled', id: $commentId, liked: $liked, count: $count);

        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.web.post-comments');
    }
}
