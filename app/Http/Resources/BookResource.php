<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'category' => new BookCategoryResource($this->whenLoaded('category')),
            'edition' => $this->edition,
            'publisher' => $this->publisher,
            'pages' => $this->pages,
            'has_hardcopy' => $this->has_hardcopy,
            'has_softcopy' => $this->has_softcopy,
            'additional_info' => $this->additional_info,
            'borrowings' => BookBorrowingResource::collection($this->whenLoaded('borrowings')),
            'subscriptions' => BookSubscriptionResource::collection($this->whenLoaded('subscriptions')),
            'group_subscriptions' => GroupBookSubscriptionResource::collection($this->whenLoaded('groupSubscriptions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'links' => [
                'self' => route('books.show', $this->id),
            ],
        ];
    }
}
