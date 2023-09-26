<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageTeamsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'reason' => ['required', 'string', 'min:2'],
            'status' => ['required', 'string', 'min:2'],
        ];
    }
}
