<?php

namespace App\Http\Requests\Auth;

use Phaseolies\Http\Validation\FormRequest;

class VerifyTwoFactorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string|min:6|max:40',
        ];
    }
}
