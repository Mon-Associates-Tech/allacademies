<?php

namespace App\Models\Book;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;

class BookTableOfContent extends Model
{
    protected $fillable = [
        'book_id',
        'contents'
    ];

    protected $casts = [
        'contents' => 'array'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getFormattedContents()
    {
        $toc = $this->contents;
        if (!$toc) {
            return [];
        }
        return collect($toc)->map(function ($chapter) {
            return [
                'chapter_number' => $chapter['chapter'] ?? 1,
                'title' => $chapter['title'] ?? 'Untitled Chapter',
                'description' => $chapter['description'] ?? '',
                'page_range' => isset($chapter['page_start'], $chapter['page_end'])
                    ? "Pages {$chapter['page_start']}-{$chapter['page_end']}"
                    : '',
                'page_count' => isset($chapter['page_start'], $chapter['page_end'])
                    ? $chapter['page_end'] - $chapter['page_start'] + 1
                    : 0,
                'sections' => $chapter['sections'] ?? []
            ];
        })->toArray();
    }
}
