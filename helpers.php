<?php

use App\Http\Controllers\ExaminationController;
use App\Models\AcademicSubtopic;
use App\Models\Examination;
use App\Models\Student;
use App\Models\User;
use App\Support\Examiner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

if (! function_exists('fisher_yates_shuffle')) {
    function fisher_yates_shuffle($array, $seed)
    {
        @mt_srand($seed);
        for ($i = count($array) - 1; $i > 0; $i--) {
            $j = @mt_rand(0, $i);
            $tmp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $tmp;
        }
        mt_srand();

        return $array;
    }
}

function convertMinutesToHoursMinutes($minutes): string
{
    // Ensure input is a non-negative integer
    $minutes = (int) $minutes;
    if ($minutes < 0) {
        return 'Invalid duration';
    }

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    $result = [];
    if ($hours > 0) {
        $result[] = $hours.' Hour'.($hours > 1 ? 's' : '');
    }
    if ($remainingMinutes > 0 || $hours === 0) {
        $result[] = $remainingMinutes.' Minute'.($remainingMinutes != 1 ? 's' : '');
    }

    return implode(' ', $result);
}

function getTopicQuestionCount($topicId)
{
    $subtopics = AcademicSubtopic::where('academic_topic_id', $topicId)
        ->withCount([
            'essayQuestions',
            'multipleChoiceQuestions',
            'trueOrFalseQuestions',
        ])
        ->get();

    return $subtopics->sum(function ($subtopic) {
        return $subtopic->essay_questions_count
            + $subtopic->multiple_choice_questions_count
            + $subtopic->true_or_false_questions_count;
    });
}

function getSubtopicQuestionCount($subtopicId)
{
    $subtopic = AcademicSubtopic::withCount([
        'essayQuestions',
        'multipleChoiceQuestions',
        'trueOrFalseQuestions',
    ])->find($subtopicId);

    if (! $subtopic) {
        return 0;
    }

    return $subtopic->essay_questions_count
        + $subtopic->multiple_choice_questions_count
        + $subtopic->true_or_false_questions_count;
}

function greetUser($name): string
{
    // Get the current hour (0-23)
    $hour = (int) date('H');

    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Good Afternoon';
    } elseif ($hour >= 17 && $hour < 21) {
        $greeting = 'Good Evening';
    } else {
        $greeting = 'Good Night';
    }

    return "$greeting, $name";
}

/**
 * @throws CommonMarkException
 */
function parsedMarkdown($markdown): string
{
    $config = [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
        'max_nesting_level' => 100,
        'renderer' => [
            'block_separator' => "\n",
            'inner_separator' => "\n",
            'soft_break' => "\n",
        ],
    ];

    $converter = new CommonMarkConverter($config);
    try {
        return $converter->convertToHtml($markdown);
    } catch (CommonMarkException $e) {
        return htmlspecialchars($markdown);
    }
}

/**
 * Strip markdown formatting and convert math notation for PDF rendering.
 * This removes markdown syntax and converts LaTeX math to readable text.
 */
if (! function_exists('stripMarkdownForPdf')) {
    function stripMarkdownForPdf(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Convert display math first: $$...$$ or \[...\]
        $text = preg_replace('/\\\\\[(.*?)\\\\\]/s', '$1', $text);
        $text = preg_replace('/\$\$(.+?)\$\$/s', '$1', $text);
        
        // Convert inline math: $...$ or \(...\)
        $text = preg_replace('/\\\\\((.*?)\\\\\)/s', '$1', $text);
        $text = preg_replace('/(?<!\$)\$(?!\$)(.+?)(?<!\$)\$(?!\$)/s', '$1', $text);
        
        // Remove markdown headers
        $text = preg_replace('/^#{1,6}\s+/m', '', $text);
        
        // Remove bold/italic markers
        $text = preg_replace('/\*{1,3}(.+?)\*{1,3}/', '$1', $text);
        $text = preg_replace('/_{1,3}(.+?)_{1,3}/', '$1', $text);
        
        // Remove code blocks and inline code
        $text = preg_replace('/`{3}[^`]*`{3}/s', '', $text);
        $text = preg_replace('/`([^`]+)`/', '$1', $text);
        
        // Remove image syntax but keep alt text
        $text = preg_replace('/!\[([^\]]*)\]\([^)]*\)/', '$1', $text);
        
        // Remove link syntax but keep link text
        $text = preg_replace('/\[([^\]]*)\]\([^)]*\)/', '$1', $text);
        
        // Remove blockquotes
        $text = preg_replace('/^>\s+/m', '', $text);
        
        // Remove horizontal rules
        $text = preg_replace('/^[-*_]{3,}$/m', '', $text);
        
        // Remove list markers
        $text = preg_replace('/^[\s-]*[-*+]\s+/m', '', $text);
        $text = preg_replace('/^\d+\.\s+/m', '', $text);
        
        // Clean up extra whitespace
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);
        
        // Escape HTML entities for safe rendering
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Render markdown with math for PDF using server-side processing.
 * Converts markdown to HTML and preserves math notation as plain text.
 * This is a PDF-safe alternative to the JavaScript-based markdown-with-math component.
 */
if (! function_exists('renderMarkdownForPdf')) {
    function renderMarkdownForPdf(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // First, protect math expressions by replacing them with placeholders
        $mathExpressions = [];
        $placeholderIndex = 0;
        
        // Protect display math: $$...$$
        $text = preg_replace_callback('/\$\$(.+?)\$\$/s', function($matches) use (&$mathExpressions, &$placeholderIndex) {
            $placeholder = "__MATH_DISPLAY_{$placeholderIndex}__";
            $mathExpressions[$placeholder] = '<span style="font-style: italic;">' . htmlspecialchars(trim($matches[1])) . '</span>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Protect display math: \[...\]
        $text = preg_replace_callback('/\\\\\[(.+?)\\\\\]/s', function($matches) use (&$mathExpressions, &$placeholderIndex) {
            $placeholder = "__MATH_DISPLAY_{$placeholderIndex}__";
            $mathExpressions[$placeholder] = '<span style="font-style: italic;">' . htmlspecialchars(trim($matches[1])) . '</span>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Protect inline math: $...$
        $text = preg_replace_callback('/(?<!\$)\$(?!\$)(.+?)(?<!\$)\$(?!\$)/s', function($matches) use (&$mathExpressions, &$placeholderIndex) {
            $placeholder = "__MATH_INLINE_{$placeholderIndex}__";
            $mathExpressions[$placeholder] = '<span style="font-style: italic;">' . htmlspecialchars(trim($matches[1])) . '</span>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Protect inline math: \(...\)
        $text = preg_replace_callback('/\\\\\((.+?)\\\\\)/s', function($matches) use (&$mathExpressions, &$placeholderIndex) {
            $placeholder = "__MATH_INLINE_{$placeholderIndex}__";
            $mathExpressions[$placeholder] = '<span style="font-style: italic;">' . htmlspecialchars(trim($matches[1])) . '</span>';
            $placeholderIndex++;
            return $placeholder;
        }, $text);
        
        // Now convert markdown to HTML using CommonMark
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
            'renderer' => [
                'block_separator' => "\n",
                'inner_separator' => "\n",
                'soft_break' => " ",  // Use space instead of newline for inline flow
            ],
        ];
        
        $converter = new \League\CommonMark\CommonMarkConverter($config);
        try {
            $html = $converter->convertToHtml($text);
            
            // Strip paragraph tags for inline content
            $html = preg_replace('/^<p>/', '', $html);
            $html = preg_replace('/<\/p>$/', '', $html);
            
            // Restore math expressions
            foreach ($mathExpressions as $placeholder => $replacement) {
                $html = str_replace($placeholder, $replacement, $html);
            }
            
            return $html;
        } catch (\Exception $e) {
            // Fallback to plain text with math
            return stripMarkdownForPdf($text);
        }
    }
}

use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @throws Throwable
 * @throws \PhpOffice\PhpWord\Exception\Exception
 */
function exportToWord(): BinaryFileResponse
{
    $phpWord = new PhpWord;
    $section = $phpWord->addSection();
    $examination = Examination::find(request()->examination_id);
    // $sections = Examiner::createSections($examination);

    $controller = app(ExaminationController::class);

    $view = $controller->show($examination);
    $html = $view->render();

    Html::addHtml($section, $html, false, false);

    $filePath = storage_path('app/public/exported.docx');
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend();
}

function exportToPdf(): \Illuminate\Http\Response
{
    $examination = Examination::find(request()->examination_id);
    $sections = Examiner::createSections($examination);

    $pdf = Pdf::loadView('exports.examination', ['examination' => $examination, 'sections' => $sections]);

    return $pdf->download($examination->title.'.pdf');
}

function getRouteParameter($name = 'id'): object|string|null
{
    return Route::getCurrentRoute()?->parameter($name);
}

function logInfo(string $message, array $context = []): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
    $context = array_merge([
        'class' => $trace['class'],
        'method' => $trace['function'],
    ], $context);

    Log::info($message, $context);
}

function logError(string $message, array $context = []): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
    $context = array_merge([
        'class' => $trace['class'],
        'method' => $trace['function'],
    ], $context);

    Log::error($message, $context);
}

use App\Models\SchoolSetting;

if (! function_exists('school_setting')) {
    function school_setting($key, $default = null)
    {
        return SchoolSetting::get($key, $default);
    }
}

if (! function_exists('set_school_setting')) {
    function set_school_setting($key, $value)
    {
        return SchoolSetting::set($key, $value);
    }
}

/**
 * Generate a route with academic hierarchy parameters
 *
 * @param  string  $routeName  The route name
 * @param  array  $parameters  Route parameters (e.g., ['essay_question' => $model])
 * @param  array  $overrides  Override specific academic parameters
 * @return string The generated route URL
 */
function academicRoute(string $routeName, array $parameters = [], array $overrides = []): string
{
    // Default academic parameters from current route
    $academicParams = [
        'academic_group' => getRouteParameter('academic_group'),
        'academic_level' => getRouteParameter('academic_level'),
        'academic_subject' => getRouteParameter('academic_subject'),
        'academic_topic' => getRouteParameter('academic_topic'),
    ];

    // Override with any provided values
    $academicParams = array_merge($academicParams, $overrides);

    // Filter out null values
    $academicParams = array_filter($academicParams);

    // Merge with provided parameters
    $allParams = array_merge($academicParams, $parameters);

    return route($routeName, $allParams);
}

if (! function_exists('getTimeRemaining')) {
    /**
     * Calculates the remaining days, hours, and minutes from now until a future timestamp.
     *
     * @param  string|Carbon|\DateTimeInterface  $futureTimestamp  The future date.
     */
    function getTimeRemaining($futureTimestamp): string
    {
        // Ensure we have a Carbon instance to work with
        $futureDate = Carbon::parse($futureTimestamp);
        $now = Carbon::now();

        // If the date is in the past, return an "expired" message
        if ($now->greaterThan($futureDate)) {
            return 'Expired';
        }

        // Get the difference between now and the future date
        $diff = $now->diff($futureDate);

        $parts = [];

        // Add days to the output string if there are any
        if ($diff->d > 0) {
            $parts[] = $diff->d.' '.Str::plural('day', $diff->d);
        }

        // Add hours if there are any
        if ($diff->h > 0) {
            $parts[] = $diff->h.' '.Str::plural('hour', $diff->h);
        }

        // Add minutes if there are any
        if ($diff->i > 0) {
            $parts[] = $diff->i.' '.Str::plural('minute', $diff->i);
        }

        // If the difference is less than a minute, provide a specific message
        if (empty($parts)) {
            return 'Less than a minute remaining';
        }

        return implode(', ', $parts).' remaining';
    }

    if (! function_exists('special_access_emails')) {
        function special_access_emails(): array
        {
            $emails = config('access.owner.special_access_emails', '');

            return array_map('trim', explode(',', $emails));
        }
    }

    if (! function_exists('impersonateUser')) {
        function impersonateUser($userId)
        {
            $user = User::findOrFail($userId);

            // Check if current user can impersonate
            if (! Auth::user()->canImpersonate()) {
                session()->flash('error', 'You do not have permission to impersonate users.');

                return;
            }

            // Check if target user can be impersonated
            if (! $user->canBeImpersonated()) {
                session()->flash('error', 'This user cannot be impersonated.');

                return;
            }

            // Store the current user ID and redirect URL before impersonation
            session()->put('impersonate_redirect_to', route('dashboard'));

            return redirect()->route('impersonate', $userId);
        }
    }

    function getSchoolId(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        // For owners/super admins, check session for selected school
        if ($user->canAccessCrossSchool()) {
            $sessionSchoolId = session('current_school_id');

            // If they've explicitly selected a school, use it
            if ($sessionSchoolId) {
                return $sessionSchoolId;
            }

            // Check app binding
            if (app()->bound('current_school_id')) {
                return app('current_school_id');
            }

            // Check if current_school is bound
            if (app()->bound('current_school')) {
                $school = app('current_school');

                return $school ? $school->id : null;
            }

            // Fallback to their assigned school if they have one
            if ($user->school_id) {
                return $user->school_id;
            }

            // No school selected - return null
            return null;
        }

        // For regular users, use their school_id
        return $user->school_id;
    }

    if (! function_exists('getCurrentSchoolContext')) {
        /**
         * Get the current school context for the authenticated user
         *
         * Handles school switching for owners/super admins
         * Returns the user's school for regular users
         */
        function getCurrentSchoolContext(): ?\App\Models\School
        {
            $user = Auth::user();

            if (! $user) {
                return null;
            }

            // For owners/super admins, check for switched school context
            if ($user->hasRole('owner') || $user->isSuperAdmin()) {
                // Check if a specific school is bound in the app container
                if (app()->bound('current_school')) {
                    try {
                        $school = app('current_school');
                        if ($school instanceof \App\Models\School) {
                            return $school;
                        }
                    } catch (\Exception $e) {
                        // Fall through to next check
                    }
                }

                // Check session for switched school ID
                $sessionSchoolId = session('current_school_id');
                if ($sessionSchoolId) {
                    try {
                        $school = \App\Models\School::find($sessionSchoolId);
                        if ($school) {
                            return $school;
                        }
                    } catch (\Exception $e) {
                        // Fall through to user's default school
                    }
                }

                // If viewing "all schools" or no context set, return null
                if (session()->has('current_school_id') && session('current_school_id') === null) {
                    return null;
                }

                // Default to user's own school if they have one
                return $user->school ?? null;
            }

            // For regular users, return their assigned school
            return $user->school ?? null;
        }
    }

    if (! function_exists('isViewingAllSchools')) {
        /**
         * Check if the user is viewing all schools context
         */
        function isViewingAllSchools(): bool
        {
            $user = Auth::user();

            if (! $user || (! $user->hasRole('owner') && ! $user->isSuperAdmin())) {
                return false;
            }

            // Check if current_school_id is explicitly set to null in session
            if (session()->has('current_school_id') && session('current_school_id') === null) {
                return true;
            }

            // Check if no school context is bound
            $currentSchool = getCurrentSchoolContext();

            return $currentSchool === null;
        }
    }

    /**
     * Get a student based on provided parameters
     *
     * @param  int|null  $user_id  The user ID to search by
     * @param  int|null  $student_id  The student's database ID to search by
     * @param  int|null  $school_id  The school ID to filter by
     * @param  bool  $withoutScopes  Whether to bypass global scopes
     */
    function getStudent($user_id = null, $student_id = null, $school_id = null, $withoutScopes = true): ?Student
    {
        // Start with the base query
        $query = $withoutScopes
            ? \App\Models\Student::withoutGlobalScopes()
            : \App\Models\Student::query();

        // If specific student_id provided, search by that first (highest priority)
        if ($student_id !== null) {
            $query->where('id', $student_id);

            // Optionally filter by school_id if provided
            if ($school_id !== null) {
                $query->where('school_id', $school_id);
            }

            return $query->first();
        }

        // If user_id provided, search by user_id
        if ($user_id !== null) {
            $query->where('user_id', $user_id);

            // Optionally filter by school_id if provided
            if ($school_id !== null) {
                $query->where('school_id', $school_id);
            }

            return $query->first();
        }

        // If school_id only provided, get first student from that school
        if ($school_id !== null) {
            return $query->where('school_id', $school_id)->first();
        }

        // Fallback: Get authenticated user's student
        if (Auth::check()) {
            $student = Auth::user()->student;

            // If student not found via relationship, try direct query
            if (! $student) {
                $student = \App\Models\Student::withoutGlobalScopes()
                    ->where('user_id', Auth::id())
                    ->first();
            }

            return $student;
        }

        // No parameters and no authenticated user
        return null;
    }

}

if (!function_exists('extractQuestionsFromDocument')) {
    /**
     * Extract or generate questions from document content
     * Intelligently detects if content contains pre-formatted questions or needs generation
     *
     * @param string $content The document content
     * @param string $questionType Type of questions (multiple_choice, true_false, essay, short_answer)
     * @param int $count Number of questions to extract/generate
     * @return array Array of structured questions
     */
    function extractQuestionsFromDocument(string $content, string $questionType = 'multiple_choice', int $count = 10): array
    {
        $service = app(\App\Services\DocumentQuestionExtractionService::class);
        return $service->extractFromText($content, $questionType, $count);
    }
}
