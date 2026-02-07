<?php

namespace App\Services\Lms;

use App\Models\Lms\CertificateTemplate;
use App\Models\Lms\Course;
use App\Models\Lms\CourseEnrollment;
use App\Models\Lms\IssuedCertificate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function issueCertificate(CourseEnrollment $enrollment): IssuedCertificate
    {
        $course = $enrollment->course;
        $user = $enrollment->user;

        // Check if certificate already exists
        $existingCertificate = IssuedCertificate::where('enrollment_id', $enrollment->id)->first();
        if ($existingCertificate) {
            return $existingCertificate;
        }

        $template = $this->getTemplateForCourse($course);

        $certificate = IssuedCertificate::create([
            'template_id' => $template->id,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'recipient_name' => $user->name,
            'issue_date' => now(),
            'custom_data' => $this->buildCustomData($enrollment),
        ]);

        // Generate PDF
        $this->generatePdf($certificate);

        return $certificate;
    }

    public function issueManualCertificate(
        User $user,
        CertificateTemplate $template,
        array $customData = [],
        ?Course $course = null
    ): IssuedCertificate {
        $certificate = IssuedCertificate::create([
            'template_id' => $template->id,
            'user_id' => $user->id,
            'course_id' => $course?->id,
            'recipient_name' => $user->name,
            'issue_date' => now(),
            'custom_data' => $customData,
        ]);

        $this->generatePdf($certificate);

        return $certificate;
    }

    public function generatePdf(IssuedCertificate $certificate): string
    {
        $template = $certificate->template;

        $pdf = PDF::loadView(
            $template->getViewPath(),
            [
                'certificate' => $certificate,
                'template' => $template,
                'user' => $certificate->user,
                'course' => $certificate->course,
            ]
        );

        $pdf->setPaper($template->paper_size ?? 'a4', $template->orientation ?? 'landscape');

        $filename = "certificates/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());

        $certificate->update(['pdf_path' => $filename]);

        return $filename;
    }

    public function regeneratePdf(IssuedCertificate $certificate): string
    {
        // Delete old PDF if exists
        if ($certificate->pdf_path) {
            Storage::disk('public')->delete($certificate->pdf_path);
        }

        return $this->generatePdf($certificate);
    }

    public function verify(string $verificationCode): ?IssuedCertificate
    {
        return IssuedCertificate::where('verification_code', $verificationCode)
            ->with(['user', 'course', 'template'])
            ->first();
    }

    public function revokeCertificate(IssuedCertificate $certificate): bool
    {
        // Delete PDF
        if ($certificate->pdf_path) {
            Storage::disk('public')->delete($certificate->pdf_path);
        }

        return $certificate->delete();
    }

    public function getUserCertificates(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return IssuedCertificate::where('user_id', $user->id)
            ->with(['course', 'template'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    public function getCourseCertificates(Course $course): \Illuminate\Database\Eloquent\Collection
    {
        return IssuedCertificate::where('course_id', $course->id)
            ->with(['user', 'template'])
            ->orderBy('issue_date', 'desc')
            ->get();
    }

    protected function getTemplateForCourse(Course $course): CertificateTemplate
    {
        // First try to find a school-specific template
        if ($course->school_id) {
            $template = CertificateTemplate::where('school_id', $course->school_id)
                ->where('type', CertificateTemplate::TYPE_COURSE)
                ->where('is_active', true)
                ->first();

            if ($template) {
                return $template;
            }
        }

        // Fall back to global template
        $template = CertificateTemplate::whereNull('school_id')
            ->where('type', CertificateTemplate::TYPE_COURSE)
            ->where('is_active', true)
            ->first();

        if ($template) {
            return $template;
        }

        // Create a default template if none exists
        return $this->createDefaultTemplate();
    }

    protected function createDefaultTemplate(): CertificateTemplate
    {
        return CertificateTemplate::create([
            'name' => 'Default Course Certificate',
            'slug' => 'default-course-certificate',
            'type' => CertificateTemplate::TYPE_COURSE,
            'description' => 'Default certificate template for course completion',
            'template_file' => 'elegant',
            'orientation' => 'landscape',
            'paper_size' => 'a4',
            'is_active' => true,
            'default_fields' => [
                'title' => 'Certificate of Completion',
                'subtitle' => 'This is to certify that',
                'completion_text' => 'has successfully completed the course',
            ],
        ]);
    }

    protected function buildCustomData(CourseEnrollment $enrollment): array
    {
        $course = $enrollment->course;

        return [
            'course_title' => $course->title,
            'course_description' => $course->description,
            'completion_date' => $enrollment->completed_at?->format('F d, Y'),
            'final_grade' => $enrollment->final_grade,
            'duration_hours' => $course->estimated_duration_minutes ? round($course->estimated_duration_minutes / 60, 1) : null,
            'difficulty_level' => ucfirst($course->difficulty_level),
            'instructor_name' => $course->creator?->name,
            'school_name' => $course->school?->name,
        ];
    }
}
