<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\Course;
use App\Models\Lms\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Enroll the current user in a course.
     */
    public function enroll(Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        $user = Auth::user();

        // Check if already enrolled
        if ($course->isEnrolled($user)) {
            return redirect()->back()->with('info', 'You are already enrolled in this course.');
        }

        // Check if course is paid and handle payment (simplified - you may want to integrate with payment system)
        if (! $course->is_free && $course->price > 0) {
            // For now, redirect to a payment page or show error
            // In production, integrate with your payment system
            return redirect()->back()->with('error', 'This is a paid course. Payment integration required.');
        }

        // Create enrollment
        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => CourseEnrollment::STATUS_ENROLLED,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('lms.courses.learn', $course->slug)
            ->with('success', 'Successfully enrolled in the course!');
    }

    /**
     * Unenroll the current user from a course.
     */
    public function unenroll(Course $course): RedirectResponse
    {
        $user = Auth::user();

        $enrollment = $course->getEnrollment($user);

        if (! $enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if course is completed - don't allow unenrollment
        if ($enrollment->isCompleted()) {
            return redirect()->back()->with('error', 'Cannot unenroll from a completed course.');
        }

        // Mark as dropped instead of deleting to preserve history
        $enrollment->drop();

        return redirect()->route('my-learning.index')
            ->with('success', 'Successfully unenrolled from the course.');
    }

    /**
     * Display the user's learning dashboard.
     */
    public function myLearning(Request $request): View
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::query()
            ->forUser($user)
            ->active()
            ->with(['course.creator', 'course.chapters'])
            ->latest('enrolled_at')
            ->paginate(12);

        // Get summary stats
        $stats = [
            'total_enrolled' => CourseEnrollment::forUser($user)->active()->count(),
            'in_progress' => CourseEnrollment::forUser($user)->inProgress()->count(),
            'completed' => CourseEnrollment::forUser($user)->completed()->count(),
        ];

        return view('lms.my-learning.index', compact('enrollments', 'stats'));
    }

    /**
     * Display courses in progress.
     */
    public function inProgress(Request $request): View
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::query()
            ->forUser($user)
            ->inProgress()
            ->with(['course.creator', 'course.chapters'])
            ->latest('started_at')
            ->paginate(12);

        return view('lms.my-learning.in-progress', compact('enrollments'));
    }

    /**
     * Display completed courses.
     */
    public function completed(Request $request): View
    {
        $user = Auth::user();

        $enrollments = CourseEnrollment::query()
            ->forUser($user)
            ->completed()
            ->with(['course.creator', 'certificate'])
            ->latest('completed_at')
            ->paginate(12);

        return view('lms.my-learning.completed', compact('enrollments'));
    }

    /**
     * Display enrollments for a specific course (for instructors/admins).
     */
    public function courseEnrollments(Course $course, Request $request): View
    {
        $this->authorize('update', $course);

        $enrollments = $course->enrollments()
            ->with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->latest('enrolled_at')
            ->paginate(20);

        $statuses = CourseEnrollment::getStatuses();

        return view('lms.courses.enrollments', compact('course', 'enrollments', 'statuses'));
    }
}
