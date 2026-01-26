<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accountant extends Model
{
    use BelongsToSchoolEnhanced;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
