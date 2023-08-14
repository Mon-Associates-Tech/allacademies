<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImageUploadRequest extends FormRequest
{
    public function rules()
    {
        return [
            'description' => ['required', 'string'],
            'tags' => ['required', 'array'],
            'image' => ['nullable', 'image'],
        ];
    }
}
