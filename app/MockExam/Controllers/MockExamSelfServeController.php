<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubscription;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockExamSelfServeController extends Controller
{
    public function __construct(
        private readonly MockExamCreationService $creationService,
    ) {
        // Deliberately no ensureInstructor gate — open to any authenticated user.
    }

    /**
     * List active templates a general user can generate an exam from.
     * Not scoped by user_id — templates belong to instructors, but any
     * active template is fair game for self-serve generation.
     */
    public function index(): View
    {
        $templates = MockExamTemplate::where('is_active', true)
            ->with(['academicSubject', 'academicLevel', 'academicGroup'])
            ->orderBy('name')
            ->get();

        $subscribedSubjectIds = MockExamSubscription::where('user_id', auth()->id())
            ->active()
            ->with('subjects')
            ->get()
            ->filter(fn (MockExamSubscription $sub) => $sub->canCreateExam())
            ->flatMap(fn (MockExamSubscription $sub) => $sub->subjects->pluck('id'))
            ->unique()
            ->values();

        return view('mock-exam.self-serve.index', compact('templates', 'subscribedSubjectIds'));
    }

    /**
     * Quick Generate: build a personal MockExam container for the current
     * user and populate it with a subject exam pulled from the template.
     */
    public function generate(Request $request, MockExamTemplate $template): RedirectResponse
    {
        $subscription = null;

        // ── Subscription gate ───────────────────────────────────────────
        // Comment out the next line to bypass the requirement while
        // testing the generation flow. $subscription stays null above,
        // so nothing else needs to change.
       // $subscription = $this->ensureActiveSubscription($template);

        $exam = $this->creationService->createExam((int) auth()->id(), [
            'title'                     => $template->getDisplayName(),
            'status'                    => 'published',
            'delivery_type'             => 'print',
            'academic_subject_id'       => $template->academic_subject_id,
            'mock_exam_subscription_id' => $subscription?->id,
        ]);

        $result = $this->creationService->createSubjectExamFromTemplate($exam, $template);

        if ($subscription) {
            $subscription->incrementExamUsage();
        }

        $message = "Your exam is ready — {$result['questions_created']} question(s) loaded.";
        if (! empty($result['warnings'])) {
            $message .= ' Note: '.implode(' ', $result['warnings']);
        }

        return redirect()
            ->route('mock-exams.pdf', $exam)
            ->with('success', $message);
    }
    private function ensureActiveSubscription(MockExamTemplate $template): MockExamSubscription
    {
        $subscription = MockExamSubscription::where('user_id', auth()->id())
            ->active()
            ->whereHas('subjects', fn ($q) => $q->where('academic_subjects.id', $template->academic_subject_id))
            ->get()
            ->first(fn (MockExamSubscription $sub) => $sub->canCreateExam());

        abort_unless(
            $subscription,
            403,
            'You need an active mock exam subscription covering this subject, with exam slots remaining, to generate this exam.'
        );

        return $subscription;
    }
}
