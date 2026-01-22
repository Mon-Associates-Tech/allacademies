<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultipleChoiceQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
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
            'option_c' => ['nullable', 'array'],
            'option_c.up' => ['nullable', 'string'],
            'option_c.down' => ['nullable', 'string'],
            'option_d' => ['nullable', 'array'],
            'option_d.up' => ['nullable', 'string'],
            'option_d.down' => ['nullable', 'string'],
            'option_e' => ['nullable', 'array'],
            'option_e.up' => ['nullable', 'string'],
            'option_e.down' => ['nullable', 'string'],
            'answer' => ['required', 'string', 'in:a,b,c,d,e'],
            'score' => ['required', 'numeric', 'min:1'],
            'difficulty_level' => ['required', 'string', 'in:easy,medium,difficult,unspecified'],
            'subtopic' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question.up' => 'question',
            'option_a.up' => 'option A',
            'option_b.up' => 'option B',
            'option_c.up' => 'option C',
            'option_d.up' => 'option D',
            'option_e.up' => 'option E',
        ];
    }
}
