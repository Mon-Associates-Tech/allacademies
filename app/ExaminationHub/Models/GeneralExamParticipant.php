<?php

namespace App\ExaminationHub\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class GeneralExamParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'verification_token',
        'email_verified_at',
        'verification_sent_at',
        'result_access_token',
        'user_id',
        'student_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_sent_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->result_access_token)) {
                $participant->result_access_token = self::generateUniqueResultToken();
            }
            if (empty($participant->verification_token)) {
                $participant->verification_token = Str::random(64);
            }
        });
    }

    public static function generateUniqueResultToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('result_access_token', $token)->exists());

        return $token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function submissions(): MorphMany
    {
        return $this->morphMany(GeneralExamSubmission::class, 'participant');
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => now(),
            'verification_token' => null,
        ])->save();
    }

    public function sendVerificationEmail(): void
    {
        $this->update(['verification_sent_at' => now()]);

        // Dispatch notification/mail job
        // This will be implemented in the notification service
    }

    public function canResendVerification(): bool
    {
        if (! $this->verification_sent_at) {
            return true;
        }

        // Allow resend after 1 minute
        return $this->verification_sent_at->addMinute()->isPast();
    }

    public static function findByEmail(string $email): ?self
    {
        return self::where('email', strtolower($email))->first();
    }

    public static function findByVerificationToken(string $token): ?self
    {
        return self::where('verification_token', $token)->first();
    }

    public static function findByResultToken(string $token): ?self
    {
        return self::where('result_access_token', $token)->first();
    }

    public function getSubmissionForAssignment(GeneralExam $assignment): ?GeneralExamSubmission
    {
        return $this->submissions()
            ->where('general_exam_id', $assignment->id)
            ->first();
    }

    public function hasSubmittedAssignment(GeneralExam $assignment): bool
    {
        return $this->submissions()
            ->where('general_exam_id', $assignment->id)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    public function linkToUser(User $user): void
    {
        $this->update(['user_id' => $user->id]);
    }

    public function linkToStudent(Student $student): void
    {
        $this->update([
            'student_id' => $student->id,
            'user_id' => $student->user_id,
        ]);
    }

    public function getMorphClass(): string
    {
        // Store new records with canonical alias; old aliases are covered in morph map
        return 'participant';
    }
}
