<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImporter;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\StudentAcademicProgression;
use App\Models\StudentIdCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'academicLevel', 'studentGroup'])->paginate(20);

        return view('students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'academicLevel',
            'academicGroup',
            'studentGroup',
            'academicProgression.academicLevel',
            //            'reportCards.grades.subject',
            'reportCards',
            'idCards',
        ]);

        return view('students.show', compact('student'));
    }

    public function promote(Student $student, Request $request)
    {
        $request->validate([
            'new_academic_level_id' => 'required|exists:academic_levels,id',
            'promotion_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        // End current academic level
        $currentProgression = $student->academicProgression()
            ->where('status', 'current')
            ->first();

        if ($currentProgression) {
            $currentProgression->update([
                'end_date' => $request->promotion_date,
                'status' => 'completed',
            ]);
        }

        // Create new academic level progression
        StudentAcademicProgression::create([
            'student_id' => $student->id,
            'academic_level_id' => $request->new_academic_level_id,
            'start_date' => $request->promotion_date,
            'status' => 'current',
            'notes' => $request->remarks,
        ]);

        // Update student's current academic level
        $student->update([
            'academic_level_id' => $request->new_academic_level_id,
        ]);

        return redirect()->back()->with('success', 'Student promoted successfully');
    }

    public function generateReportCard(Student $student, Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term' => 'required|string',
        ]);

        // Check if report card already exists
        $reportCard = ReportCard::firstOrCreate([
            'student_id' => $student->id,
            'academic_year_id' => $request->academic_year_id,
            'term' => $request->term,
            'school_id' => $student->school_id,
        ], [
            'generated_at' => now(),
        ]);

        // Here you would add logic to calculate grades and populate report card
        // This is a simplified example

        return redirect()->back()->with('success', 'Report card generated successfully');
    }

    public function generateIdCard(Student $student)
    {
        // Check if ID card already exists and is still valid
        $existingIdCard = $student->idCards()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->first();

        if ($existingIdCard) {
            return redirect()->back()->with('info', 'Student already has a valid ID card');
        }

        // Expire any existing cards
        $student->idCards()->update(['status' => 'expired']);

        // Generate new ID card
        $idCard = StudentIdCard::create([
            'student_id' => $student->id,
            'card_number' => 'ID'.time().rand(1000, 9999),
            'issue_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'ID card generated successfully');
    }

    public function printIdCard(Student $student)
    {
        $idCard = $student->idCards()
            ->where('status', 'active')
            ->firstOrFail();

        // Generate PDF for ID card
        $pdf = PDF::loadView('students.id-card-pdf', compact('student', 'idCard'));

        return $pdf->download("id-card-{$student->user->name}.pdf");
    }

    public function printReportCard(ReportCard $reportCard)
    {
        $reportCard->load([
            'student.user',
            'student.academicLevel',
            'academicYear',
            'grades.subject',
            'grades.teacher.user',
        ]);

        // Generate PDF for report card
        $pdf = PDF::loadView('students.report-card-pdf', compact('reportCard'));

        return $pdf->download("report-card-{$reportCard->student->user->name}.pdf");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        try {
            $file = $request->file('file');

            $importer = new StudentsImporter(
                $request->school_id,
                'password123' // default password
            );

            Excel::import($importer, $file);

            $stats = $importer->getImportStats();

            return redirect()->back()->with('success', "Import completed successfully! Imported: {$stats['imported']}, Skipped: {$stats['skipped']}, Errors: {$stats['errors']}");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }
}
