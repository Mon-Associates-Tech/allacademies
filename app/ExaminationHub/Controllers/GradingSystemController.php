<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Services\GradingSystemService;
use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\ExaminationHubGradeScale;
use App\Models\GradeScale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingSystemController extends Controller
{
    public function __construct(private readonly GradingSystemService $gradingService) {}

    public function index(): View
    {
        // The existing GradeScalePolicy::viewAny() already enforces super-admin / owner / admin.
        $this->authorize('viewAny', GradeScale::class);

        $scales    = $this->gradingService->getScalesForUser(
            auth()->id(),
            auth()->user()->school_id ?? null
        );
        $hasScales = $scales->isNotEmpty();

        return view('examination-hub.grading-system.grading-system-index', compact('scales', 'hasScales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', GradeScale::class);

        $data = $request->validate([
            'grade_label'    => ['required', 'string', 'max:10'],
            'min_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'max_percentage' => ['required', 'integer', 'min:0', 'max:100', 'gte:min_percentage'],
            'grade_point'    => ['nullable', 'numeric', 'min:0', 'max:4'],
            'is_passing'     => ['nullable', 'boolean'],
            'color_code'     => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $this->gradingService->createScale(
                auth()->id(),
                auth()->user()->school_id ?? null,
                $data
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['min_percentage' => $e->getMessage()])->withInput();
        }

        return back()->with('success', "Grade '{$data['grade_label']}' added.");
    }

    public function update(Request $request, ExaminationHubGradeScale $gradeScale): RedirectResponse
    {
        // Re-use the existing policy's update check: must be able to access the school.
        $this->authorize('update', GradeScale::class);

        // Extra ownership guard – only the creator can edit their own scales.
        abort_unless($gradeScale->user_id === auth()->id(), 403);

        $data = $request->validate([
            'grade_label'    => ['required', 'string', 'max:10'],
            'min_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'max_percentage' => ['required', 'integer', 'min:0', 'max:100', 'gte:min_percentage'],
            'grade_point'    => ['nullable', 'numeric', 'min:0', 'max:4'],
            'is_passing'     => ['nullable', 'boolean'],
            'color_code'     => ['nullable', 'string', 'max:20'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        try {
            $this->gradingService->updateScale($gradeScale, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['min_percentage' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Grade scale updated.');
    }

    public function destroy(ExaminationHubGradeScale $gradeScale): RedirectResponse
    {
        $this->authorize('delete', GradeScale::class);
        abort_unless($gradeScale->user_id === auth()->id(), 403);

        $label = $gradeScale->grade_label;
        $this->gradingService->deleteScale($gradeScale);

        return back()->with('success', "Grade '{$label}' deleted.");
    }

    public function initializeDefault(): RedirectResponse
    {
        $this->authorize('create', GradeScale::class);

        $count = $this->gradingService->initializeDefaults(
            auth()->id(),
            auth()->user()->school_id ?? null
        );

        if ($count === 0) {
            return back()->with('info', 'A grading system already exists. No defaults were added.');
        }

        return back()->with('success', "{$count} default grade scales initialised.");
    }
}
