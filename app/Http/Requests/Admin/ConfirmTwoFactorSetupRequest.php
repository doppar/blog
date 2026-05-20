<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class ConfirmTwoFactorSetupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'two_factor_code' => 'required|digits:6',
        ];
    }
}
