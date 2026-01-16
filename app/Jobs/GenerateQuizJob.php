<?php

namespace App\Jobs;

use App\Exceptions\NotEnoughQuestionsException;
use App\Models\AcademicSubject;
use App\Models\Quiz;
use App\Models\Team;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateQuizJob implements ShouldQueue
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
        private string $title,
        private int $durationInMinutes,
        private ?Carbon $startsAt,
        private ?Carbon $endsAt,
        private array $sections
    ) {
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

        try {
            collect($this->sections)->each(function ($section) use (
                &$sections,
                &$multiple_choice_questions,
                &$true_or_false_questions
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
                    throw new NotEnoughQuestionsException;
                }

                $sections[] = [
                    'name' => $section['name'],
                    'type' => $section['type'],
                    'questions' => $questions,
                ];

                ${$section['type']} = array_merge(${$section['type']}, $questions);
            });

            $quiz = new Quiz([
                'title' => $this->title,
                'duration_in_minutes' => $this->durationInMinutes,
                'sections' => $sections,
            ]);

            $quiz->creator()->associate($this->creator);
            $quiz->team()->associate($this->team);

            $this->academicSubject->quizzes()->save($quiz);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
