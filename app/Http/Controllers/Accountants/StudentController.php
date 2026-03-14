<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayment;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = getSchoolId();

        $query = Student::query()
            ->where('school_id', $schoolId)
            ->with(['user', 'academicGroup', 'academicLevel']);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $students = $query->paginate(20);

        return view('accountant.students.index', compact('students'));
    }

    public function api(Request $request)
    {
        $schoolId = getSchoolId();

        $query = Student::query()
            ->where('school_id', $schoolId)
            ->with(['user'])
            ->limit(100);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            })->orWhere('student_id', 'like', '%'.$request->search.'%');
        }

        $students = $query->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->user?->name ?? 'N/A',
            ];
        });

        return response()->json($students);
    }

    public function show(Request $request, $studentId)
    {
        $schoolId = getSchoolId();
        
        // Find student with explicit school scoping to avoid 404 from global scope
        $student = Student::withoutSchoolScope()
            ->where('school_id', $schoolId)
            ->where('id', $studentId)
            ->with(['user', 'academicGroup', 'academicLevel'])
            ->firstOrFail();

        return view('accountant.students.show', compact('student'));
    }

    public function payments(Request $request, $studentId)
    {
        $schoolId = getSchoolId();
        
        // Find student with explicit school scoping to avoid 404 from global scope
        $student = Student::withoutSchoolScope()
            ->where('school_id', $schoolId)
            ->where('id', $studentId)
            ->with(['user', 'academicGroup', 'academicLevel'])
            ->firstOrFail();

        $payments = SchoolPayment::query()
            ->where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->with(['academicPeriod'])
            ->latest()
            ->paginate(20);

        return view('accountant.students.payments', compact('student', 'payments'));
    }
}
