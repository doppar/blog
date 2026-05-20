<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists_in:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'string|max:255',
            'excerpt' => 'string|max:320',
            'body' => 'required|string|max:20000',
            'cover_image' => 'string|max:255',
            'author_name' => 'string|max:120',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'required|boolean',
            'published_at' => 'string|max:30',
            'view_count' => 'numeric',
            'seo_title' => 'string|max:255',
            'seo_description' => 'string|max:255',
        ];
    }
}
