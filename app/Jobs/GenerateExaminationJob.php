<?php

namespace App\Jobs;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicSubject;
use App\Models\Examination;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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
        private string $heading_type,
        private string $title,
        private string $date,
        private string $start,
        private string $end,
        private string $instructions,
        private array $sections,
        private ?string $examiners
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sections = [];
        $multiple_choice_questions = [];
        $true_or_false_questions = [];
        $essay_questions = [];
        $heading = [];

        try {
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
                ];

                ${$section['type']} = array_merge(${$section['type']}, $questions);
            });

            $heading = [
                'heading_type' => $this->heading_type,
                'date' => $this->date,
                'start' => $this->start,
                'end' => $this->end,
                'instructions' => $this->instructions,
            ];

            $examination = new Examination([
                'title' => $this->title,
                'heading' => $heading,
                'sections' => $sections,
                'examiners' => $this->examiners,
            ]);

            $examination->creator()->associate($this->creator);
            $examination->team()->associate($this->team);

            $this->academicSubject->examinations()->save($examination);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
