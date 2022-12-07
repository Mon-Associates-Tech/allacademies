<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrueOrFalseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'question' => ['required', 'array'],
            'question.up' => ['required', 'string'],
            'question.down' => ['required', 'string'],
            'answer' => ['required', 'boolean'],
            'score' => ['nullable','numeric'],
            'difficulty_level' => ['nullable', 'string', 'in:easy,medium,difficult,unspecified'],
        ];
    }
}
