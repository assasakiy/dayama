<?php

namespace App\Policies;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Authorization\AuthorizationService;

class MediaPolicy
{
    public function __construct(
        private AuthorizationService $authService
    ) {}

    public function viewAny(User $user): Response
    {
        $result = $this->authService->check($user, 'viewAny', Media::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function view(User $user, Media $media): Response
    {
        $result = $this->authService->check($user, 'view', $media);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function create(User $user): Response
    {
        $result = $this->authService->check($user, 'create', Media::class);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function update(User $user, Media $media): Response
    {
        $result = $this->authService->check($user, 'update', $media);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function delete(User $user, Media $media): Response
    {
        $result = $this->authService->check($user, 'delete', $media);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function restore(User $user, Media $media): Response
    {
        $result = $this->authService->check($user, 'restore', $media);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }

    public function forceDelete(User $user, Media $media): Response
    {
        $result = $this->authService->check($user, 'forceDelete', $media);
        return $result->allowed() ? Response::allow() : Response::deny($result->message());
    }
}
