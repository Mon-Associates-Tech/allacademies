<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'other_names' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required', 'same:password'],
            'author' => ['sometimes'],
            'gender' => ['nullable', 'string', 'in:male,female,other,prefer_not_to_say'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'region_manual' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'city_manual' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'min:7'],
            'terms' => ['required', 'accepted'],
            'newschool' => ['nullable', 'boolean'],
        ];
    }
}
