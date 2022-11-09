<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EssayQuestionRequest extends FormRequest
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
            'answer' => ['required', 'array'],
            'answer.up' => ['required', 'string'],
            'answer.down' => ['required', 'string'],
            'score' => ['nullable','numeric'],
            'difficulty_level' => ['required', 'string'],
            'score' => ['nullable','numeric'],
            'difficulty_level' => ['required', 'string'],
        ];
    }
}
