<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
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
            'duration_in_minutes' => ['required', 'numeric'],
            // 'starts_at' => ['required', 'date'],
            // 'ends_at' => ['required', 'date'],
            'sections' => ['required', 'array', 'min:1', 'max:20'],
            'sections.*.name' => ['required', 'string', 'min:2', 'max:255'],
            'sections.*.type' => ['required', 'string', 'in:multiple_choice_questions,true_or_false_questions'],
            'sections.*.count' => ['required', 'numeric', 'min:1', 'max:100'],
            'sections.*.topics' => ['required', 'array'],
            'sections.*.topics.*' => ['required', 'exists:academic_topics,id'],
            'team_id' => ['required', 'numeric', 'exists:teams,id'],
            'creator_id' => ['required', 'numeric', 'exists:users,id'],
        ];
    }
}
