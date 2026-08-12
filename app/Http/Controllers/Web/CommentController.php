<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Modules\CMS\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;

class CommentController
{
    public function store(Request $request, \App\Services\CommentService $commentService): JsonResponse|RedirectResponse
    {
        $rules = [
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
        ];

        // Guest users must provide name and email
        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string|max:100';
            $rules['guest_email'] = 'required|email|max:160';
        }

        $validated = $request->validate($rules);

        $post = \Modules\CMS\Models\Post::findOrFail($validated['post_id']);
        $identity = \App\Services\IdentityService::current();

        $data = [
            'content' => $validated['content'],
            'guest_name' => $validated['guest_name'] ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
        ];

        if (isset($validated['parent_id'])) {
            $target = Comment::findOrFail($validated['parent_id']);
            $comment = $commentService->replyTo(
                $target,
                $identity,
                $data,
                $request->ip(),
                $request->userAgent(),
                $request->headers->get('referer')
            );
        } else {
            $comment = $commentService->addComment(
                $post,
                $identity,
                $data,
                $request->ip(),
                $request->userAgent(),
                $request->headers->get('referer')
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $comment->status === \App\Enums\CommentStatus::Published->value
                    ? 'Comment posted successfully.'
                    : 'Comment submitted and is awaiting moderation.',
                'data' => [
                    'id' => $comment->id,
                    'status' => $comment->status,
                ]
            ]);
        }

        $msg = $comment->status === \App\Enums\CommentStatus::Published->value
            ? 'Comment posted successfully.'
            : 'Comment submitted and is awaiting moderation.';

        return redirect()->back()->with('success', $msg);
    }
}
