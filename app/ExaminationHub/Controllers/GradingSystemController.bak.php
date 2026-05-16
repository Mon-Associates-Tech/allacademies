<?php

namespace App\ExaminationHub\Controllers;

use App\Http\Controllers\Controller;
use App\ExaminationHub\Models\AcademicLevel;
use App\ExaminationHub\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingSystemController extends Controller
{
    public function index(): View
    {
        $schoolId = auth()->user()->school_id;

        $gradeScales = GradeScale::where('school_id', $schoolId)
            ->with('academicLevel')
            ->orderBy('is_default', 'desc')
            ->orderBy('academic_level_id')
            ->orderBy('min_score', 'desc')
            ->get();

        $academicLevels = AcademicLevel::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        return view('examination-hub.grading-system.index', compact('gradeScales', 'academicLevels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_level_id' => ['nullable', 'exists:academic_levels,id'],
            'name' => ['required', 'string', 'max:255'],
            'min_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_score' => ['required', 'numeric', 'min:0', 'max:100', 'gte:min_score'],
            'letter_grade' => ['required', 'string', 'max:10'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $validated['is_default'] = $request->boolean('is_default');

        GradeScale::create($validated);

        return redirect()->route('examination-hub.grading-system.index')
            ->with('success', 'Grade scale created successfully');
    }

    public function update(Request $request, GradeScale $gradeScale)
    {
        $this->authorize('update', $gradeScale);

        $validated = $request->validate([
            'academic_level_id' => ['nullable', 'exists:academic_levels,id'],
            'name' => ['required', 'string', 'max:255'],
            'min_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'max_score' => ['required', 'numeric', 'min:0', 'max:100', 'gte:min_score'],
            'letter_grade' => ['required', 'string', 'max:10'],
            'grade_point' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'remarks' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');

        $gradeScale->update($validated);

        return redirect()->route('examination-hub.grading-system.index')
            ->with('success', 'Grade scale updated successfully');
    }

    public function destroy(GradeScale $gradeScale)
    {
        $this->authorize('delete', $gradeScale);

        $gradeScale->delete();

        return redirect()->route('examination-hub.grading-system.index')
            ->with('success', 'Grade scale deleted successfully');
    }

    public function initializeDefault()
    {
        $schoolId = auth()->user()->school_id;

        // Check if default grades already exist
        if (GradeScale::where('school_id', $schoolId)->where('is_default', true)->exists()) {
            return redirect()->route('examination-hub.grading-system.index')
                ->with('info', 'Default grading system already exists');
        }

        $defaultGrades = [
            ['name' => 'Excellent', 'min_score' => 90, 'max_score' => 100, 'letter_grade' => 'A+', 'grade_point' => 4.0, 'remarks' => 'Outstanding performance'],
            ['name' => 'Very Good', 'min_score' => 80, 'max_score' => 89, 'letter_grade' => 'A', 'grade_point' => 3.7, 'remarks' => 'Excellent performance'],
            ['name' => 'Good', 'min_score' => 70, 'max_score' => 79, 'letter_grade' => 'B', 'grade_point' => 3.0, 'remarks' => 'Good performance'],
            ['name' => 'Satisfactory', 'min_score' => 60, 'max_score' => 69, 'letter_grade' => 'C', 'grade_point' => 2.0, 'remarks' => 'Satisfactory performance'],
            ['name' => 'Pass', 'min_score' => 50, 'max_score' => 59, 'letter_grade' => 'D', 'grade_point' => 1.0, 'remarks' => 'Minimum pass'],
            ['name' => 'Fail', 'min_score' => 0, 'max_score' => 49, 'letter_grade' => 'F', 'grade_point' => 0.0, 'remarks' => 'Failed'],
        ];

        foreach ($defaultGrades as $grade) {
            GradeScale::create(array_merge($grade, [
                'school_id' => $schoolId,
                'is_default' => true,
            ]));
        }

        return redirect()->route('examination-hub.grading-system.index')
            ->with('success', 'Default grading system initialized successfully');
    }
}
