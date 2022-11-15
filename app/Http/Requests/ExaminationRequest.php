<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExaminationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'heading' => ['required', 'array'],
            'heading.up' => ['required', 'string'],
            'heading.down' => ['required', 'string'],
            'sections' => ['required', 'array', 'min:1', 'max:20'],
            'sections.*.name' => ['required', 'string', 'min:2', 'max:255'],
            'sections.*.type' => ['required', 'string', 'in:multiple_choice_questions,true_or_false_questions,essay_questions'],
            'sections.*.count' => ['required', 'numeric', 'min:1', 'max:100'],
            'sections.*.topics' => ['required', 'array'],
            'sections.*.topics.*' => ['required', 'exists:academic_topics,id'],
            'examiners' => ['required', 'string', 'min:2', 'max:255'],
        ];
    }
}
