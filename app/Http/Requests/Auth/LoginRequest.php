<?php

namespace App\Http\Requests\Auth;

use Phaseolies\Http\Validation\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|min:2|max:100',
            'password' => 'required|string|min:2|max:100',
            'remember' => 'null|boolean',
        ];
    }
}
