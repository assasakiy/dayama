<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('role'));
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255|unique:core_roles,name,' . $this->route('role')->id,
            'guard_name'   => 'nullable|string|max:255',
            'display_name' => 'nullable|string|max:100',
            'description'  => 'nullable|string|max:500',
            'slug'         => 'nullable|string|max:100',
            'color'        => 'nullable|string|max:20',
            'icon'         => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,inactive',
            'scope'        => 'nullable|string|in:yayasan,lembaga',
            'rank'         => 'nullable|integer|min:0|max:' . ($this->user()->is_primary_super_admin ? 100 : $this->user()->getHighestRank()),
            'permissions'  => 'nullable|array',
            'permissions.*' => 'string|exists:core_permissions,name',
        ];
    }
}
