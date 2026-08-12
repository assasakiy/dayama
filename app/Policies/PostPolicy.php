<?php

namespace App\Policies;

use Modules\CMS\Models\Post;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class PostPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Post::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Post $post): Response
    {
        $result = $this->authService->check($user, 'view', $post);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Post::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Post $post): Response
    {
        $result = $this->authService->check($user, 'update', $post);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Post $post): Response
    {
        $result = $this->authService->check($user, 'delete', $post);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Post $post): Response
    {
        $result = $this->authService->check($user, 'restore', $post);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Post $post): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $post);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
