<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\FinancialAid;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use Illuminate\Http\Request;

class FinancialAidController extends Controller
{
    public function index()
    {
        $schoolId = getSchoolId();
        $financialAids = FinancialAid::where('school_id', $schoolId)
            ->withCount('beneficiaries')
            ->with('schoolPaymentStructure')
            ->latest()
            ->paginate(20);

        return view('accountant.financial-aid.index', compact('financialAids'));
    }

    public function show(FinancialAid $financialAid)
    {
        $financialAid->load(['beneficiaries.user', 'beneficiaries.academicGroup', 'beneficiaries.academicLevel', 'schoolPaymentStructure']);

        return view('accountant.financial-aid.show', compact('financialAid'));
    }

    public function create()
    {
        $schoolId = getSchoolId();
        $paymentStructures = SchoolPaymentStructure::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        return view('accountant.financial-aid.create', compact('paymentStructures'));
    }

    public function store(Request $request)
    {
        $schoolId = getSchoolId();

        if (! $schoolId) {
            return back()->withErrors(['error' => 'School context not found']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'school_payment_structure_id' => 'nullable|exists:school_payment_structures,id',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['amount_raised'] = 0;

        FinancialAid::create($validated);

        return redirect()->route('accountant.financial-aid.index')
            ->with('success', 'Financial aid program created successfully');
    }

    public function edit(FinancialAid $financialAid)
    {
        $schoolId = getSchoolId();
        $paymentStructures = SchoolPaymentStructure::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        return view('accountant.financial-aid.edit', compact('financialAid', 'paymentStructures'));
    }

    public function update(Request $request, FinancialAid $financialAid)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'school_payment_structure_id' => 'nullable|exists:school_payment_structures,id',
        ]);

        $financialAid->update($validated);

        return redirect()->route('accountant.financial-aid.index')
            ->with('success', 'Financial aid program updated successfully');
    }

    public function manageBeneficiaries(FinancialAid $financialAid)
    {
        $financialAid->load(['beneficiaries.user', 'beneficiaries.academicGroup', 'beneficiaries.academicLevel']);

        return view('accountant.financial-aid.manage-beneficiaries', compact('financialAid'));
    }

    public function addBeneficiary(Request $request, FinancialAid $financialAid)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        if (! $financialAid->beneficiaries()->where('financial_aid_student.student_id', $request->student_id)->exists()) {
            $financialAid->beneficiaries()->attach($request->student_id);
        }

        return back()->with('success', 'Beneficiary added successfully');
    }

    public function removeBeneficiary(FinancialAid $financialAid, Student $student)
    {
        $financialAid->beneficiaries()->detach($student->id);

        return back()->with('success', 'Beneficiary removed successfully');
    }
}
