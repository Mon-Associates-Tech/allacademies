<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'students_count' => $this->when(isset($this->students_count), $this->students_count),
            'students' => StudentResource::collection($this->whenLoaded('students')),
            'subscriptions' => GroupBookSubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('student-groups.show', $this->id),
            ],
        ];
    }
}
