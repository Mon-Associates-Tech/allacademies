<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookInventory extends Model
{
    protected $table = 'book_inventories';

    public function books(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Book::class);
    }
}
