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

        $query = Student::where('school_id', $schoolId)
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

    public function show(Student $student)
    {
        $student->load(['user', 'academicGroup', 'academicLevel']);

        return view('accountant.students.show', compact('student'));
    }

    public function payments(Student $student)
    {
        $payments = SchoolPayment::where('student_id', $student->id)
            ->with(['academicPeriod'])
            ->latest()
            ->paginate(20);

        return view('accountant.students.payments', compact('student', 'payments'));
    }
}
