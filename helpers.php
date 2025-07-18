<?php

use App\Http\Controllers\ExaminationController;
use App\Models\AcademicSubtopic;
use App\Models\Examination;
use App\Support\Examiner;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Output\RenderedContentInterface;

if (!function_exists('fisher_yates_shuffle')) {
    function fisher_yates_shuffle($array, $seed)
    {
        @mt_srand($seed);
        for ($i = count($array) - 1; $i > 0; --$i) {
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
    $minutes = (int)$minutes;
    if ($minutes < 0) {
        return "Invalid duration";
    }

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    $result = [];
    if ($hours > 0) {
        $result[] = $hours . ' Hour' . ($hours > 1 ? 's' : '');
    }
    if ($remainingMinutes > 0 || $hours === 0) {
        $result[] = $remainingMinutes . ' Minute' . ($remainingMinutes != 1 ? 's' : '');
    }

    return implode(' ', $result);
}

function getTopicQuestionCount($topicId)
{
    $subtopics = AcademicSubtopic::where('academic_topic_id', $topicId)
        ->withCount([
            'essayQuestions',
            'multipleChoiceQuestions',
            'trueOrFalseQuestions'
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
        'trueOrFalseQuestions'
    ])->find($subtopicId);

    if (!$subtopic) {
        return 0;
    }

    return $subtopic->essay_questions_count
        + $subtopic->multiple_choice_questions_count
        + $subtopic->true_or_false_questions_count;
}


function greetUser($name): string
{
    // Get the current hour (0-23)
    $hour = (int)date('H');


    if ($hour >= 5 && $hour < 12) {
        $greeting = "Good Morning";
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = "Good Afternoon";
    } elseif ($hour >= 17 && $hour < 21) {
        $greeting = "Good Evening";
    } else {
        $greeting = "Good Night";
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

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\Shared\Html;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @throws Throwable
 * @throws \PhpOffice\PhpWord\Exception\Exception
 */
function exportToWord(): BinaryFileResponse
{
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $examination = Examination::find(request()->examination_id);
    //$sections = Examiner::createSections($examination);

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

    return $pdf->download($examination->title . '.pdf');
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

if (!function_exists('school_setting')) {
    function school_setting($key, $default = null)
    {
        return SchoolSetting::get($key, $default);
    }
}

if (!function_exists('set_school_setting')) {
    function set_school_setting($key, $value)
    {
        return SchoolSetting::set($key, $value);
    }
}
