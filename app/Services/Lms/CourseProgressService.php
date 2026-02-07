<?php

namespace App\Services\Lms;

use App\Models\Lms\Course;
use App\Models\Lms\CourseContent;
use App\Models\Lms\CourseEnrollment;
use App\Models\Lms\CourseProgress;
use App\Models\Lms\CourseSection;
use App\Models\User;

class CourseProgressService
{
    public function enrollUser(Course $course, User $user): CourseEnrollment
    {
        return CourseEnrollment::firstOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'status' => CourseEnrollment::STATUS_ENROLLED,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]
        );
    }

    public function startCourse(CourseEnrollment $enrollment): bool
    {
        if ($enrollment->status !== CourseEnrollment::STATUS_ENROLLED) {
            return false;
        }

        return $enrollment->update([
            'status' => CourseEnrollment::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    public function getOrCreateProgress(CourseEnrollment $enrollment, CourseContent $content): CourseProgress
    {
        return CourseProgress::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'content_id' => $content->id,
            ],
            [
                'is_completed' => false,
                'progress_value' => 0,
                'progress_max' => $this->getProgressMax($content),
                'started_at' => now(),
            ]
        );
    }

    public function trackVideoProgress(
        CourseEnrollment $enrollment,
        CourseContent $content,
        int $watchedSeconds
    ): CourseProgress {
        $progress = $this->getOrCreateProgress($enrollment, $content);

        $progress->updateVideoProgress($watchedSeconds, $content->duration_seconds ?? 0);

        if ($progress->is_completed) {
            $this->updateSectionProgress($enrollment, $content->section);
        }

        return $progress;
    }

    public function trackAudioProgress(
        CourseEnrollment $enrollment,
        CourseContent $content,
        int $listenedSeconds
    ): CourseProgress {
        return $this->trackVideoProgress($enrollment, $content, $listenedSeconds);
    }

    public function trackTextProgress(
        CourseEnrollment $enrollment,
        CourseContent $content,
        int $paragraphsRead,
        int $totalParagraphs
    ): CourseProgress {
        $progress = $this->getOrCreateProgress($enrollment, $content);

        $progress->updateTextProgress($paragraphsRead, $totalParagraphs);

        if ($progress->is_completed) {
            $this->updateSectionProgress($enrollment, $content->section);
        }

        return $progress;
    }

    public function trackQuizCompletion(
        CourseEnrollment $enrollment,
        CourseContent $content,
        float $score,
        bool $passed
    ): CourseProgress {
        $progress = $this->getOrCreateProgress($enrollment, $content);

        $progress->updateQuizProgress($score, $passed);

        if ($progress->is_completed) {
            $this->updateSectionProgress($enrollment, $content->section);
        }

        return $progress;
    }

    public function markContentComplete(CourseEnrollment $enrollment, CourseContent $content): CourseProgress
    {
        $progress = $this->getOrCreateProgress($enrollment, $content);

        $progress->markAsCompleted();

        $this->updateSectionProgress($enrollment, $content->section);

        return $progress;
    }

    public function updateSectionProgress(CourseEnrollment $enrollment, CourseSection $section): void
    {
        if ($this->isSectionComplete($enrollment, $section)) {
            $this->updateOverallProgress($enrollment);
            $this->checkCourseCompletion($enrollment);
        }
    }

    public function isSectionComplete(CourseEnrollment $enrollment, CourseSection $section): bool
    {
        $requiredContents = $section->contents()->where('is_required', true)->pluck('id');

        if ($requiredContents->isEmpty()) {
            return true;
        }

        $completedContents = CourseProgress::where('enrollment_id', $enrollment->id)
            ->whereIn('content_id', $requiredContents)
            ->where('is_completed', true)
            ->count();

        return $completedContents >= $requiredContents->count();
    }

    public function getSectionProgress(CourseEnrollment $enrollment, CourseSection $section): array
    {
        $requiredContents = $section->contents()->where('is_required', true)->count();
        $completedContents = CourseProgress::where('enrollment_id', $enrollment->id)
            ->whereIn('content_id', $section->contents()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $percentage = $requiredContents > 0 ? ($completedContents / $requiredContents) * 100 : 0;

        return [
            'total' => $requiredContents,
            'completed' => $completedContents,
            'percentage' => min(100, $percentage),
            'is_complete' => $completedContents >= $requiredContents,
        ];
    }

    public function calculateOverallProgress(CourseEnrollment $enrollment): float
    {
        $course = $enrollment->course;

        $totalContents = $course->chapters()
            ->with('sections.contents')
            ->get()
            ->flatMap->sections
            ->flatMap->contents
            ->where('is_required', true)
            ->count();

        if ($totalContents === 0) {
            return 0;
        }

        $completedContents = CourseProgress::where('enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->count();

        return min(100, ($completedContents / $totalContents) * 100);
    }

    public function updateOverallProgress(CourseEnrollment $enrollment): void
    {
        $progress = $this->calculateOverallProgress($enrollment);
        $enrollment->updateProgress($progress);
    }

    public function checkCourseCompletion(CourseEnrollment $enrollment): bool
    {
        $progress = $this->calculateOverallProgress($enrollment);

        if ($progress >= 100 && $enrollment->status !== CourseEnrollment::STATUS_COMPLETED) {
            $finalGrade = $this->calculateFinalGrade($enrollment);

            $enrollment->complete($finalGrade);

            // Issue certificate
            app(CertificateService::class)->issueCertificate($enrollment);

            return true;
        }

        return false;
    }

    public function calculateFinalGrade(CourseEnrollment $enrollment): float
    {
        $quizProgress = CourseProgress::where('enrollment_id', $enrollment->id)
            ->whereNotNull('quiz_score')
            ->get();

        if ($quizProgress->isEmpty()) {
            return 100.0;
        }

        return $quizProgress->avg('quiz_score') ?? 0;
    }

    public function getContentProgress(CourseEnrollment $enrollment, CourseContent $content): ?CourseProgress
    {
        return CourseProgress::where('enrollment_id', $enrollment->id)
            ->where('content_id', $content->id)
            ->first();
    }

    public function isContentComplete(CourseEnrollment $enrollment, CourseContent $content): bool
    {
        $progress = $this->getContentProgress($enrollment, $content);

        return $progress?->is_completed ?? false;
    }

    public function getNextContent(CourseEnrollment $enrollment): ?CourseContent
    {
        $course = $enrollment->course;
        $completedContentIds = CourseProgress::where('enrollment_id', $enrollment->id)
            ->where('is_completed', true)
            ->pluck('content_id');

        foreach ($course->chapters()->ordered()->get() as $chapter) {
            foreach ($chapter->sections()->ordered()->get() as $section) {
                foreach ($section->contents()->ordered()->get() as $content) {
                    if (! $completedContentIds->contains($content->id)) {
                        return $content;
                    }
                }
            }
        }

        return null;
    }

    protected function getProgressMax(CourseContent $content): int
    {
        if ($content->isVideo() || $content->isAudio()) {
            return $content->duration_seconds ?? 100;
        }

        if ($content->isText()) {
            return $content->word_count ?? 100;
        }

        return 100;
    }
}
