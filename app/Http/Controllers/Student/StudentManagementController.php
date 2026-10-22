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
use Illuminate\Support\Facades\Request as RequestFacade;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;

class StudentManagementController extends Controller
{
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

    public function previewReportCardAsStudent(ReportCard $reportCard)
    {
        $reportCard->load([
            'student.user',
            'student.academicLevel',
            'student.school',
            'grades.subject',
            'configuration.academicPeriod',
        ]);

        $school = $reportCard->student->school;

        // Inline the built Tailwind CSS so Puppeteer doesn't need to fetch external assets
        $tailwindCss = '';
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            if ($cssFile) {
                $cssPath = public_path('build/' . $cssFile);
                if (file_exists($cssPath)) {
                    $tailwindCss = file_get_contents($cssPath);
                }
            }
        }

        // Pre-compute inline image data URIs for Puppeteer
        $inlineImage = function ($relativePath) {
            if (empty($relativePath)) return '';
            $fullPath = public_path($relativePath);
            if (!file_exists($fullPath)) return '';
            $mime = mime_content_type($fullPath);
            $content = file_get_contents($fullPath);
            return 'data:' . $mime . ';base64,' . base64_encode($content);
        };

        $logoPath = !empty($school?->logo) ? 'storage/' . $school->logo : 'img/logo.png';
        $logoUrl = $inlineImage($logoPath);

        // Pre-compute signature data URIs
        $configuration = $reportCard->configuration;
        $classTeacherSigUri = $configuration?->class_teacher_signature_path
            ? $inlineImage('storage/' . $configuration->class_teacher_signature_path)
            : '';
        $principalSigUri = $configuration?->principal_signature_path
            ? $inlineImage('storage/' . $configuration->principal_signature_path)
            : '';

        $html = view('reports.report-card-pdf', [
            'reportCard' => $reportCard,
            'school' => $school,
            'tailwindCss' => $tailwindCss,
            'logoUrl' => $logoUrl,
            'classTeacherSigUri' => $classTeacherSigUri,
            'principalSigUri' => $principalSigUri,
        ])->render();

        try {
            $pdf = Browsershot::html($html)
                ->setIncludePath('$PATH:/usr/local/bin')
                ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
                ->showBackground()
                ->noSandbox()
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="report-card-'.$reportCard->student->user->name.'-'.$reportCard->term.'.pdf"',
            ]);
        } catch (\Throwable $e) {
            // Fallback to DomPDF if BrowserShot fails
            $pdf = Pdf::loadView('reports.report-card-pdf', [
                'reportCard' => $reportCard,
                'school' => $school,
            ]);

            return $pdf->stream("report-card-{$reportCard->student->user->name}-{$reportCard->term}.pdf");
        }
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
