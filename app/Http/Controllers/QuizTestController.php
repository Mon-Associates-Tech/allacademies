<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class QuizTestController extends Controller
{
    public function creator()
    {
        return view('quiz-test.creator');
    }

    public function take(Request $request)
    {
        $assignment = Assignment::latest()->first();

        if (! $assignment) {
            return redirect()->route('quiz.test-create')->with('error', 'No quiz found. Please create one first.');
        }

        // Prepare data for QuizEngine
        $quizData = [
            'title' => $assignment->title,
            'instructions' => $assignment->instructions,
            'sections' => $assignment->assignmentSections->map(function ($section) use ($assignment) {
                return [
                    'title' => $section->title,
                    'instructions' => $section->instructions,
                    'duration_minutes' => $section->duration_minutes,
                    'questions' => $assignment->questions ?? [], // Simplification for demo
                ];
            })->toArray(),
        ];

        // If no sections found, create a default one from assignment questions
        if (empty($quizData['sections'])) {
            $quizData['sections'] = [
                [
                    'title' => $assignment->title,
                    'instructions' => $assignment->instructions,
                    'duration_minutes' => 30,
                    'questions' => $assignment->questions ?? [],
                ],
            ];
        }

        return view('quiz-test.take', compact('quizData'));
    }
}
