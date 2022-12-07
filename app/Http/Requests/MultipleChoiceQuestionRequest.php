<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MultipleChoiceQuestionRequest extends FormRequest
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
            'option_a' => ['required', 'array'],
            'option_a.up' => ['required', 'string'],
            'option_a.down' => ['required', 'string'],
            'option_b' => ['required', 'array'],
            'option_b.up' => ['required', 'string'],
            'option_b.down' => ['required', 'string'],
            'option_c' => ['required', 'array'],
            'option_c.up' => ['required', 'string'],
            'option_c.down' => ['required', 'string'],
            'option_d' => ['required', 'array'],
            'option_d.up' => ['required', 'string'],
            'option_d.down' => ['required', 'string'],
            'option_e' => ['required', 'array'],
            'option_e.up' => ['nullable', 'string'],
            'option_e.down' => ['nullable', 'string'],
            'answer' => ['required', 'string', 'in:a,b,c,d,e'],
            'score' => ['nullable','numeric'],
            'difficulty_level' => ['nullable', 'string', 'in:easy,medium,difficult,unspecified'],
        ];
    }
}
