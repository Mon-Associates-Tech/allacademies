<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'avatar' => ['nullable', 'image'],
            'force_update_avatar' => ['boolean'],
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['force_update_avatar' => $this->has('force_update_avatar')]);
    }
}
