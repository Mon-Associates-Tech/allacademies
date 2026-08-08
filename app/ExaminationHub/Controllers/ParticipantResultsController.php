<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Http\Controllers\Controller;
use App\Notifications\ResultAccessNotification;
use App\Services\Lms\CertificateTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;

class ParticipantResultsController extends Controller
{
    private const RESULT_ACCESS_SESSION_KEY = 'exam_results_access';

    public function index(Request $request): View
    {
        $email = $request->query('email');

        if (! $email) {
            return view('examination-hub.results.index', [
                'needsEmail' => true,
                'submissions' => collect(),
                'summary' => [],
            ]);
        }

        $emailCheck = $this->verifyEmail($request, $email);
        if ($emailCheck instanceof RedirectResponse) {
            return view('examination-hub.results.index', [
                'needsEmail' => true,
                'submissions' => collect(),
                'summary' => [],
            ]);
        }

        $submissions = GeneralExamSubmission::query()
            ->where('participant_email', strtolower($email))
            ->whereNotNull('submitted_at')
            ->with(['assignment.user'])
            ->orderByDesc('submitted_at')
            ->get();

        $summary = [
            'total_submissions' => $submissions->count(),
            'results_released' => $submissions->filter(fn ($s) => $s->canViewResults())->count(),
            'average_percentage' => round((float) $submissions->avg('percentage'), 2),
            'best_percentage' => round((float) $submissions->max('percentage'), 2),
        ];

        $performanceTrend = $submissions
            ->sortBy('submitted_at')
            ->take(10)
            ->mapWithKeys(function ($submission) {
                $date = $submission->submitted_at?->format('M d') ?? 'N/A';

                return [$date => round((float) ($submission->percentage ?? 0), 1)];
            });

        $gradeDistribution = $submissions->groupBy(function ($submission) {
            $percentage = $submission->percentage ?? 0;

            return match (true) {
                $percentage >= 90 => 'A+',
                $percentage >= 80 => 'A',
                $percentage >= 70 => 'B',
                $percentage >= 60 => 'C',
                $percentage >= 50 => 'D',
                default => 'F',
            };
        })->map->count();

        return view('examination-hub.results.index', [
            'needsEmail' => false,
            'email' => $email,
            'submissions' => $submissions,
            'summary' => $summary,
            'performanceTrend' => $performanceTrend,
            'gradeDistribution' => $gradeDistribution,
        ]);
    }

    public function show(Request $request, GeneralExamSubmission $submission): View|RedirectResponse
    {
        // Try token-based access first (secure method)
        $token = $request->query('token');
        
        if ($token) {
            return $this->validateTokenAndShow($request, $submission, $token);
        }
        
        // Fallback to email-based access (legacy method - will be deprecated)
        $email = $request->query('email') ?? session(self::RESULT_ACCESS_SESSION_KEY);

        if (! $email || strtolower($submission->participant_email) !== strtolower($email)) {
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'Unauthorized access.']);
        }

        // Log access for audit trail
        $this->logResultAccess($submission, $request);

        return view('examination-hub.results.show', [
            'submission' => $submission->load('assignment'),
            'email' => $email,
        ]);
    }
    
    /**
     * Validate secure token and show results
     */
    private function validateTokenAndShow(Request $request, GeneralExamSubmission $submission, string $token): View|RedirectResponse
    {
        $cacheKey = "result_access:{$token}";
        $cacheData = Cache::get($cacheKey);
        
        // Validate token exists and matches submission
        if (!$cacheData || $cacheData['submission_id'] !== $submission->id) {
            Log::warning('Invalid result access token attempt', [
                'token' => substr($token, 0, 8),
                'submission_id' => $submission->id,
                'ip' => $request->ip(),
            ]);
            
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'Invalid or expired access link. Please request a new one.']);
        }
        
        // Check expiration
        if (isset($cacheData['expires_at']) && now()->gt($cacheData['expires_at'])) {
            Cache::forget($cacheKey);
            
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'This access link has expired. Please request a new one.']);
        }
        
        // Log access for audit trail
        $this->logResultAccess($submission, $request, $token);
        
        return view('examination-hub.results.show', [
            'submission' => $submission->load('assignment'),
            'email' => $cacheData['email'],
            'access_token' => $token,
        ]);
    }
    
    /**
     * Log result access for audit purposes
     */
    private function logResultAccess(GeneralExamSubmission $submission, Request $request, ?string $token = null): void
    {
        Log::info('Exam result accessed', [
            'submission_id' => $submission->id,
            'participant_email' => $submission->participant_email,
            'exam_title' => $submission->assignment->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'access_method' => $token ? 'token' : 'email',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function certificate(Request $request, GeneralExamSubmission $submission): View|RedirectResponse
    {
        // Try token-based access first
        $token = $request->query('token');
        
        if ($token) {
            $cacheKey = "result_access:{$token}";
            $cacheData = Cache::get($cacheKey);
            
            if (!$cacheData || $cacheData['submission_id'] !== $submission->id) {
                return redirect()->route('examination-hub.results.show', ['submission' => $submission])
                    ->withErrors(['error' => 'Invalid or expired access link.']);
            }
            
            $email = $cacheData['email'];
        } else {
            // Fallback to email/session
            $email = $request->query('email') ?? session(self::RESULT_ACCESS_SESSION_KEY);
        }

        if (! $email || strtolower($submission->participant_email) !== strtolower($email)) {
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'Unauthorized access.']);
        }

        if (! $submission->isSubmitted() || ! $submission->assignment->canShowResults()) {
            return redirect()->route('examination-hub.results.show', ['submission' => $submission, 'email' => $email])
                ->withErrors(['error' => 'Results not available for certificate.']);
        }

        return view('examination-hub.results.certificate', [
            'submission' => $submission->load('assignment'),
            'email' => $email,
        ]);
    }

    public function certificatePdf(Request $request, GeneralExamSubmission $submission)
    {
        // Try token-based access first
        $token = $request->query('token');
        
        if ($token) {
            $cacheKey = "result_access:{$token}";
            $cacheData = Cache::get($cacheKey);
            
            if (!$cacheData || $cacheData['submission_id'] !== $submission->id) {
                return redirect()->route('examination-hub.results.show', ['submission' => $submission])
                    ->withErrors(['error' => 'Invalid or expired access link.']);
            }
            
            $email = $cacheData['email'];
        } else {
            // Fallback to email/session
            $email = $request->query('email') ?? session(self::RESULT_ACCESS_SESSION_KEY);
        }

        if (! $email || strtolower($submission->participant_email) !== strtolower($email)) {
            return redirect()->route('examination-hub.results.index')
                ->withErrors(['error' => 'Unauthorized access.']);
        }

        if (! $submission->isSubmitted() || ! $submission->assignment->canShowResults()) {
            return redirect()->route('examination-hub.results.show', ['submission' => $submission, 'email' => $email])
                ->withErrors(['error' => 'Results not available for certificate.']);
        }

        $data = [
            'submission' => $submission->load('assignment'),
            'email' => $email,
        ];

        $html = view('examination-hub.results.certificate', $data)->render();
        $filename = 'certificate-'.$submission->id.'.pdf';

        // If a PDF template exists in resources/pdf, use FPDI overlay generator for precise certificates
        $templatePath = resource_path('pdf/certificate-template.pdf');
        if (file_exists($templatePath)) {
            $service = new CertificateTemplateService;
            try {
                $binary = $service->generateFromTemplate($submission, $templatePath);

                return response($binary, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                ]);
            } catch (\Throwable $e) {
                // fallback to other methods below
            }
        }

        try {
            $pdf = Browsershot::html($html)
                ->setContentUrl($request->root())
                ->showBackground()
                ->landscape()
                ->noSandbox()
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $exception) {
            $pdf = Pdf::loadView('examination-hub.results.certificate', $data)
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }
    }

    private function verifyEmail(Request $request, string $email): ?RedirectResponse
    {
        if ($this->hasResultAccessSession($request, $email)) {
            return null;
        }

        $exists = GeneralExamSubmission::where('participant_email', strtolower($email))
            ->whereNotNull('submitted_at')
            ->exists();

        if (! $exists) {
            return redirect()->back()->with('error', 'No results found for this email.');
        }

        $request->session()->put(self::RESULT_ACCESS_SESSION_KEY, strtolower($email));

        return null;
    }

    private function hasResultAccessSession(Request $request, string $email): bool
    {
        $sessionEmail = $request->session()->get(self::RESULT_ACCESS_SESSION_KEY);

        return $sessionEmail && strtolower($sessionEmail) === strtolower($email);
    }
}
