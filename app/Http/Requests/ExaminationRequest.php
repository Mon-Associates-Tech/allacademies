<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
            'heading.up' => ['required', 'string'],
            'heading.down' => ['required', 'string'],
            'heading.title' => ['required', 'string', 'min:2', 'max:255'],
            'heading.duration' => ['required', 'string', 'min:2', 'max:255'],
            'heading.instructions' => ['required', 'string', 'min:2', 'max:1023'],
            'sections' => ['required', 'array', 'min:1', 'max:20'],
            'sections.*.name' => ['required', 'string', 'min:2', 'max:255'],
            'sections.*.type' => ['required', 'string', 'in:multiple_choice_questions,true_or_false_questions,essay_questions'],
            'sections.*.count' => ['required', 'numeric', 'min:1', 'max:100'],
            'sections.*.topics' => ['required', 'array'],
            'sections.*.topics.*' => ['required', 'exists:academic_topics,id'],
            'team_id' => ['required', 'numeric', 'exists:teams,id'],
            'creator_id' => ['required', 'numeric', 'exists:users,id'],
        ];
    }
}
