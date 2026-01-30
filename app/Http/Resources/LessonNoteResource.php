<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonNoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'file_path' => $this->file_path,
            'file_url' => $this->file_path ? url('storage/'.$this->file_path) : null,
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'lesson' => new LessonResource($this->whenLoaded('lesson')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'topic' => new TopicResource($this->whenLoaded('topic')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
