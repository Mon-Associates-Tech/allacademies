<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentSeeder extends Seeder
{
    public function run()
    {
        // Get the first student (or adjust as needed)
        $student = Student::find(401);

        if (! $student) {
            $this->command->error('No student found. Please seed students first.');

            return;
        }

        // Clear existing assessments for this student
        Assessment::where('student_id', $student->id)->delete();

        // Define some sample assessment titles and subjects
        $titles = [
            'Math Diagnostic Test',
            'Science Quiz',
            'History Midterm',
            'English Essay',
            'Physics Final Exam',
            'Chemistry Review',
            'Biology Practice',
            'Algebra Mastery',
            'Grammar Skills',
            'Geography Map Test',
        ];

        $subjects = ['Math', 'Science', 'History', 'English', 'Physics', 'Chemistry', 'Biology'];

        $statuses = ['completed', 'in_progress', 'needs_grading'];
        $books = ['Textbook A', 'Workbook B', 'Guide C', 'Practice D'];

        // Generate 10 assessments with varied dates
        for ($i = 0; $i < 10; $i++) {
            $startDate = Carbon::now()->subDays(rand(1, 60))->setTime(rand(8, 19), rand(0, 59));
            $endDate = (clone $startDate)->addMinutes(rand(30, 120));

            $score = rand(40, 100);
            $maxScore = 100;

            $assessment = Assessment::create([
                'student_id' => $student->id,
                'subject_id' => rand(20, 100),
                'topic_id' => rand(10, 20),
                'book_id' => rand(1, 5),
                'title' => $titles[$i % count($titles)],
                'score' => $score,
                'max_score' => $maxScore,
                'percentage_score' => round(($score / $maxScore) * 100, 2),
                'status' => $statuses[array_rand($statuses)],
                'start_time' => $startDate,
                'end_time' => $endDate,
                'created_at' => $startDate,
                'updated_at' => $endDate,
            ]);

            DB::table('assessment_responses')->insert([
                'assessment_id' => $assessment->id,
                'data' => json_encode([
                    'questions' => $this->generateQuestions(),
                    'byType' => [],
                ]),
            ]);
        }

        $this->command->info("✅ Created 10 sample assessments for student ID {$student->id}");
    }

    private function generateQuestions()
    {
        $questions = [];
        $questionTypes = ['Multiple Choice', 'True/False', 'Short Answer'];
        $subjects = ['Math', 'Science', 'History', 'English'];

        for ($q = 1; $q < rand(3, 6); $q++) {
            $isCorrect = rand(0, 1) === 1;

            $questions[] = [
                'question' => "Sample question #{$q} on ".$subjects[array_rand($subjects)],
                'type' => $questionTypes[array_rand($questionTypes)],
                'user_answer' => $isCorrect ? "Answer Q{$q}" : 'Wrong answer',
                'correct_answer' => "Correct answer for Q{$q}",
                'is_correct' => $isCorrect,
            ];
        }

        return $questions;
    }
}
