<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSubject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    use AcademicGroupLogs;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'academic_level_id',
        'description'
    ];

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicTopics()
    {
        return $this->hasMany(AcademicTopic::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'academic_subject_subscription');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function subtopic()
    {
        return $this->hasManyThrough(AcademicSubtopic::class, AcademicTopic::class);
    }

    public function topics()
    {
        return $this->hasMany(AcademicTopic::class);
    }

    public function essayQuestions()
    {
        return $this->hasManyThrough(
            EssayQuestion::class,  // or change to a base Question model if unified later
            AcademicSubtopic::class,
            'academic_topic_id', // Foreign key on Subtopic
            'academic_subtopic_id', // Foreign key on EssayQuestion
            'id', // Local key on Subject
            'id'  // Local key on Subtopic
        )->whereHas('subtopic.academicTopic', function ($q) {
            $q->whereColumn('academic_topics.academic_subject_id', 'id');
        });
    }


    public function mcqQuestions()
    {
        return $this->hasManyThrough(
            MultipleChoiceQuestion::class,  // or change to a base Question model if unified later
            AcademicSubtopic::class,
            'academic_topic_id', // Foreign key on Subtopic
            'academic_subtopic_id', // Foreign key on EssayQuestion
            'id', // Local key on Subject
            'id'  // Local key on Subtopic
        )->whereHas('subtopic.academicTopic', function ($q) {
            $q->whereColumn('academic_topics.academic_subject_id', 'id');
        });
    }


    public function trueFalseQuestions()
    {
        return $this->hasManyThrough(
            TrueOrFalseQuestion::class,  // or change to a base Question model if unified later
            AcademicSubtopic::class,
            'academic_topic_id', // Foreign key on Subtopic
            'academic_subtopic_id', // Foreign key on EssayQuestion
            'id', // Local key on Subject
            'id'  // Local key on Subtopic
        )->whereHas('subtopic.academicTopic', function ($q) {
            $q->whereColumn('academic_topics.academic_subject_id', 'id');
        });
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'subject_id');
    }

    public function lessonNotes()
    {
        return $this->hasMany(LessonNote::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher', 'subject_id', 'teacher_id')
            ->withTimestamps()
            ->withPivot('is_primary', 'notes');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function studentsWithIndividualAccess(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_subject')
            ->withTimestamps()
            ->withPivot('is_active', 'assigned_by', 'notes', 'assigned_at');
    }

    public function studentsWithAccess()
    {
        // Get students who have access through academic level OR individual assignment
        return Student::where(function ($query) {
            $query->whereHas('academicLevel', function ($levelQuery) {
                $levelQuery->whereHas('academicSubjects', function ($subjectQuery) {
                    $subjectQuery->where('academic_subjects.id', $this->id);
                });
            })
                ->orWhereHas('individualSubjects', function ($individualQuery) {
                    $individualQuery->where('academic_subjects.id', $this->id)
                        ->wherePivot('is_active', true);
                });
        })
            // Exclude students who have this subject individually marked as inactive
            ->whereDoesntHave('individualSubjects', function ($query) {
                $query->where('academic_subjects.id', $this->id)
                    ->wherePivot('is_active', false);
            });
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'subject_id');
    }

    public function getQuizCreateRoute()
    {
        return route('quizzes.create', [
            'academic_group' => $this->academicLevel->academicGroup->id,
            'academic_level' => $this->academicLevel->id,
            'academic_subject' => $this->id
        ]);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function subjectNotes()
    {
        return $this->hasMany(Note::class);
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(AcademicResource::class, 'resourceable');
    }

    public function todos(): MorphMany
    {
        return $this->morphMany(Todo::class, 'todoable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
