<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'string|max:255',
            'description' => 'string|max:1000',
            'color' => 'string|max:20',
        ];
    }
}
