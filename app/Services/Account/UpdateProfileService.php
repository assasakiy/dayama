<?php

declare(strict_types=1);

namespace App\Services\Account;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateProfileService
{
    /**
     * Update user profile information.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'] ?? null,
        ]);

        $profileData = [
            'biography' => $data['biography'] ?? null,
            'website' => $data['website'] ?? null,
            'social_links' => $data['social_links'] ?? null,
        ];

        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old legacy avatar if it exists
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
                $profileData['avatar'] = null;
            }
            
            // Delete old spatie avatar and add new one
            $user->clearMediaCollection('avatars');
            $user->addMedia($data['avatar'])->toMediaCollection('avatars');
            
        } elseif (!empty($data['delete_avatar'])) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
                $profileData['avatar'] = null;
            }
            $user->clearMediaCollection('avatars');
        }

        if (isset($data['banner']) && $data['banner'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old legacy banner
            if ($user->banner) {
                Storage::disk('public')->delete($user->banner);
                $user->banner = null;
            }
            
            // Delete old spatie banner and add new one
            $user->clearMediaCollection('banners');
            $user->addMedia($data['banner'])->toMediaCollection('banners');
            
        } elseif (!empty($data['delete_banner'])) {
            if ($user->banner) {
                Storage::disk('public')->delete($user->banner);
                $user->banner = null;
            }
            $user->clearMediaCollection('banners');
        }

        $user->save();

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return $user;
    }
}
