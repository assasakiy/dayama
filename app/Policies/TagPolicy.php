<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class TagPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Tag::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Tag $tag): Response
    {
        $result = $this->authService->check($user, 'view', $tag);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Tag::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Tag $tag): Response
    {
        $result = $this->authService->check($user, 'update', $tag);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Tag $tag): Response
    {
        $result = $this->authService->check($user, 'delete', $tag);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Tag $tag): Response
    {
        $result = $this->authService->check($user, 'restore', $tag);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Tag $tag): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $tag);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
