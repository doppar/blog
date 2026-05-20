<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class UpdateProfilePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required|string|min:8|max:100',
            'password' => 'required|string|min:8|max:100|confirmed',
        ];
    }
}
