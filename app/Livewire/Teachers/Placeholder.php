<?php

namespace App\Livewire\Teachers;

use App\Models\AssessmentResponse;

class Placeholder
{
    public $scores = [];

    public function mount($assessmentId)
    {
        $response = AssessmentResponse::where('assessment_id', $assessmentId)->first();
        $this->essays = collect($response->data['questions'])->filter(fn($q) => $q['question_type'] === 'essay_question');

        foreach ($this->essays as $e) {
            $this->scores[$e['question_id']] = $e['score'];
        }
    }

    public function saveScore($questionId)
    {
        $newScore = $this->scores[$questionId] ?? 0;

        // Update data JSON
        $responseData = AssessmentResponse::where('assessment_id', $this->assessmentId)->first();
        $data = $responseData->data;

        foreach ($data['questions'] as &$q) {
            if ($q['question_id'] == $questionId) {
                $q['score'] = $newScore;
            }
        }

        // Recalculate total
        $data['total_score'] = array_sum(array_column($data['questions'], 'score'));
        $data['percentage_score'] = $data['max_score'] > 0
            ? round(($data['total_score'] / $data['max_score']) * 100, 1)
            : 0;

        $responseData->data = $data;
        $responseData->save();

        session()->flash('success', 'Score saved successfully.');
    }

}
