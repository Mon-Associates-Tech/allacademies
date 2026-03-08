<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupBookSubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_group' => new StudentGroupResource($this->whenLoaded('studentGroup')),
            'book' => new BookResource($this->whenLoaded('book')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'subscribed_by_type' => $this->subscribed_by_type,
            'subscribed_by_id' => $this->subscribed_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
