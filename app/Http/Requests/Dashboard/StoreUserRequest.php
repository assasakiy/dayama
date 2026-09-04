<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Models\User;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:core_users,email',
            'username' => 'nullable|string|max:255|unique:core_users,username',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:core_roles,name',
            'institution_id' => 'nullable|string|uuid|exists:core_institutions,id',
            'assignments' => 'nullable|array',
            'assignments.*.role' => 'required_with:assignments|string|exists:core_roles,name',
            'assignments.*.institution' => 'nullable|string|uuid|exists:core_institutions,id',
            'assignments.*.institution_id' => 'nullable|string|uuid|exists:core_institutions,id',
            'biography' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:500',
            'social_links.github' => 'nullable|url|max:500',
            'social_links.twitter' => 'nullable|url|max:500',
            'social_links.linkedin' => 'nullable|url|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'is_protected' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
        ];
    }
}
