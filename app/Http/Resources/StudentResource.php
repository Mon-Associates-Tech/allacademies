<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'group' => new StudentGroupResource($this->whenLoaded('group')),
            'borrowed_books_count' => $this->when(isset($this->borrowed_books_count), $this->borrowed_books_count),
            'active_subscriptions_count' => $this->when(isset($this->active_subscriptions_count), $this->active_subscriptions_count),
            'assessments_count' => $this->when(isset($this->assessments_count), $this->assessments_count),
            'borrowed_books' => BookBorrowingResource::collection($this->whenLoaded('borrowedBooks')),
            'subscriptions' => BookSubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'assessments' => AssessmentResource::collection($this->whenLoaded('assessments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('students.show', $this->id),
            ],
        ];
    }
}