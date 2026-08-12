<?php

namespace App\Policies;

use Modules\CMS\Models\Category;
use Modules\Core\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class CategoryPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Category::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Category $category): Response
    {
        $result = $this->authService->check($user, 'view', $category);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Category::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Category $category): Response
    {
        $result = $this->authService->check($user, 'update', $category);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Category $category): Response
    {
        $result = $this->authService->check($user, 'delete', $category);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Category $category): Response
    {
        $result = $this->authService->check($user, 'restore', $category);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Category $category): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $category);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
