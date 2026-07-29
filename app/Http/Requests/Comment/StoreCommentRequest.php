<?php

namespace App\Http\Requests\Comment;

use Phaseolies\Http\Validation\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => 'required|string|min:3|max:5000',
            'parent_id' => 'exists_in:comments,id',
        ];
    }
}
