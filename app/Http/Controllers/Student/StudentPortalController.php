<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;

class StudentPortalController extends Controller
{
    public function teachers()
    {
        abort_unless(auth()->user()?->hasRole('student'), 403);

        $student = Student::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $teachers = $student->teachers()
            ->where('teachers.school_id', $student->school_id)
            ->with(['user', 'subjects'])
            ->orderBy('teachers.id')
            ->get();

        return view('students.teachers', compact('teachers'));
    }
}
