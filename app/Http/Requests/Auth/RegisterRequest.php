<?php

namespace App\Http\Requests\Auth;

use Phaseolies\Http\Validation\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|unique:users|min:2|max:100',
            'password' => 'required|string|min:8|max:100|confirmed',
        ];
    }
}
