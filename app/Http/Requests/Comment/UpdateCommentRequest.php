<?php

namespace App\Http\Requests\Comment;

use Phaseolies\Http\Validation\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => 'required|string|min:3|max:5000',
        ];
    }
}
