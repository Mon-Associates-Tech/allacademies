<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'topics_count' => $this->when(isset($this->topics_count), $this->topics_count),
            'topics' => TopicResource::collection($this->whenLoaded('topics')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
