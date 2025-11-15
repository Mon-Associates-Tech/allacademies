<?php

namespace App\Templates;

use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\View;

class StudentDocumentTemplate
{
    /**
     * Render report card template
     */
    public static function renderReportCard(array $data): string
    {
        return View::make('students.report-card-pdf', [
            'reportCard' => $data['report_card'],
            'attendanceSummary' => $data['attendance'] ?? null,
        ])->render();
    }

    /**
     * Render ID card template
     */
    public static function renderIdCard(array $data): string
    {
        return View::make('students.id-card-pdf', [
            'student' => $data['student'],
            'idCard' => $data['id_card'],
        ])->render();
    }

    /**
     * Render library card template
     */
    public static function renderLibraryCard(array $data): string
    {
        return View::make('students.library-card', [
            'student' => $data['student'],
            'libraryCard' => $data['library_card'],
        ])->render();
    }

    /**
     * Render attendance report template
     */
    public static function renderAttendanceReport(array $data): string
    {
        return View::make('students.attendance-report', [
            'student' => $data['student'],
            'data' => $data,
            'academicYear' => $data['academic_year'] ?? null,
        ])->render();
    }
}
