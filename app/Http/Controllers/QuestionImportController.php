<?php

namespace App\Http\Controllers;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\AcademicSubject;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use App\Services\QuestionImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class QuestionImportController extends Controller
{
    public function __construct(
        private QuestionImportService $importService
    ) {}

    /**
     * Show the import form for questions
     */
    public function showImportForm(
        AcademicGroup $academic_group, 
        AcademicLevel $academic_level, 
        AcademicSubject $academic_subject, 
        AcademicTopic $academic_topic, 
        ?AcademicSubtopic $academicSubtopic = null
    ): View {
        // $this->authorize('update', $academic_topic);
        
        return view('questions.import', [
            'academicTopic' => $academic_topic,
            'academicSubtopic' => $academicSubtopic,
        ]);
    }

    /**
     * Preview the questions from the uploaded file before saving
     */
    public function previewQuestions(
        Request $request, 
        AcademicGroup $academic_group, 
        AcademicLevel $academic_level, 
        AcademicSubject $academic_subject, 
        AcademicTopic $academic_topic, 
        ?AcademicSubtopic $academicSubtopic = null
    ) {
        // Check if this is actually a file upload request
        if (!$request->hasFile('questions_file')) {
            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('error', 'Please select a file to preview questions.');
        }

        $validator = Validator::make($request->all(), [
            'questions_file' => 'required|file|mimes:xlsx,xls,docx,doc,pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('questions_file');
        
        try {
            // Preview the questions without saving them
            $previewResult = $this->importService->previewQuestions(
                $file,
                $academic_topic,
                $academicSubtopic,
                Auth::id()
            );

            // Store the file path in session for later use during import
            $filePath = $file->store('temp/question-imports', 'local');
            session(['question_import_file_path' => $filePath]);
            session(['question_import_file_original_name' => $file->getClientOriginalName()]);
            session(['question_import_academic_topic_id' => $academic_topic->id]);
            session(['question_import_academic_subtopic_id' => $academicSubtopic?->id]);
            // Store the preview results to reuse during import
            session(['question_import_preview_results' => $previewResult]);
            // Set a flag to prevent duplicate imports
            session(['question_import_processing' => false]);

            return view('questions.preview', [
                'academicTopic' => $academic_topic,
                'academicSubtopic' => $academicSubtopic,
                'previewData' => $previewResult,
                'file' => $file,
                'academic_group' => $academic_group,
                'academic_level' => $academic_level,
                'academic_subject' => $academic_subject,
            ]);
                
        } catch (\Exception $e) {
            \Log::error('Question preview failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'topic_id' => $academic_topic->id,
                'subtopic_id' => $academicSubtopic?->id,
            ]);

            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('error', 'An error occurred while previewing questions: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Import the questions after preview
     */
    public function importQuestions(
        Request $request, 
        AcademicGroup $academic_group, 
        AcademicLevel $academic_level, 
        AcademicSubject $academic_subject, 
        AcademicTopic $academic_topic, 
        ?AcademicSubtopic $academicSubtopic = null
    ): RedirectResponse {
        // Check if import is already in progress to prevent duplicates
        if (session('question_import_processing', false)) {
            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('error', 'Import is already in progress. Please wait or refresh the page.');
        }

        // Mark import as in progress
        session(['question_import_processing' => true]);

        // Retrieve the preview results from session that were stored during preview
        $previewResults = session('question_import_preview_results');
        $filePath = session('question_import_file_path');
        $originalFileName = session('question_import_file_original_name');
        $storedTopicId = session('question_import_academic_topic_id');
        $storedSubtopicId = session('question_import_academic_subtopic_id');

        // Verify that the session data exists and matches the current request
        if (!$previewResults || $storedTopicId != $academic_topic->id) {
            // Clear the processing flag
            session()->forget('question_import_processing');
            
            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('error', 'Import session expired. Please restart the import process.');
        }

        try {
            // Perform the actual import using the preview data
            $result = $this->importService->importFromPreviewData(
                $previewResults,
                $academic_topic,
                $academicSubtopic,
                Auth::id()
            );

            // Clean up the temporary file if it exists
            if ($filePath) {
                $fullFilePath = storage_path('app/' . $filePath);
                if (file_exists($fullFilePath)) {
                    unlink($fullFilePath);
                }
            }

            // Clear session data
            session()->forget([
                'question_import_file_path', 
                'question_import_file_original_name', 
                'question_import_academic_topic_id', 
                'question_import_academic_subtopic_id', 
                'question_import_preview_results',
                'question_import_processing'
            ]);

            $totalImported = array_sum($this->importService->getImportStatistics());
            
            $message = "Successfully imported {$totalImported} questions: ";
            $stats = [];
            $stats[] = $this->importService->getImportStatistics()['multiple_choice'] . " multiple choice";
            $stats[] = $this->importService->getImportStatistics()['true_false'] . " true/false";
            $stats[] = $this->importService->getImportStatistics()['essay'] . " essay";
            
            $message .= implode(", ", $stats);

            if (!empty($result['errors'])) {
                $message .= ". " . count($result['errors']) . " errors occurred during import.";
                
                return redirect()
                    ->route('academic-topics.show', [
                        'academic_topic' => $academic_topic,
                        'academic_subject' => $academic_subject,
                        'academic_level' => $academic_level,
                        'academic_group' => $academic_group
                    ])
                    ->with('warning', $message)
                    ->with('import_errors', $result['errors']);
            }

            return redirect()
                ->route('academic-topics.show', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('success', $message);
                
        } catch (\Exception $e) {
            // Clean up the temporary file in case of error
            if (isset($filePath) && file_exists(storage_path('app/' . $filePath))) {
                unlink(storage_path('app/' . $filePath));
            }

            // Clear session data
            session()->forget([
                'question_import_file_path', 
                'question_import_file_original_name', 
                'question_import_academic_topic_id', 
                'question_import_academic_subtopic_id', 
                'question_import_preview_results',
                'question_import_processing'
            ]);

            \Log::error('Question import failed', [
                'error' => $e->getMessage(),
                'topic_id' => $academic_topic->id,
                'subtopic_id' => $academicSubtopic?->id,
            ]);

            return redirect()
                ->route('questions.import.form', [
                    'academic_topic' => $academic_topic,
                    'academic_subject' => $academic_subject,
                    'academic_level' => $academic_level,
                    'academic_group' => $academic_group
                ])
                ->with('error', 'An error occurred while importing questions: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for question import
     */
    public function downloadTemplate(
        AcademicGroup $academic_group, 
        AcademicLevel $academic_level, 
        AcademicSubject $academic_subject, 
        AcademicTopic $academic_topic
    ) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Define the headers - using the exact format expected by the system
        $headers = [
            'question',
            'type',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'answer',
            'difficulty_level',
            'score'
        ];
        
        // Set headers in the first row using the exact lowercase format expected
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);  // Using the exact expected format
            $column++;
        }
        
        // Add some example data
        $sheet->setCellValue('A2', 'What is the capital of France?');
        $sheet->setCellValue('B2', 'multiple_choice');
        $sheet->setCellValue('C2', 'London');
        $sheet->setCellValue('D2', 'Paris');
        $sheet->setCellValue('E2', 'Berlin');
        $sheet->setCellValue('F2', 'Madrid');
        $sheet->setCellValue('G2', '');
        $sheet->setCellValue('H2', 'B');
        $sheet->setCellValue('I2', 'medium');
        $sheet->setCellValue('J2', '1');
        
        $sheet->setCellValue('A3', 'The Earth is round.');
        $sheet->setCellValue('B3', 'true_false');
        $sheet->setCellValue('C3', '');
        $sheet->setCellValue('D3', '');
        $sheet->setCellValue('E3', '');
        $sheet->setCellValue('F3', '');
        $sheet->setCellValue('G3', '');
        $sheet->setCellValue('H3', 'true');
        $sheet->setCellValue('I3', 'easy');
        $sheet->setCellValue('J3', '1');
        
        $sheet->setCellValue('A4', 'Explain the theory of relativity.');
        $sheet->setCellValue('B4', 'essay');
        $sheet->setCellValue('C4', '');
        $sheet->setCellValue('D4', '');
        $sheet->setCellValue('E4', '');
        $sheet->setCellValue('F4', '');
        $sheet->setCellValue('G4', '');
        $sheet->setCellValue('H4', 'Brief explanation...');
        $sheet->setCellValue('I4', 'hard');
        $sheet->setCellValue('J4', '5');
        
        // Set column widths
        $column = 'A';
        foreach ($headers as $index => $header) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        
        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6E6E6'],
            ],
        ];
        
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        
        // Create the writer and save to a temporary file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'question_import_template.xlsx';
        $tempFile = storage_path('app/temp/' . $fileName);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $writer->save($tempFile);
        
        // Return the file for download
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}