<?php

namespace App\Support;

use App\Models\Quiz;
use App\Models\Worksheet;
use Illuminate\Support\Str;

class Quizzer
{
    private static function incrementCursor(Quiz $quiz, Worksheet $worksheet): void
    {
        [$sectionIndex, $questionIndex] = $worksheet->cursor;

        $questionIndex++;

        if (count($quiz->sections[$sectionIndex]['questions']) <= $questionIndex) {
            $questionIndex = 0;
            $sectionIndex++;
        }

        $worksheet->cursor = [$sectionIndex, $questionIndex];
    }

    private static function durationElapsed(Quiz $quiz, Worksheet $worksheet): bool
    {
        return !$worksheet->ended_at &&
            now() > $worksheet->started_at->addMinutes($quiz->duration_in_minutes);
    }

    private static function questionOutOfBounds(Quiz $quiz, Worksheet $worksheet): bool
    {
        [$sectionIndex, $questionIndex] = $worksheet->cursor;

        return count($quiz->sections) <= $sectionIndex ||
            count($quiz->sections[$sectionIndex]['questions']) <= $questionIndex;
    }

    public static function shouldStopWork(Quiz $quiz, Worksheet $worksheet): bool
    {
        return static::durationElapsed($quiz, $worksheet) ||
            static::questionOutOfBounds($quiz, $worksheet);
    }

    /** @return \App\Models\MultipleChoiceQuestion|\App\Models\TrueOrFalseQuestion|\App\Models\EssayQuestion */
    public static function askQuestion(Quiz $quiz, Worksheet $worksheet)
    {
        [$sectionIndex, $questionIndex] = $worksheet->cursor;

        $section = $quiz->sections[$sectionIndex];

        $questionType = $section['type'];
        $shuffledQuestions = fisher_yates_shuffle($section['questions'], $worksheet->seed);
        $questionId = $shuffledQuestions[$questionIndex];

        $model = (string) Str::of($questionType)->singular()->studly();

        return $model::query()->find($questionId);
    }

    public static function markAnswer(Quiz $quiz, Worksheet $worksheet, string|bool $answer): void
    {
        [$sectionIndex, $questionIndex] = $worksheet->cursor;

        if (
            static::questionOutOfBounds($quiz, $worksheet)
        ) {
            return;
        }

        $question = static::askQuestion($quiz, $worksheet);

        $sheets = $worksheet->sheets;

        $sheets[$sectionIndex][$question->id] = $answer;

        $worksheet->sheets = $sheets;

        static::incrementCursor($quiz, $worksheet);

        $worksheet->save();
    }
}
