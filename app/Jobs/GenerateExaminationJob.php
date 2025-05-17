<?php

namespace App\Jobs;

use App\Models\AcademicSubject;
use App\Models\Examination;
use App\Models\Team;
use App\Models\User;
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
        private AcademicSubject $academicSubject,
        private Team $team,
        private User $creator,
        private array $heading,
        private array $sections
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
        $multiple_choice_questions = [];
        $true_or_false_questions = [];
        $essay_questions = [];
        $heading = [];

    /*    try {
            collect($this->sections)->each(function ($section) use (
                &$sections,
                &$multiple_choice_questions,
                &$true_or_false_questions,
                &$essay_questions
                ) {
                // TODO: validate that topics are under the right subject;
                $questions = DB::table($section['type'])
                    ->select('id')
                    ->whereIn('academic_topic_id', $section['topics'])
                    ->whereNotIn('id', ${$section['type']})
                    ->inRandomOrder()
                    ->take($section['count'])
                    ->get()
                    ->pluck('id')
                    ->all();


                if (count($questions) < $section['count']) {
                    throw new NotEnoughQuestionsException();
                }

                $sections[] = [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'questions' => $questions,
                    'instructions' => $section['instructions'],
                ];

                ${$section['type']} = array_merge(${$section['type']}, $questions);

            });

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
        }*/

        try{
            $allQuestions = [];

            foreach ($sections as $section) {
                $topicIds = collect($section['topics'])->map(fn($id) => (int) $id)->all();
                $subtopics = $section['subtopics'];
                $table = $section['type'];

                if ($section['document'] instanceof UploadedFile) {
                    $file = $section['document'];
                    $path = $file->store('documents', 'public');
                    $section['document'] = $path;
                }

                foreach ($subtopics as $subtopic) {
                    $count = (int) $subtopic['count'];

                    $questions = DB::table($table)
                        ->join('academic_subtopics', $table . '.academic_subtopic_id', '=', 'academic_subtopics.id')
                        ->whereIn('academic_subtopics.academic_topic_id', $topicIds)
                        ->inRandomOrder()
                        ->select($table . '.id')
                        ->take($count)
                        ->get()
                        ->pluck('id');

                    $allQuestions = array_merge($allQuestions, $questions->all());
                }

                $sections[] = [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'questions' => $allQuestions,
                    'page' => $section['page'] ?? null,
                    'document' => $section['document'],
                    'instructions' => $section['instructions'],
                ];


            }

            array_shift($sections);

            $examination = new Examination([
                'title' => $heading['title'],
                'heading' => $heading,
                'sections' => $sections,
            ]);

            $examination->creator()->associate($this->creator);
            $examination->team()->associate($this->team);

            $this->academicSubject->examinations()->save($examination);
        }catch (Exception $exception){
            Log::info($exception->getMessage());
        }
    }
}
