<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExamGradeScale;
use App\MockExam\Services\MockExamGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockExamGradeScaleController extends Controller
{
    public function __construct(
        private readonly MockExamGradingService $gradingService
    ) {}

    public function index(): View
    {
        $scales    = $this->gradingService->getScalesForUser(auth()->id());
        $hasScales = $scales->isNotEmpty();

        return view('mock-exam.grading.index', compact('scales', 'hasScales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateScale($request);

        try {
            $this->gradingService->createScale(auth()->id(), $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['min_percentage' => $e->getMessage()])->withInput();
        }

        return back()->with('success', "Grade '{$data['grade_label']}' added.");
    }

    public function update(Request $request, MockExamGradeScale $gradeScale): RedirectResponse
    {
        abort_unless($gradeScale->user_id === auth()->id(), 403);

        $data = $this->validateScale($request, withActive: true);

        try {
            $this->gradingService->updateScale($gradeScale, $data);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['min_percentage' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Grade scale updated.');
    }

    public function destroy(MockExamGradeScale $gradeScale): RedirectResponse
    {
        abort_unless($gradeScale->user_id === auth()->id(), 403);

        $label = $gradeScale->grade_label;
        $this->gradingService->deleteScale($gradeScale);

        return back()->with('success', "Grade '{$label}' deleted.");
    }

    public function initialize(): RedirectResponse
    {
        $count = $this->gradingService->initializeDefaults(auth()->id());

        return $count === 0
            ? back()->with('info', 'A grading system already exists.')
            : back()->with('success', "{$count} default grade scales initialised.");
    }

    private function validateScale(Request $request, bool $withActive = false): array
    {
        $rules = [
            'grade_label'    => ['required', 'string', 'max:10'],
            'min_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'max_percentage' => ['required', 'integer', 'min:0', 'max:100', 'gte:min_percentage'],
            'grade_point'    => ['nullable', 'numeric', 'min:0', 'max:4'],
            'is_passing'     => ['nullable', 'boolean'],
            'color_code'     => ['nullable', 'string', 'max:20'],
        ];

        if ($withActive) {
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        return $request->validate($rules);
    }
}
