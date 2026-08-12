<?php

namespace App\Policies;

use Modules\CMS\Models\Comment;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class CommentPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Comment::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'view', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Comment::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'update', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'delete', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'restore', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function moderate(User $user, Comment $comment): Response
    {
        $result = $this->authService->check($user, 'moderate', $comment);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
