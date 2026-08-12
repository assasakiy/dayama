<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \Modules\Core\Models\Permission::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:core_permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ];
    }
}
