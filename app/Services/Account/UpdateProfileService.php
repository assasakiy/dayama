<?php

declare(strict_types=1);

namespace App\Services\Account;

use Modules\Core\Models\Person;
use Modules\Core\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateProfileService
{
    public function update(User $user, array $data): User
    {
        // Note: the person names are now kept intact.
        // The display names (full_name and nickname) are stored in the profile.
        
        if (isset($data['username'])) {
            $user->update([
                'username' => $data['username']
            ]);
        }

        $profileData = [
            'full_name' => $data['full_name'] ?? null,
            'nickname'  => $data['nickname'] ?? null,
            'biography' => $data['biography'] ?? null,
            'website' => $data['website'] ?? null,
            'social_links' => $data['social_links'] ?? null,
        ];

        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = $data['avatar']->store("users/{$user->id}/avatars", 'public');
            $user->clearMediaCollection('avatars');
        } elseif (!empty($data['delete_avatar'])) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $profileData['avatar'] = null;
            $user->clearMediaCollection('avatars');
        }

        if (isset($data['banner']) && $data['banner'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->profile && $user->profile->banner) {
                Storage::disk('public')->delete($user->profile->banner);
            }
            $profileData['banner'] = $data['banner']->store("users/{$user->id}/banners", 'public');
            $user->clearMediaCollection('banners');
        } elseif (!empty($data['delete_banner'])) {
            if ($user->profile && $user->profile->banner) {
                Storage::disk('public')->delete($user->profile->banner);
            }
            $profileData['banner'] = null;
            $user->clearMediaCollection('banners');
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return $user;
    }
}
