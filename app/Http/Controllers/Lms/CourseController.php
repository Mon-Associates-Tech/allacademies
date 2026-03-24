<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\Course;
use App\Models\Lms\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses for the current user.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $courses = Course::query()
            ->published()
            ->forAudience($user)
            ->with(['creator', 'school', 'chapters'])
            ->withCount('enrollments')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('difficulty'), function ($query) use ($request) {
                $query->where('difficulty_level', $request->difficulty);
            })
            ->when($request->filled('price'), function ($query) use ($request) {
                if ($request->price === 'free') {
                    $query->free();
                } else {
                    $query->paid();
                }
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereJsonContains('metadata->category', $request->category);
            })
            ->latest('published_at')
            ->paginate(12);

        $difficulties = ['beginner', 'intermediate', 'advanced'];

        return view('lms.courses.index', compact('courses', 'difficulties'));
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): View
    {
        $this->authorize('view', $course);

        $course->load([
            'creator',
            'school',
            'chapters.sections.contents',
        ]);

        $user = Auth::user();
        $enrollment = $course->getEnrollment($user);
        $isEnrolled = $enrollment !== null;

        return view('lms.courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    /**
     * Display courses for management (instructors/admins).
     */
    public function manage(Request $request): View
    {
        $user = Auth::user();

        $baseQuery = Course::query();

        // Filter based on user role
        if ($user->hasAnyRole(['owner', 'admin'])) {
            if ($user->school_id) {
                $baseQuery->where('school_id', $user->school_id);
            }
        } else {
            $baseQuery->where('created_by', $user->id);
        }

        // Get stats for the dashboard cards
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'archived' => (clone $baseQuery)->where('status', 'archived')->count(),
        ];

        $courses = (clone $baseQuery)
            ->with(['creator', 'school'])
            ->withCount(['enrollments', 'chapters'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(15);

        $statuses = ['draft', 'published', 'unpublished', 'archived'];

        return view('lms.courses.manage', compact('courses', 'statuses', 'stats'));
    }

    /**
     * Publish the specified course.
     */
    public function publish(Course $course): RedirectResponse
    {
        $this->authorize('publish', $course);

        // Validate course has content before publishing
        $contentCount = $course->getRequiredContentsCount();
        if ($contentCount === 0) {
            return redirect()->back()->with('error', 'Cannot publish a course without any content.');
        }

        $course->publish();

        return redirect()->back()->with('success', 'Course published successfully.');
    }

    /**
     * Unpublish the specified course.
     */
    public function unpublish(Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->unpublish();

        return redirect()->back()->with('success', 'Course unpublished successfully.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        // Check if course has active enrollments
        $activeEnrollments = $course->enrollments()
            ->whereIn('status', [CourseEnrollment::STATUS_ENROLLED, CourseEnrollment::STATUS_IN_PROGRESS])
            ->count();

        if ($activeEnrollments > 0) {
            return redirect()->back()->with('error', "Cannot delete course with {$activeEnrollments} active enrollment(s). Please wait for students to complete or drop the course.");
        }

        $course->delete();

        return redirect()->route('course-management.index')->with('success', 'Course deleted successfully.');
    }

    /**
     * Display analytics for the specified course.
     */
    public function analytics(Course $course): View
    {
        $this->authorize('update', $course);

        $course->load(['chapters.sections.contents']);

        // Get enrollment statistics
        $enrollmentStats = [
            'total' => $course->enrollments()->count(),
            'enrolled' => $course->enrollments()->enrolled()->count(),
            'in_progress' => $course->enrollments()->inProgress()->count(),
            'completed' => $course->enrollments()->completed()->count(),
            'dropped' => $course->enrollments()->dropped()->count(),
        ];

        // Calculate completion rate
        $completionRate = $enrollmentStats['total'] > 0
            ? round(($enrollmentStats['completed'] / $enrollmentStats['total']) * 100, 1)
            : 0;

        // Get average progress
        $averageProgress = $course->enrollments()
            ->whereIn('status', [CourseEnrollment::STATUS_IN_PROGRESS, CourseEnrollment::STATUS_COMPLETED])
            ->avg('progress_percentage') ?? 0;

        // Get recent enrollments
        $recentEnrollments = $course->enrollments()
            ->with('user')
            ->latest('enrolled_at')
            ->take(10)
            ->get();

        // Get average final grade for completed courses
        $averageGrade = $course->enrollments()
            ->completed()
            ->whereNotNull('final_grade')
            ->avg('final_grade');

        return view('lms.courses.analytics', compact(
            'course',
            'enrollmentStats',
            'completionRate',
            'averageProgress',
            'recentEnrollments',
            'averageGrade'
        ));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        return view('lms.courses.create');
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        return view('lms.courses.edit', compact('course'));
    }

    /**
     * Display the course player for learning.
     */
    public function learn(Course $course): View|RedirectResponse
    {
        $this->authorize('view', $course);

        $user = Auth::user();
        $enrollment = $course->getEnrollment($user);

        // Check if user is enrolled
        if (! $enrollment) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'You must be enrolled in this course to access the learning content.');
        }

        // Check if enrollment is active (not dropped)
        if ($enrollment->isDropped()) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'Your enrollment in this course has been dropped. Please re-enroll to continue.');
        }

        return view('lms.courses.learn', compact('course'));
    }
}
