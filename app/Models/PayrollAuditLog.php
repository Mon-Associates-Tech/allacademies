<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayrollAuditLog extends Model
{
    use BelongsToSchool, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'payload',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function logAction(string $action, Model $subject, array $payload = []): void
    {
        static::create([
            'school_id' => auth()->user()->school_id ?? $subject->school_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
