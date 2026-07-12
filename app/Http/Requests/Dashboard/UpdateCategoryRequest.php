<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('category'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120|unique:categories,name,' . $this->route('category')?->id,
            'title' => 'nullable|string|max:160',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:160',
            'seo_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
            'image' => 'nullable|image|max:5120',
            'image_media_id' => 'nullable|exists:media,id',
            'remove_image' => 'nullable|boolean',
        ];
    }
}
