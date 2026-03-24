<?php

namespace App\Livewire\Courses;

use App\Models\Lms\Course;
use App\Models\Lms\CourseContent;
use App\Models\Lms\CourseEnrollment;
use App\Models\Lms\CourseProgress;
use App\Models\Lms\CourseSection;
use App\Services\Lms\CourseProgressService;
use Livewire\Component;

class CoursePlayer extends Component
{
    public Course $course;

    public ?CourseEnrollment $enrollment = null;

    public ?CourseContent $currentContent = null;

    public bool $sidebarOpen = true;

    public ?int $selectedChapterId = null;

    public ?int $selectedSectionId = null;

    protected CourseProgressService $progressService;

    public function boot(CourseProgressService $progressService): void
    {
        $this->progressService = $progressService;
    }

    public function mount(Course $course): void
    {
        $this->course = $course->load(['chapters.sections.contents']);

        // Get or create enrollment
        $this->enrollment = $this->progressService->enrollUser($course, auth()->user());

        // Start course if not started
        if ($this->enrollment->isEnrolled()) {
            $this->progressService->startCourse($this->enrollment);
            $this->enrollment->refresh();
        }

        // Load next content or first content
        $this->currentContent = $this->progressService->getNextContent($this->enrollment)
            ?? $this->getFirstContent();

        if ($this->currentContent) {
            $this->selectedSectionId = $this->currentContent->section_id;
            $this->selectedChapterId = $this->currentContent->section->chapter_id;
        }
    }

    public function selectContent(int $contentId): void
    {
        $content = CourseContent::with('section.chapter')->findOrFail($contentId);

        // Verify content belongs to this course
        if ($content->section->chapter->course_id !== $this->course->id) {
            return;
        }

        $this->currentContent = $content;
        $this->selectedSectionId = $content->section_id;
        $this->selectedChapterId = $content->section->chapter_id;

        // Mark as started if not already
        $this->progressService->getOrCreateProgress($this->enrollment, $content);
    }

    public function updateVideoProgress(int $watchedSeconds): void
    {
        if (! $this->currentContent || ! $this->currentContent->isVideo()) {
            return;
        }

        $this->progressService->trackVideoProgress(
            $this->enrollment,
            $this->currentContent,
            $watchedSeconds
        );

        $this->enrollment->refresh();
    }

    public function updateAudioProgress(int $listenedSeconds): void
    {
        if (! $this->currentContent || ! $this->currentContent->isAudio()) {
            return;
        }

        $this->progressService->trackAudioProgress(
            $this->enrollment,
            $this->currentContent,
            $listenedSeconds
        );

        $this->enrollment->refresh();
    }

    public function markTextAsRead(): void
    {
        if (! $this->currentContent || ! $this->currentContent->isText()) {
            return;
        }

        $wordCount = $this->currentContent->word_count ?? 100;

        $this->progressService->trackTextProgress(
            $this->enrollment,
            $this->currentContent,
            $wordCount,
            $wordCount
        );

        $this->enrollment->refresh();
        $this->dispatch('content-completed');
    }

    public function submitQuiz(array $answers): void
    {
        if (! $this->currentContent || ! $this->currentContent->isQuiz()) {
            return;
        }

        // Calculate score (simplified - would integrate with Quiz model)
        $score = $this->calculateQuizScore($answers);
        $passed = $score >= ($this->currentContent->getCompletionThreshold());

        $this->progressService->trackQuizCompletion(
            $this->enrollment,
            $this->currentContent,
            $score,
            $passed
        );

        $this->enrollment->refresh();

        if ($passed) {
            $this->dispatch('quiz-passed', score: $score);
        } else {
            $this->dispatch('quiz-failed', score: $score);
        }
    }

    public function markContentComplete(): void
    {
        if (! $this->currentContent) {
            return;
        }

        $this->progressService->markContentComplete($this->enrollment, $this->currentContent);
        $this->enrollment->refresh();

        $this->dispatch('content-completed');
    }

    public function goToNextContent(): void
    {
        $nextContent = $this->progressService->getNextContent($this->enrollment);

        if ($nextContent) {
            $this->selectContent($nextContent->id);
        } else {
            // Course completed
            $this->dispatch('course-completed');
        }
    }

    public function goToPreviousContent(): void
    {
        $allContents = $this->getAllContentsOrdered();
        $currentIndex = $allContents->search(fn ($c) => $c->id === $this->currentContent?->id);

        if ($currentIndex > 0) {
            $this->selectContent($allContents[$currentIndex - 1]->id);
        }
    }

    public function toggleSidebar(): void
    {
        $this->sidebarOpen = ! $this->sidebarOpen;
    }

    public function toggleChapter(int $chapterId): void
    {
        $this->selectedChapterId = $this->selectedChapterId === $chapterId ? null : $chapterId;
    }

    public function toggleSection(int $sectionId): void
    {
        $this->selectedSectionId = $this->selectedSectionId === $sectionId ? null : $sectionId;
    }

    public function isContentCompleted(int $contentId): bool
    {
        return $this->progressService->isContentComplete(
            $this->enrollment,
            CourseContent::find($contentId)
        );
    }

    public function getContentProgress(int $contentId): ?CourseProgress
    {
        return $this->progressService->getContentProgress(
            $this->enrollment,
            CourseContent::find($contentId)
        );
    }

    /**
     * Get section progress data for the sidebar display.
     *
     * @return array{is_complete: bool, percentage: float, total: int, completed: int}
     */
    public function getSectionProgressData(CourseSection $section): array
    {
        return $this->progressService->getSectionProgress($this->enrollment, $section);
    }

    /**
     * Get content progress data for the sidebar display.
     *
     * @return array{is_completed: bool, progress_percentage: float}
     */
    public function getContentProgressData(CourseContent $content): array
    {
        $progress = $this->progressService->getContentProgress($this->enrollment, $content);

        return [
            'is_completed' => $progress?->is_completed ?? false,
            'progress_percentage' => $progress?->getProgressPercentage() ?? 0,
        ];
    }

    protected function getFirstContent(): ?CourseContent
    {
        $firstChapter = $this->course->chapters()->orderBy('order')->first();
        if (! $firstChapter) {
            return null;
        }

        $firstSection = $firstChapter->sections()->orderBy('order')->first();
        if (! $firstSection) {
            return null;
        }

        return $firstSection->contents()->orderBy('order')->first();
    }

    protected function getAllContentsOrdered(): \Illuminate\Support\Collection
    {
        return $this->course->chapters()
            ->orderBy('order')
            ->with(['sections' => fn ($q) => $q->orderBy('order')->with(['contents' => fn ($q2) => $q2->orderBy('order')])])
            ->get()
            ->flatMap->sections
            ->flatMap->contents;
    }

    protected function calculateQuizScore(array $answers): float
    {
        // Simplified scoring - would integrate with actual Quiz model
        if (empty($answers)) {
            return 0;
        }

        // This would be replaced with actual quiz evaluation logic
        return 85.0;
    }

    public function render()
    {
        $chapters = $this->course->chapters()->orderBy('order')->with([
            'sections' => fn ($q) => $q->orderBy('order')->with([
                'contents' => fn ($q2) => $q2->orderBy('order'),
            ]),
        ])->get();

        $completedContentIds = CourseProgress::where('enrollment_id', $this->enrollment->id)
            ->where('is_completed', true)
            ->pluck('content_id')
            ->toArray();

        // Calculate previous and next content for navigation
        $previousContent = null;
        $nextContent = null;

        if ($this->currentContent) {
            $allContents = $this->getAllContentsOrdered();
            $currentIndex = $allContents->search(fn ($c) => $c->id === $this->currentContent->id);

            if ($currentIndex !== false) {
                if ($currentIndex > 0) {
                    $previousContent = $allContents[$currentIndex - 1];
                }
                if ($currentIndex < $allContents->count() - 1) {
                    $nextContent = $allContents[$currentIndex + 1];
                }
            }
        }

        return view('livewire.courses.course-player', [
            'chapters' => $chapters,
            'completedContentIds' => $completedContentIds,
            'overallProgress' => $this->enrollment->progress_percentage,
            'previousContent' => $previousContent,
            'nextContent' => $nextContent,
        ]);
    }
}
