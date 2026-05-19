<?php

namespace App\Http\Requests;

use Phaseolies\Http\Validation\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'excerpt' => 'string|max:1000',
            'status' => 'required|boolean',
        ];
    }
}
