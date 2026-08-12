<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:120|unique:cms_categories,name,' . $this->route('category')?->id,
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:cms_categories,id',
        ];
    }
}
