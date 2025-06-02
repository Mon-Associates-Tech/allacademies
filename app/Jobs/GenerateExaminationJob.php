<?php

namespace App\Jobs;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicSubject;
use App\Models\Examination;
use App\Models\Team;
use App\Models\User;
use App\Templates\TemplateRenderer;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateExaminationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly AcademicSubject $academicSubject,
        private readonly Team            $team,
        private readonly User            $creator,
        private  array           $heading,
        private  array           $sections,
        private array $metadata
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $sections = [];
        $usedQuestions = [
            'multiple_choice_questions' => [],
            'true_or_false_questions' => [],
            'essay_questions' => []
        ];

        try {
            collect($this->sections)->each(function ($section) use (
                &$sections,
                &$usedQuestions
            ) {
                $table = $section['type'];
                $topicIds = collect($section['topics'])->map(fn($id) => (int)$id)->all();
                $subtopics = $section['subtopics'] ?? [];
                $sectionQuestions = [];

                // Handle document upload if present
                if (isset($section['document']) && $section['document'] instanceof UploadedFile) {
                    $file = $section['document'];
                    $path = $file->store('documents', 'public');
                    $section['document'] = $path;
                }

                if (!empty($subtopics)) {
                    // Handle questions with subtopics
                    collect($subtopics)->each(function ($subtopic) use ($table, $topicIds, &$sectionQuestions, &$usedQuestions) {
                        $count = (int)$subtopic['count'];

                        $questions = DB::table($table)
                            ->select($table . '.id')
                            ->join('academic_subtopics', $table . '.academic_subtopic_id', '=', 'academic_subtopics.id')
                            ->where('academic_subtopics.academic_topic_id', $subtopic['topic_id'])
                            ->where('academic_subtopics.id', $subtopic['id'])
                            ->whereNotIn($table . '.id', $usedQuestions[$table])
                            ->inRandomOrder()
                            ->take($count)
                            ->get()
                            ->pluck('id')
                            ->all();

                        if (count($questions) < $count) {
                            throw new NotEnoughQuestionsException();
                        }

                        $sectionQuestions = array_merge($sectionQuestions, $questions);
                    });
                } else {
                    // Handle questions without subtopics (topic-level questions)
                    $questions = DB::table($table)
                        ->select('id')
                        ->whereIn('academic_topic_id', $topicIds)
                        ->whereNull('academic_subtopic_id')
                        ->whereNotIn('id', $usedQuestions[$table])
                        ->inRandomOrder()
                        ->take($section['count'])
                        ->get()
                        ->pluck('id')
                        ->all();

                    if (count($questions) < $section['count']) {
                        throw new NotEnoughQuestionsException();
                    }

                    $sectionQuestions = $questions;
                }

                // Add questions to the tracking array for duplicate prevention
                $usedQuestions[$table] = array_merge($usedQuestions[$table], $sectionQuestions);

                // Build section
                $sections[] = [
                    'name' => $section['name'],
                    'type' => $table,
                    'questions' => $sectionQuestions,
                    'page' => $section['page'] ?? null,
                    'document' => $section['document'] ?? null,
                    'instructions' => $section['instructions'],
                ];
            });

            Log::info('section', $sections);

            $this->heading['down'] = TemplateRenderer::renderTwig($this->heading['instructions']['down'], $this->heading['duration'], $this->heading['title'], $this->metadata);


            $examination = new Examination([
                'title' => $this->heading['title'],
                'heading' => $this->heading,
                'sections' => $sections,
            ]);

            $examination->creator()->associate($this->creator);
            $examination->team()->associate($this->team);

            $this->academicSubject->examinations()->save($examination);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
