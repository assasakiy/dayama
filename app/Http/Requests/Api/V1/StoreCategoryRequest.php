<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120|unique:cms_categories',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:cms_categories,id',
        ];
    }
}
