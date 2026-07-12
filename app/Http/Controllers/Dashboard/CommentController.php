<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentController
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Comment::class);

        $query = Comment::with(['author', 'post', 'moderator']);

        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        return Inertia::render('Comments/Index', [
            'comments' => $query->latest()
                ->paginate(15)
                ->through(fn ($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'status' => $comment->status,
                    'author' => $comment->author?->only('name', 'username'),
                    'guest_name' => $comment->guest_name,
                    'guest_email' => $comment->guest_email,
                    'guest_ip' => $comment->guest_ip,
                    'guest_user_agent' => $comment->guest_user_agent,
                    'created_as_guest' => $comment->created_as_guest,
                    'post' => $comment->post ? [
                        'title' => $comment->post->title,
                        'slug' => $comment->post->slug,
                    ] : null,
                    'created_at' => $comment->created_at,
                    'moderation_score' => $comment->moderation_score,
                    'moderation_flags' => $comment->moderation_flags,
                    'moderated_at' => $comment->moderated_at,
                    'moderator' => $comment->moderator?->only('name'),
                    'replies_count' => $comment->replies_count,
                    'depth' => $comment->depth,
                    'is_pinned' => $comment->is_pinned,
                ]),
            'currentStatus' => $request->status ?? 'all',
        ]);
    }

    public function updateStatus(Request $request, Comment $comment): RedirectResponse
    {
        Gate::authorize('moderate', $comment);

        $request->validate([
            'status' => 'required|in:published,rejected,spam,review',
        ]);

        $data = ['status' => $request->status];

        $data['moderated_at'] = now();
        $data['moderated_by'] = $request->user()->id;

        $comment->update($data);

        return redirect()->back()->with('success', 'Comment status updated.');
    }

    public function togglePin(Request $request, Comment $comment): RedirectResponse
    {
        Gate::authorize('moderate', $comment);

        $comment->update(['is_pinned' => !$comment->is_pinned]);

        $status = $comment->is_pinned ? 'pinned' : 'unpinned';
        return redirect()->back()->with('success', "Comment has been {$status}.");
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->route('dashboard.comments.index')->with('success', 'Comment deleted.');
    }
}
