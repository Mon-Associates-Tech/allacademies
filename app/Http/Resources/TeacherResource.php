<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'student_groups_count' => $this->when(isset($this->student_groups_count), $this->student_groups_count),
            'lessons_count' => $this->when(isset($this->lessons_count), $this->lessons_count),
            'lesson_notes_count' => $this->when(isset($this->lesson_notes_count), $this->lesson_notes_count),
            'student_groups' => StudentGroupResource::collection($this->whenLoaded('studentGroups')),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'lesson_notes' => LessonNoteResource::collection($this->whenLoaded('lessonNotes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('teachers.show', $this->id),
            ],
        ];
    }
}
