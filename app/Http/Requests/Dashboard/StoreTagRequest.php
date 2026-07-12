<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Tag::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80|unique:tags',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'is_visible' => 'boolean',
        ];
    }
}
