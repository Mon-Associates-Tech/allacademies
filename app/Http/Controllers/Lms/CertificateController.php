<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Lms\Course;
use App\Models\Lms\IssuedCertificate;
use App\Services\Lms\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(protected CertificateService $certificateService) {}

    /**
     * Show the certificate for a completed course.
     */
    public function showCourseCertificate(Course $course): View|RedirectResponse
    {
        $user = Auth::user();
        $enrollment = $course->getEnrollment($user);

        if (! $enrollment) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'You are not enrolled in this course.');
        }

        if (! $enrollment->isCompleted()) {
            return redirect()->route('lms.courses.learn', $course->slug)
                ->with('error', 'You must complete the course to receive a certificate.');
        }

        // Get or create certificate
        $certificate = $enrollment->certificate;

        if (! $certificate) {
            // Issue certificate if not already issued
            $certificate = $this->certificateService->issueCertificate($enrollment);
        }

        return view('lms.certificates.show', compact('certificate', 'course', 'enrollment'));
    }

    /**
     * Download the certificate PDF for a completed course.
     */
    public function downloadCourseCertificate(Course $course): Response|RedirectResponse
    {
        $user = Auth::user();
        $enrollment = $course->getEnrollment($user);

        if (! $enrollment) {
            return redirect()->route('lms.courses.show', $course->slug)
                ->with('error', 'You are not enrolled in this course.');
        }

        if (! $enrollment->isCompleted()) {
            return redirect()->route('lms.courses.learn', $course->slug)
                ->with('error', 'You must complete the course to download a certificate.');
        }

        $certificate = $enrollment->certificate;

        if (! $certificate) {
            // Issue certificate if not already issued
            $certificate = $this->certificateService->issueCertificate($enrollment);
        }

        // Generate PDF if not exists
        if (! $certificate->pdf_path || ! Storage::exists($certificate->pdf_path)) {
            $this->certificateService->generatePdf($certificate);
            $certificate->refresh();
        }

        $filename = "certificate-{$course->slug}-{$user->id}.pdf";

        return Storage::download($certificate->pdf_path, $filename);
    }

    /**
     * Display all certificates for the current user.
     */
    public function myCertificates(): View
    {
        $user = Auth::user();

        $certificates = IssuedCertificate::query()
            ->forUser($user)
            ->with(['course', 'template'])
            ->latest('issue_date')
            ->paginate(12);

        // Get stats
        $stats = [
            'total' => IssuedCertificate::forUser($user)->count(),
            'valid' => IssuedCertificate::forUser($user)->valid()->count(),
            'expired' => IssuedCertificate::forUser($user)->expired()->count(),
        ];

        return view('lms.my-learning.certificates', compact('certificates', 'stats'));
    }

    /**
     * Verify a certificate by its verification code.
     */
    public function verify(string $code): View
    {
        $certificate = IssuedCertificate::findByVerificationCode($code);

        if (! $certificate) {
            return view('lms.certificates.verify', [
                'certificate' => null,
                'isValid' => false,
                'message' => 'Certificate not found. Please check the verification code and try again.',
            ]);
        }

        $certificate->load(['user', 'course', 'template']);

        return view('lms.certificates.verify', [
            'certificate' => $certificate,
            'isValid' => $certificate->isValid(),
            'message' => $certificate->isValid()
                ? 'This certificate is valid and authentic.'
                : 'This certificate has expired.',
        ]);
    }
}
