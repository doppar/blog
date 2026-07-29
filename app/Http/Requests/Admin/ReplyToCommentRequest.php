<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class ReplyToCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => 'required|string|min:3|max:5000',
        ];
    }
}
