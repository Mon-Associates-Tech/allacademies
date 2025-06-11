<?php

namespace App\Services;

use App\Exceptions\NotEnoughQuestionsException;
use App\Exceptions\NoTopicsException;
use App\Models\AcademicSubject;
use App\Models\Examination;
use App\Templates\TemplateRenderer;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionGenerator
{
    public static function generate(
        array           $heading,
        array           $sections,
        array $metadata = []
    ): array
    {
        $usedQuestions = [
            'multiple_choice_questions' => [],
            'true_or_false_questions' => [],
            'essay_questions' => []
        ];

        collect($sections)->each(function ($section) use (
            &$sections,
            &$usedQuestions
        ) {
            $table = $section['type'];
            if(empty($section['topics']) || $section['topics'][0] == 0){
                throw new NoTopicsException();
            }
            $topicIds =  collect($section['topics'])->map(fn($id) => (int)$id)->all();
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
//                        ->select($table . '.id')
                        ->join('academic_subtopics', $table . '.academic_subtopic_id', '=', 'academic_subtopics.id')
                        ->where('academic_subtopics.academic_topic_id', $subtopic['topic_id'])
                        ->where('academic_subtopics.id', $subtopic['id'])
                        ->whereNotIn($table . '.id', $usedQuestions[$table])
                        ->inRandomOrder()
                        ->take($count)
                        ->get()
                        ->all();

                    if (count($questions) < $count) {
                        throw new NotEnoughQuestionsException();
                    }

                    $sectionQuestions = array_merge($sectionQuestions, $questions);
                });
            } else {
                // Handle questions without subtopics (topic-level questions)
                $questions = DB::table($table)
//                    ->select('id')
                    ->whereIn('academic_topic_id', $topicIds)
                    ->whereNull('academic_subtopic_id')
                    ->whereNotIn('id', $usedQuestions[$table])
                    ->inRandomOrder()
                    ->take($section['count'])
                    ->get()
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
                'pdf_images' => $section['pdf_images'] ?? [],
                'extension' => $section['extension'] ?? null,
                'instructions' => $section['instructions'],
            ];
        });
        $sections = array_slice($sections,1 );

        if(($heading['instructions']['up'] !== null) && isset($heading['template']) && $heading['template'] === 'twig') {
            $heading['up'] =  TemplateRenderer::renderTwig($heading['instructions']['up'], $heading['duration'], $heading['title'], $metadata);
        }

        if($heading['instructions']['down'] !== null){
            if(isset($heading['template']) && $heading['template'] === 'twig'){
                $heading['down'] =  TemplateRenderer::renderTwig($heading['instructions']['down'], $heading['duration'], $heading['title'], $metadata);
            }
            if(isset($heading['template']) && $heading['template'] === 'pug'){
                $heading['down'] =  TemplateRenderer::renderPug($heading['instructions']['down'], $heading['duration'], $heading['title'], $metadata);
            }
        }
        return [
            'title' => $heading['title'],
            'heading' => $heading,
            'sections' => $sections,
        ];

    }

    public function createExamination(
        AcademicSubject $academicSubject,
        array $validatedData,
         $team_id,
         $creator_id
    ): Examination {
        $heading = $validatedData['heading'];

        $metadata = $validatedData['metadata'];

        $heading['instructions'] = $heading['instructions']['down'] ?? null;

        try {
            $examination = Examination::create([
                'heading' => $validatedData['heading'],
                'sections' => $validatedData['sections'],
                'title' => $validatedData['title'],
                'academic_subject_id' => $academicSubject->id,
                'creator_id' => $creator_id,
                'team_id' => $team_id,

            ]);

            $examination->creator()->associate($creator_id);
            $examination->team()->associate($team_id);

            $academicSubject->examinations()->save($examination);

            return $examination;

        } catch (Exception $e) {
            Log::error('Examination creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'academic_subject_id' => $academicSubject->id,
                'team_id' => $team_id,
                'creator_id' => $creator_id,
            ]);

            throw $e;
        }
    }


}
