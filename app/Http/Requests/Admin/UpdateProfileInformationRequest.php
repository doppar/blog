<?php

namespace App\Http\Requests\Admin;

use Phaseolies\Http\Validation\FormRequest;

class UpdateProfileInformationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|min:2|max:100',
            'image_file' => 'null|image|mimes:jpg,jpeg,png,gif,webp,avif,svg|max:2M',
        ];
    }
}
