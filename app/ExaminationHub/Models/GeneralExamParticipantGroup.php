<?php

namespace App\ExaminationHub\Models;

use App\ExaminationHub\Models\GeneralExam;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralExamParticipantGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GeneralExamParticipantGroupMember::class, 'group_id');
    }

       /**
     * Get the parent group (e.g., The Course)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(GeneralExamParticipantGroup::class, 'parent_id')->withTrashed();
    }

    /**
     * Get the child groups (e.g., The Programmes)
     */
    public function children(): HasMany
    {
        return $this->hasMany(GeneralExamParticipantGroup::class, 'parent_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(GeneralExam::class, 'participant_group_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check() && empty($model->deleted_by)) {
                $model->deleted_by = auth()->id();
            }
        });
    }
}
