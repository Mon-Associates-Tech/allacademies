<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return $this->isMethod('GET') ? [] : [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'role' => ['required', 'string', 'in:admin,moderator,guest'],
        ];
    }
}
