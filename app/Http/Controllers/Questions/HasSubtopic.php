<?php

namespace App\Http\Controllers\Questions;

use App\Models\AcademicSubtopic;

trait HasSubtopic
{
    public function getSubtopicId($question = null, $request = null)
    {
        $subtopicId = null;
        if (isset($request->subtopic)) {
            if (is_numeric($request->subtopic)) {
                $subtopic = AcademicSubtopic::find($request->subtopic);
                $subtopicId = $subtopic->id;
            } else {
                $subtopic = AcademicSubtopic::firstOrCreate(
                    ['name' => $request->subtopic, 'academic_topic_id' => $question->academic_topic_id]
                );
                $subtopicId = $subtopic->id;
            }

        }

        return $subtopicId;
    }
}
