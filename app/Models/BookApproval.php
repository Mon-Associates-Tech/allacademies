<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'librarian_id',
        'status',
        'comments',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function librarian()
    {
        return $this->belongsTo(Librarian::class);
    }
}
