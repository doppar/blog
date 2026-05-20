<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Phaseolies\Http\Validation\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        $roles = implode(',', array_keys(User::roleOptions()));

        return [
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|unique:users|min:2|max:100',
            'role' => "required|in:{$roles}",
            'image_file' => 'null|image|mimes:jpg,jpeg,png,gif,webp,avif,svg|max:2M',
            'status' => 'required|boolean',
            'password' => 'required|string|min:8|max:100|confirmed',
        ];
    }
}
