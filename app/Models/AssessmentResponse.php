<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'assessment_id',
        'data',
    ];

    protected $casts = [
        'response' => 'array', // For multiple choice selections
        'is_correct' => 'boolean',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    private function formatAssessment($assessment)
{
    $response = $assessment->assessmentResponse;

    $questions = [];
    if ($response && isset($response->data['questions'])) {
        foreach ($response->data['questions'] as $q) {
            $questions[] = [
                'text' => $q['question'],
                'studentAnswer' => $q['user_answer'],
                'correctAnswer' => $q['correct_answer'],
                'isCorrect' => $q['is_correct']
            ];
        }
    }

    return [
        'id' => "assessment_{$assessment->id}",
        'title' => "Assessment: {$assessment->title}",
        'start' => $assessment->created_at->format('Y-m-d H:i:s'),
        'end' => $assessment->updated_at->format('Y-m-d H:i:s'),
        'type' => 'assessment',
        'status' => $assessment->status,
        'subject' => optional($assessment->subject)->name,
        'book' => optional($assessment->book)->title,
        'score' => $assessment->score,
        'max_score' => $assessment->max_score,
        'percentage' => $assessment->max_score > 0 ? round(($assessment->score / $assessment->max_score) * 100, 2) : 0,
        'className' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'is_assessment' => true,
        'questions' => $questions
    ];
}

}
