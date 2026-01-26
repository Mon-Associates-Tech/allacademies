<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\SchoolPaymentStructure;
use Illuminate\Http\Request;

class SchoolPaymentStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $schoolId = getSchoolId();

        $query = SchoolPaymentStructure::with([
            'academicYear',
            'academicPeriod',
            'academicGroup',
            'academicLevel',
        ])->where('school_id', $schoolId);

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $feeStructures = $query->latest()->paginate(20)->withQueryString();

        // Get filter options
        $filterOptions = [
            'academic_years' => AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_periods' => AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'payment_types' => SchoolPaymentStructure::paymentTypes(),
        ];

        return view('admin.school-payment-structures.index', compact('feeStructures', 'filterOptions'));
    }

    public function create()
    {
        $schoolId = getSchoolId();

        $data = [
            'academic_years' => AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_periods' => AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_groups' => AcademicGroup::forSchool($schoolId)->get(),
            'academic_levels' => AcademicLevel::forSchool($schoolId)->get(),
            'payment_types' => SchoolPaymentStructure::paymentTypes(),
            'payment_periods' => SchoolPaymentStructure::paymentPeriods(),
        ];

        return view('admin.school-payment-structures.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
            'academic_level_id' => 'nullable|exists:academic_levels,id',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'payment_period' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'allow_partial_payment' => 'boolean',
            'minimum_partial_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ];

        // Add validation for new entities if provided
        if ($request->has('new_year_start_date')) {
            $rules['new_year_start_date'] = 'required|date';
            $rules['new_year_end_date'] = 'required|date|after:new_year_start_date';
        }

        if ($request->has('new_period_start_date')) {
            $rules['new_period_name'] = 'required|string|max:255';
            $rules['new_period_type'] = 'required|string';
            $rules['new_period_sequence'] = 'required|integer|min:1';
            $rules['new_period_start_date'] = 'required|date';
            $rules['new_period_end_date'] = 'required|date|after:new_period_start_date';
        }

        $validated = $request->validate($rules);

        $validated['school_id'] = getSchoolId();
        $validated['created_by'] = auth()->id();
        $validated['currency'] = 'GHS';

        // Create new Academic Year if needed
        if (! $request->filled('academic_year_id') && $request->has('new_year_start_date')) {
            $year = new AcademicYear([
                'school_id' => $validated['school_id'],
                'start_date' => $request->new_year_start_date,
                'end_date' => $request->new_year_end_date,
                'status' => 'active',
                'is_current' => true,
            ]);
            $year->name = $year->generateName();
            $year->save();

            $validated['academic_year_id'] = $year->id;
        }

        // Create new Academic Period if needed
        if (! $request->filled('academic_period_id') && $request->has('new_period_start_date')) {
            $period = new AcademicPeriod([
                'school_id' => $validated['school_id'],
                'academic_year_id' => $validated['academic_year_id'] ?? null, // Link to the year we just created or selected
                'name' => $request->new_period_name,
                'type' => $request->new_period_type,
                'sequence' => $request->new_period_sequence,
                'start_date' => $request->new_period_start_date,
                'end_date' => $request->new_period_end_date,
                'status' => 'active',
                'is_current' => true,
            ]);

            // Generate academic year string if linked to a year
            if ($period->academic_year_id) {
                $year = AcademicYear::find($period->academic_year_id);
                if ($year) {
                    $period->academic_year = $year->getDisplayName();
                }
            }

            $period->save();
            $validated['academic_period_id'] = $period->id;
        }

        // Clean up temporary fields from validated data before creating payment structure
        $fieldsToRemove = [
            'new_year_start_date', 'new_year_end_date',
            'new_period_name', 'new_period_type', 'new_period_sequence',
            'new_period_start_date', 'new_period_end_date',
        ];

        foreach ($fieldsToRemove as $field) {
            unset($validated[$field]);
        }

        SchoolPaymentStructure::create($validated);

        return redirect()
            ->route('admin.school-payment-structures.index')
            ->with('success', 'Fee structure created successfully!');
    }

    public function show(SchoolPaymentStructure $school_payment_structure)
    {
        $school_payment_structure->load([
            'academicYear',
            'academicPeriod',
            'academicGroup',
            'academicLevel',
            'creator',
        ]);

        $stats = [
            'applicable_students' => $school_payment_structure->getApplicableStudents()->count(),
            'total_collected' => $school_payment_structure->getTotalCollected(),
            'total_pending' => $school_payment_structure->getTotalPending(),
            'collection_rate' => $school_payment_structure->getCollectionRate(),
        ];

        return view('admin.school-payment-structures.show', compact('school_payment_structure', 'stats'));
    }

    public function edit(SchoolPaymentStructure $school_payment_structure)
    {
        $schoolId = getSchoolId();

        $data = [
            'feeStructure' => $school_payment_structure,
            'academic_years' => AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_periods' => AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_groups' => AcademicGroup::forSchool($schoolId)->get(),
            'academic_levels' => AcademicLevel::forSchool($schoolId)->get(),
            'payment_types' => SchoolPaymentStructure::paymentTypes(),
            'payment_periods' => SchoolPaymentStructure::paymentPeriods(),
        ];

        return view('admin.school-payment-structures.edit', $data);
    }

    public function update(Request $request, SchoolPaymentStructure $school_payment_structure)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_period_id' => 'nullable|exists:academic_periods,id',
            'academic_group_id' => 'nullable|exists:academic_groups,id',
            'academic_level_id' => 'nullable|exists:academic_levels,id',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'payment_period' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'allow_partial_payment' => 'boolean',
            'minimum_partial_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $school_payment_structure->update($validated);

        return redirect()
            ->route('admin.school-payment-structures.show', $school_payment_structure)
            ->with('success', 'Fee structure updated successfully!');
    }

    public function destroy(SchoolPaymentStructure $school_payment_structure)
    {
        $school_payment_structure->delete();

        return redirect()
            ->route('admin.school-payment-structures.index')
            ->with('success', 'Fee structure deleted successfully!');
    }
}
