<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WardController extends Controller
{
    public function select(Request $request, Student $student)
    {
        // Verify parent has access to this student
        $hasAccess = StudentParent::where('user_id', Auth::id())
            ->where('student_id', $student->id)
            ->exists();
            
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this student'
            ], 403);
        }
        
        // Store selected ward in session
        session(['selected_ward_id' => $student->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ward selected successfully',
            'student' => $student->load(['user', 'academicLevel.academicGroup'])
        ]);
    }
}
