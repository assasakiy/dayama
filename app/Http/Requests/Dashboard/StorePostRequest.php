<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \Modules\CMS\Models\Post::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'primary_category_id' => 'nullable|exists:cms_categories,id|in_array:categories.*',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:cms_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:cms_tags,id',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            'thumbnail_media_id' => 'nullable|integer|exists:core_media,id',
            'seo_title' => 'nullable|string|max:200',
            'seo_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:300',
            'allow_comments' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }
}
