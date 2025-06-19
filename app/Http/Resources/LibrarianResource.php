<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LibrarianResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'book_approvals_count' => $this->when(isset($this->book_approvals_count), $this->book_approvals_count),
            'book_approvals' => BookApprovalResource::collection($this->whenLoaded('bookApprovals')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('librarians.show', $this->id),
            ],
        ];
    }
}