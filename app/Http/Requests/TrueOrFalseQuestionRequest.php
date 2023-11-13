<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrueOrFalseQuestionRequest extends FormRequest
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
            'score' => ['required', 'numeric', 'min:1'],
            'difficulty_level' => ['required', 'string', 'in:easy,medium,difficult,unspecified'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question.up' => 'question',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['answer' => $this->has('answer')]);
    }
}
