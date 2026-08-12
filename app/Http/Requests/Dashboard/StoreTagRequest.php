<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \Modules\CMS\Models\Tag::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80|unique:cms_tags',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'is_visible' => 'boolean',
        ];
    }
}
