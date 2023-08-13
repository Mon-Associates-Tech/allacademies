<?php

namespace App\Support;

use App\Models\Examination;
use Illuminate\Support\Str;

class Examiner
{
    public static function createSections(Examination $examination)
    {
        return array_map(function ($section) {
            $model = (string) Str::of($section['type'])->singular()->studly()->prepend('App\\Models\\');
            $questions = $model::query()->find($section['questions']);

            return [
                ...$section,
                'questions' => $questions
            ];
        }, $examination->sections);
    }
}
