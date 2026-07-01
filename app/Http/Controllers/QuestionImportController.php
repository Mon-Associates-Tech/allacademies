<?php

namespace App\Http\Controllers;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\AcademicSubject;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use App\Models\QuestionImportBatch;
use App\Jobs\ProcessDocumentQuestionImportJob;
use App\Services\QuestionImport\DocumentAiQuestionImportService;
use App\Services\QuestionImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class QuestionImportController extends Controller
{
    /** File extensions handled by the AI document pipeline (DocumentAiQuestionImportService). */
    private const AI_DOCUMENT_EXTENSIONS = ['docx', 'doc', 'pdf'];

    /** File extensions handled by the legacy Excel pipeline (QuestionImportService). */
    private const EXCEL_EXTENSIONS = ['xlsx', 'xls'];

    public function __construct(
        private QuestionImportService $importService,
        private DocumentAiQuestionImportService $aiImportService,
    ) {}

    /**
     * Show the import form for questions at topic level
     */
    public function showImportForm(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        AcademicTopic $academic_topic,
        ?AcademicSubtopic $academicSubtopic = null
    ): View {
        return view('questions.import', [
            'academicTopic' => $academic_topic,
            'academicSubtopic' => $academicSubtopic,
            'academicSubject' => $academic_subject,
        ]);
    }

    /**
     * Show the import form for questions at subject level
     */
    public function showSubjectImportForm(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject
    ): View {
        return view('questions.import', [
            'academicTopic' => null,
            'academicSubtopic' => null,
            'academicSubject' => $academic_subject,
        ]);
    }

    /**
     * Preview the questions from the uploaded file before saving (at topic level)
     */
    public function previewQuestions(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        AcademicTopic $academic_topic,
        ?AcademicSubtopic $academicSubtopic = null
    ) {
        return $this->performPreview($request, $academic_group, $academic_level, $academic_subject, $academic_topic, $academicSubtopic);
    }

    /**
     * Preview the questions from the uploaded file before saving (at subject level)
     */
    public function previewSubjectQuestions(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject
    ) {
        return $this->performPreview($request, $academic_group, $academic_level, $academic_subject, null, null);
    }

    /**
     * Common method to perform preview at either topic or subject level.
     * Dispatches to the AI document pipeline (docx/doc/pdf) or the legacy
     * Excel pipeline (xlsx/xls) based on the uploaded file's extension.
     */
    private function performPreview(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        ?AcademicTopic $academic_topic = null,
        ?AcademicSubtopic $academicSubtopic = null
    ) {
        if (! $request->hasFile('questions_file')) {
            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'Please select a file to preview questions.');
        }

        $validator = Validator::make($request->all(), [
            'questions_file' => 'required|file|mimes:xlsx,xls,docx,doc,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('questions_file');
        $extension = strtolower($file->getClientOriginalExtension());

        // The AI document pipeline extracts questions from free text and has no
        // per-row topic id (unlike the Excel template's academic_topic_id column),
        // so it only makes sense as a topic-level import.
        if (in_array($extension, self::AI_DOCUMENT_EXTENSIONS, true) && ! $academic_topic) {
            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'Word/PDF document import requires a specific topic. Please import from within a topic, or use the Excel template for subject-level bulk import.')
                ->withInput();
        }

        // AI document imports (docx/doc/pdf) run through the AI service, which can
        // take well over a minute and previously caused 504s when run inline on
        // the request. Dispatch a queued job and let the user watch progress on a
        // polling status page instead.
        if (in_array($extension, self::AI_DOCUMENT_EXTENSIONS, true)) {
            try {
                $filePath = $file->store('temp/question-imports', 'local');

                $batch = QuestionImportBatch::create([
                    'user_id' => Auth::id(),
                    'driver' => 'ai_document',
                    'status' => QuestionImportBatch::STATUS_PENDING,
                    'file_path' => $filePath,
                    'original_filename' => $file->getClientOriginalName(),
                    'academic_subject_id' => $academic_subject->id,
                    'academic_topic_id' => $academic_topic->id,
                    'academic_subtopic_id' => $academicSubtopic?->id,
                ]);

                ProcessDocumentQuestionImportJob::dispatch($batch->id);

                return redirect()->route('questions.import.status', ['batch' => $batch]);
            } catch (\Exception $e) {
                \Log::error('Failed to queue question import', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName(),
                ]);

                return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                    ->with('import_error', 'Could not start the import: '.$e->getMessage())
                    ->withInput();
            }
        }

        // Excel imports are fast and stay synchronous.
        try {
            $previewResult = $this->importService->previewQuestions(
                $file,
                $academic_subject,
                $academic_topic,
                $academicSubtopic,
                Auth::id()
            );
            $driver = 'excel';
            $displayData = $previewResult;

            // Store the file path in session for later use during import
            $filePath = $file->store('temp/question-imports', 'local');
            session(['question_import_driver' => $driver]);
            session(['question_import_file_path' => $filePath]);
            session(['question_import_file_original_name' => $file->getClientOriginalName()]);
            session(['question_import_academic_subject_id' => $academic_subject->id]);
            session(['question_import_academic_topic_id' => $academic_topic?->id]);
            session(['question_import_academic_subtopic_id' => $academicSubtopic?->id]);
            session(['question_import_preview_results' => $previewResult]);
            session(['question_import_processing' => false]);

            return view('questions.preview', [
                'academicTopic' => $academic_topic,
                'academicSubtopic' => $academicSubtopic,
                'academicSubject' => $academic_subject,
                'academic_subject' => $academic_subject,
                'academic_group' => $academic_group,
                'academic_level' => $academic_level,
                'previewData' => $displayData,
                'importDriver' => $driver,
                'file' => $file,
            ]);
        } catch (\Exception $e) {
            \Log::error('Question preview failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'subject_id' => $academic_subject->id,
                'topic_id' => $academic_topic?->id,
                'subtopic_id' => $academicSubtopic?->id,
            ]);

            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'An error occurred while previewing questions: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Import the questions after preview (at topic level)
     */
    public function importQuestions(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        AcademicTopic $academic_topic,
        ?AcademicSubtopic $academicSubtopic = null
    ): RedirectResponse {
        return $this->performImport($request, $academic_group, $academic_level, $academic_subject, $academic_topic, $academicSubtopic);
    }

    /**
     * Import the questions after preview (at subject level)
     */
    public function importSubjectQuestions(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject
    ): RedirectResponse {
        return $this->performImport($request, $academic_group, $academic_level, $academic_subject, null, null);
    }

    /**
     * Common method to perform import at either topic or subject level (Excel only).
     * AI document imports are handled asynchronously via confirmImport().
     */
    private function performImport(
        Request $request,
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        ?AcademicTopic $academic_topic = null,
        ?AcademicSubtopic $academicSubtopic = null
    ): RedirectResponse {
        if (session('question_import_processing', false)) {
            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'Import is already in progress. Please wait or refresh the page.');
        }

        session(['question_import_processing' => true]);

        $driver = session('question_import_driver');
        $previewResults = session('question_import_preview_results');
        $filePath = session('question_import_file_path');
        $storedSubjectId = session('question_import_academic_subject_id');

        if (! $previewResults || $driver !== 'excel' || $storedSubjectId != $academic_subject->id) {
            session()->forget('question_import_processing');

            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'Import session expired. Please restart the import process.');
        }

        try {
            $result = $this->importService->importFromPreviewData(
                $previewResults,
                $academic_subject,
                $academic_topic,
                $academicSubtopic,
                Auth::id()
            );

            $stats = $this->importService->getImportStatistics();
            $totalImported = array_sum($stats);
            $importErrors = $result['errors'] ?? [];

            if ($filePath && file_exists(storage_path('app/'.$filePath))) {
                unlink(storage_path('app/'.$filePath));
            }

            session()->forget([
                'question_import_driver',
                'question_import_file_path',
                'question_import_file_original_name',
                'question_import_academic_subject_id',
                'question_import_academic_topic_id',
                'question_import_academic_subtopic_id',
                'question_import_preview_results',
                'question_import_processing',
            ]);

            $message = "Successfully imported {$totalImported} questions.";

            [$route, $route_params] = $this->topicOrSubjectRoute(
                $academic_group, $academic_level, $academic_subject, $academic_topic
            );

            if (! empty($importErrors)) {
                $message .= ' '.count($importErrors).' issue(s) were flagged — please review.';

                return redirect()->route($route, $route_params)
                    ->with('warning', $message)
                    ->with('import_errors', $importErrors);
            }

            return redirect()->route($route, $route_params)->with('success', $message);

        } catch (\Exception $e) {
            if (isset($filePath) && file_exists(storage_path('app/'.$filePath))) {
                unlink(storage_path('app/'.$filePath));
            }

            session()->forget([
                'question_import_driver',
                'question_import_file_path',
                'question_import_file_original_name',
                'question_import_academic_subject_id',
                'question_import_academic_topic_id',
                'question_import_academic_subtopic_id',
                'question_import_preview_results',
                'question_import_processing',
            ]);

            \Log::error('Excel question import failed', [
                'error' => $e->getMessage(),
                'subject_id' => $academic_subject->id,
                'topic_id' => $academic_topic?->id,
            ]);

            return $this->backToImportForm($academic_group, $academic_level, $academic_subject, $academic_topic)
                ->with('import_error', 'An error occurred while importing questions: '.$e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Async AI import endpoints
    // -------------------------------------------------------------------------

    /**
     * Show the polling/status page while the queued job runs.
     * Accessible via GET questions.import.status
     */
    public function showStatus(QuestionImportBatch $batch): View|RedirectResponse
    {
        if (! $batch->isOwnedBy(Auth::id())) {
            abort(403);
        }

        // If the job already finished by the time the user lands here, skip
        // the waiting screen entirely and go straight to the preview/confirm page.
        if ($batch->status === QuestionImportBatch::STATUS_COMPLETED) {
            return redirect()->route('questions.import.preview.confirm', ['batch' => $batch]);
        }

        if ($batch->status === QuestionImportBatch::STATUS_FAILED) {
            $backUrl = $this->resolveBackUrl($batch);
            return view('questions.import-status', compact('batch', 'backUrl'));
        }

        $backUrl = $this->resolveBackUrl($batch);

        return view('questions.import-status', compact('batch', 'backUrl'));
    }

    /**
     * JSON endpoint polled every few seconds from the status page.
     * Returns status + redirect URL when complete, or error message if failed.
     */
    public function pollStatus(QuestionImportBatch $batch): JsonResponse
    {
        if (! $batch->isOwnedBy(Auth::id())) {
            abort(403);
        }

        // Refresh from DB so we get the job's updates, not a stale cached instance.
        $batch->refresh();

        return match ($batch->status) {
            QuestionImportBatch::STATUS_COMPLETED => response()->json([
                'status' => 'completed',
                'redirect' => route('questions.import.preview.confirm', ['batch' => $batch]),
            ]),

            QuestionImportBatch::STATUS_FAILED => response()->json([
                'status' => 'failed',
                'message' => $batch->error_message ?? 'The import failed for an unknown reason.',
            ]),

            default => response()->json([
                'status' => $batch->status, // 'pending' or 'processing'
            ]),
        };
    }

    /**
     * Preview/confirm page shown after the job completes.
     * Mirrors the synchronous preview page but is sourced from the batch record
     * rather than session, so it survives page reloads.
     */
    public function previewConfirm(QuestionImportBatch $batch): View|RedirectResponse
    {
        if (! $batch->isOwnedBy(Auth::id())) {
            abort(403);
        }

        if (! $batch->isFinished()) {
            // Job still running — send back to the status/polling page.
            return redirect()->route('questions.import.status', ['batch' => $batch]);
        }

        if ($batch->status === QuestionImportBatch::STATUS_FAILED) {
            $backUrl = $this->resolveBackUrl($batch);
            return redirect($backUrl)->with('import_error', $batch->error_message ?? 'Import failed.');
        }

        $batch->load('academicTopic.academicSubject.academicLevel.academicGroup', 'academicSubtopic', 'academicSubject');

        $displayData = $this->normalizeAiResultsForDisplay([
            'results' => $batch->results ?? [],
            'errors' => $batch->errors ?? [],
            'extraction_method' => $batch->extraction_method,
        ]);

        return view('questions.preview', [
            'academicTopic' => $batch->academicTopic,
            'academicSubtopic' => $batch->academicSubtopic,
            'academicSubject' => $batch->academicSubject,
            'academic_subject' => $batch->academicSubject,
            'academic_group' => $batch->academicTopic?->academicSubject?->academicLevel?->academicGroup,
            'academic_level' => $batch->academicTopic?->academicSubject?->academicLevel,
            'previewData' => $displayData,
            'importDriver' => 'ai_document',
            'batch' => $batch,
        ]);
    }

    /**
     * Confirm and save the AI-extracted questions from a completed batch.
     * Called by the preview/confirm page's "Import" button.
     */
    public function confirmImport(Request $request, QuestionImportBatch $batch): RedirectResponse
    {
        if (! $batch->isOwnedBy(Auth::id())) {
            abort(403);
        }

        if ($batch->status !== QuestionImportBatch::STATUS_COMPLETED) {
            return redirect()->route('questions.import.status', ['batch' => $batch])
                ->with('import_error', 'The import is not ready yet.');
        }

        $batch->load('academicTopic.academicSubject.academicLevel.academicGroup', 'academicSubtopic');

        $topic = $batch->academicTopic;
        $subtopic = $batch->academicSubtopic;

        if (! $topic) {
            return redirect($this->resolveBackUrl($batch))
                ->with('import_error', 'Could not locate the topic for this import. Please try again.');
        }

        try {
            $result = $this->aiImportService->save(
                $batch->results ?? [],
                $topic,
                $subtopic,
                Auth::id(),
                $batch->errors ?? []
            );

            // Clean up the stored file now that we're done with it.
            $fullPath = storage_path('app/'.$batch->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Mark batch as fully consumed so the user can't re-import it.
            $batch->update(['status' => 'saved']);

            $stats = [
                'multiple_choice' => count($result['created_ids']['multiple_choice'] ?? []),
                'true_false' => count($result['created_ids']['true_false'] ?? []),
                'essay' => count($result['created_ids']['essay'] ?? []),
            ];
            $total = $result['created_count'];
            $importErrors = $result['errors'] ?? [];

            $message = "Successfully imported {$total} question".($total === 1 ? '' : 's').': '
                .implode(', ', array_filter([
                    $stats['multiple_choice'] ? $stats['multiple_choice'].' multiple choice' : null,
                    $stats['true_false'] ? $stats['true_false'].' true/false' : null,
                    $stats['essay'] ? $stats['essay'].' essay' : null,
                ])).'.';

            $academicSubject = $topic->academicSubject;
            $academicLevel = $academicSubject->academicLevel;
            $academicGroup = $academicLevel->academicGroup;

            [$route, $route_params] = $this->topicOrSubjectRoute(
                $academicGroup, $academicLevel, $academicSubject, $topic
            );

            if (! empty($importErrors)) {
                $message .= ' '.count($importErrors).' issue(s) were flagged — please review.';
                return redirect()->route($route, $route_params)
                    ->with('warning', $message)
                    ->with('import_errors', $importErrors);
            }

            return redirect()->route($route, $route_params)->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Confirm import failed', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('questions.import.preview.confirm', ['batch' => $batch])
                ->with('import_error', 'An error occurred while saving questions: '.$e->getMessage());
        }
    }

    /**
     * Download Excel template for question import at topic level
     */
    public function downloadTemplate(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        AcademicTopic $academic_topic
    ) {
        return $this->generateTemplate($academic_group, $academic_level, $academic_subject, $academic_topic);
    }

    /**
     * Download Excel template for question import at subject level
     */
    public function downloadSubjectTemplate(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject
    ) {
        return $this->generateTemplate($academic_group, $academic_level, $academic_subject, null);
    }

    /**
     * Generate Excel template for question import
     */
    private function generateTemplate(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        ?AcademicTopic $academic_topic = null
    ) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'question',
            'type',
            'academic_topic_id',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'answer',
            'difficulty_level',
            'score',
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column.'1', $header);
            $column++;
        }

        $sheet->setCellValue('A2', 'What is the capital of France?');
        $sheet->setCellValue('B2', 'multiple_choice');
        $sheet->setCellValue('C2', $academic_topic?->id ?: 'TOPIC_ID');
        $sheet->setCellValue('D2', 'London');
        $sheet->setCellValue('E2', 'Paris');
        $sheet->setCellValue('F2', 'Berlin');
        $sheet->setCellValue('G2', 'Madrid');
        $sheet->setCellValue('H2', '');
        $sheet->setCellValue('I2', 'B');
        $sheet->setCellValue('J2', 'medium');
        $sheet->setCellValue('K2', '1');

        $sheet->setCellValue('A3', 'The Earth is round.');
        $sheet->setCellValue('B3', 'true_false');
        $sheet->setCellValue('C3', $academic_topic?->id ?: 'TOPIC_ID');
        $sheet->setCellValue('D3', '');
        $sheet->setCellValue('E3', '');
        $sheet->setCellValue('F3', '');
        $sheet->setCellValue('G3', '');
        $sheet->setCellValue('H3', '');
        $sheet->setCellValue('I3', 'true');
        $sheet->setCellValue('J3', 'easy');
        $sheet->setCellValue('K3', '1');

        $sheet->setCellValue('A4', 'Explain the theory of relativity.');
        $sheet->setCellValue('B4', 'essay');
        $sheet->setCellValue('C4', $academic_topic?->id ?: 'TOPIC_ID');
        $sheet->setCellValue('D4', '');
        $sheet->setCellValue('E4', '');
        $sheet->setCellValue('F4', '');
        $sheet->setCellValue('G4', '');
        $sheet->setCellValue('H4', '');
        $sheet->setCellValue('I4', 'Brief explanation...');
        $sheet->setCellValue('J4', 'hard');
        $sheet->setCellValue('K4', '5');

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6E6'],
            ],
        ];

        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'question_import_template.xlsx';
        $tempFile = storage_path('app/temp/'.$fileName);

        if (! file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    /**
     * Flatten DocumentAiQuestionImportService's type-bucketed results
     * (['results' => ['multiple_choice' => [...], 'true_false' => [...], 'essay' => [...]], 'errors' => [...]])
     * into the same flat shape the Excel pipeline's preview produces
     * (['preview' => [one row per question], 'errors' => [['message' => ...], ...]])
     * so questions.preview.blade.php can render both drivers without modification.
     */
    private function normalizeAiResultsForDisplay(array $aiPreviewResult): array
    {
        $rowNumber = 1;
        $rows = [];

        foreach ($aiPreviewResult['results']['multiple_choice'] ?? [] as $item) {
            $options = $item['options'] ?? [];
            $rows[] = [
                'row_number' => $rowNumber++,
                'question' => $item['question_plain'] ?? '',
                'type' => 'multiple_choice',
                'option_a' => $options[0]['plain'] ?? '',
                'option_b' => $options[1]['plain'] ?? '',
                'option_c' => $options[2]['plain'] ?? '',
                'option_d' => $options[3]['plain'] ?? '',
                'option_e' => $options[4]['plain'] ?? '',
                'answer' => strtoupper($item['correct_option'] ?? ''),
                'difficulty_level' => $item['difficulty_level'] ?? 'medium',
                'score' => $item['score'] ?? 1,
            ];
        }

        foreach ($aiPreviewResult['results']['true_false'] ?? [] as $item) {
            $rows[] = [
                'row_number' => $rowNumber++,
                'question' => $item['statement_plain'] ?? '',
                'type' => 'true_false',
                'option_a' => '',
                'option_b' => '',
                'option_c' => '',
                'option_d' => '',
                'option_e' => '',
                'answer' => $item['correct_answer'] ?? null,
                'difficulty_level' => $item['difficulty_level'] ?? 'medium',
                'score' => $item['score'] ?? 1,
            ];
        }

        foreach ($aiPreviewResult['results']['essay'] ?? [] as $item) {
            $rows[] = [
                'row_number' => $rowNumber++,
                'question' => $item['question_plain'] ?? '',
                'type' => 'essay',
                'option_a' => '',
                'option_b' => '',
                'option_c' => '',
                'option_d' => '',
                'option_e' => '',
                'answer' => $item['model_answer_plain'] ?? '',
                'difficulty_level' => $item['difficulty_level'] ?? 'medium',
                'score' => $item['score'] ?? 1,
            ];
        }

        $errors = array_map(
            fn (string $message) => ['message' => $message],
            $aiPreviewResult['errors'] ?? []
        );

        if (empty($rows) && empty($errors)) {
            $errors[] = ['message' => 'No questions could be extracted from this document.'];
        }

        return [
            'preview' => $rows,
            'errors' => $errors,
        ];
    }

    /**
     * Build a redirect back to the appropriate (topic- or subject-level) import form.
     */
    private function backToImportForm(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        ?AcademicTopic $academic_topic
    ): RedirectResponse {
        [$route, $params] = $this->topicOrSubjectRoute(
            $academic_group,
            $academic_level,
            $academic_subject,
            $academic_topic,
            import: true
        );

        return redirect()->route($route, $params);
    }

    /**
     * Resolve the import form URL from a batch record (used when we don't have
     * the full hierarchy model chain injected, e.g. on the async status page).
     */
    private function resolveBackUrl(QuestionImportBatch $batch): string
    {
        $batch->load('academicTopic.academicSubject.academicLevel.academicGroup');

        $topic = $batch->academicTopic;

        if ($topic) {
            $subject = $topic->academicSubject;
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            return route('questions.import.form', [
                'academic_topic' => $topic,
                'academic_subject' => $subject,
                'academic_level' => $level,
                'academic_group' => $group,
            ]);
        }

        $subject = $batch->academicSubject;
        if ($subject) {
            $level = $subject->academicLevel;
            $group = $level->academicGroup;

            return route('questions.subject.import.form', [
                'academic_subject' => $subject,
                'academic_level' => $level,
                'academic_group' => $group,
            ]);
        }

        return route('dashboard');
    }

    /**
     * Return [$routeName, $routeParams] pointing at the topic or subject show
     * page (or their import-form equivalent when $import = true).
     */
    private function topicOrSubjectRoute(
        AcademicGroup $academic_group,
        AcademicLevel $academic_level,
        AcademicSubject $academic_subject,
        ?AcademicTopic $academic_topic,
        bool $import = false,
    ): array {
        $base = [
            'academic_subject' => $academic_subject,
            'academic_level' => $academic_level,
            'academic_group' => $academic_group,
        ];

        if ($academic_topic) {
            return [
                $import ? 'questions.import.form' : 'academic-topics.show',
                array_merge($base, ['academic_topic' => $academic_topic]),
            ];
        }

        return [
            $import ? 'questions.subject.import.form' : 'academic-subjects.show',
            $base,
        ];
    }
}