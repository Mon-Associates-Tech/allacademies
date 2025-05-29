<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookApprovalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'book' => new BookResource($this->whenLoaded('book')),
            'librarian' => new LibrarianResource($this->whenLoaded('librarian')),
            'status' => $this->status,
            'comments' => $this->comments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}