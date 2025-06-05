<?php

namespace App\Livewire\Teachers;

use Livewire\Component;
use App\Models\Assessment;
use App\Models\AssessmentResponse;

class EssayGrader extends Component
{
    public $assessmentId;
    public $essays = [];
    public $scores = [];
    public $correctAnswers = [];

    public function mount($id)
    {
        $this->assessmentId = $id;

        $response = AssessmentResponse::where('assessment_id', $id)->first();

        if (!$response || !$response->data) return;

        // Get essay questions from data blob
        $this->essays = collect($response->data['questions'])->filter(fn($q) => $q['question_type'] === 'essay_question')->values()->toArray();

        // Pre-fill scores and answers
        foreach ($this->essays as $e) {
            $this->scores[$e['question_id']] = $e['score'];
            $this->correctAnswers[$e['question_id']] = $e['correct_answer'] ?? '';
        }
    }

    public function saveGrades($questionId)
    {
        $responseData = AssessmentResponse::where('assessment_id', $this->assessmentId)->first();
        if (!$responseData) return;

        $data = $responseData->data;

        foreach ($data['questions'] as &$q) {
            if ($q['question_id'] == $questionId) {
                $q['score'] = $this->scores[$questionId] ?? 0;
                $q['correct_answer'] = $this->correctAnswers[$questionId];
                $q['is_correct'] = true; // always true for essays
            }
        }

        // Recalculate total
        $data['total_score'] = array_sum(array_column($data['questions'], 'score'));
        $data['percentage_score'] = round(($data['total_score'] / $data['max_score']) * 100, 1);

        // Mark as completed if no more essays pending
        $pending = collect($data['questions'])->filter(fn($q) =>
            $q['question_type'] === 'essay_question' && !isset($q['score'])
        )->count();

        $data['needs_grading'] = $pending > 0;

        $responseData->data = $data;
        $responseData->save();

        session()->flash('success', 'Score saved successfully.');
    }

    public function submitAllScores()
    {
        foreach ($this->essays as $e) {
            $this->saveGrades($e['question_id']);
        }

        // Update assessment status
        $assessment = Assessment::find($this->assessmentId);
        $assessment->status = 'completed';
        $assessment->save();

        // Notify student
        $this->dispatch('notify-student', [
            'message' => "Your essay has been graded.",
            'link' => route('student.assessment.results', ['id' => $this->assessmentId])
        ]);

        session()->flash('success', 'All scores submitted and student notified.');
    }

    public function render()
    {
        return view('livewire.teachers.essay-grader');
    }
}


