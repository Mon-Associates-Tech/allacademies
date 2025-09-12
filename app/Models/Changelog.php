<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{
    protected $fillable = [
        'title',
        'task_name',
        'task_description',
        'additional_info',
        'completed_items'
    ];

    protected $casts = [
        'completed_items' => 'array'
    ];
}
