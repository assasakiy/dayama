<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('tag'));
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:80|unique:tags,name,' . $this->route('tag')?->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'is_visible' => 'boolean',
        ];
    }
}
