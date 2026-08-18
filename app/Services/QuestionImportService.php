<?php

namespace App\Services;

use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Models\EssayQuestion;
use App\Support\Mark;
use App\Models\AcademicSubject;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class QuestionImportService
{
    private array $errors = [];
    private array $importedCount = [
        'multiple_choice' => 0,
        'true_false' => 0,
        'essay' => 0,
    ];
    
    public function __construct(
        private ResearchAssistantService $academicChatService,
        private DocumentContentExtractionService $documentExtractor
    ) {}

    /**
     * Import questions from a file (Excel, Word, or PDF)
     */
    public function importQuestions(mixed $file, AcademicSubject $academicSubject, ?AcademicTopic $topic = null, ?AcademicSubtopic $subtopic = null, ?int $userId = null): array
    {
        $this->errors = [];
        $this->resetImportedCount();

        // Convert Symfony UploadedFile to Illuminate UploadedFile if needed
        if ($file instanceof SymfonyUploadedFile && !$file instanceof UploadedFile) {
            $file = new UploadedFile(
                $file->getPathname(),
                $file->getClientOriginalName(),
                $file->getClientMimeType(),
                $file->getError(),
                true // trusted file
            );
        }

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException('File must be an instance of UploadedFile');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        switch ($extension) {
            case 'xlsx':
            case 'xls':
                return $this->importFromExcel($file, $academicSubject, $topic, $subtopic, $userId);
            
            case 'docx':
            case 'doc':
                return $this->importFromWord($file, $academicSubject, $topic, $subtopic, $userId);
            
            case 'pdf':
                return $this->importFromPdf($file, $academicSubject, $topic, $subtopic, $userId);
            
            default:
                throw new \InvalidArgumentException("Unsupported file format: {$extension}. Supported formats: Excel (xlsx, xls), Word (docx, doc), PDF (pdf)");
        }
    }

    /**
     * Import questions from preview data
     */
    public function importFromPreviewData(array $previewResults, AcademicSubject $academicSubject, ?AcademicTopic $topic = null, ?AcademicSubtopic $subtopic = null, ?int $userId = null): array
    {
        $this->errors = [];
        $this->resetImportedCount();

        $importResults = [
            'multiple_choice' => [],
            'true_false' => [],
            'essay' => [],
            'errors' => [],
        ];

        // Process the preview data to create actual questions
        if (isset($previewResults['preview']) && is_array($previewResults['preview'])) {
            foreach ($previewResults['preview'] as $previewItem) {
                try {
                    $questionType = $previewItem['type'] ?? 'multiple_choice';
                    $questionText = $previewItem['question'] ?? '';

                    if (empty($questionText)) {
                        continue; // Skip empty questions
                    }

                    // Create Mark object for the question text
                    $questionMark = new Mark($questionText, $questionText);

                    // Determine topic ID: prioritize topic ID from preview item (for subject-level imports with topic_id column),
                    // fallback to the topic parameter passed to the method
                    $previewItemTopicId = $previewItem['academic_topic_id'] ?? null;
                    $effectiveTopicId = $previewItemTopicId ?? $topic?->id;
                    
                    // If no effective topic ID is determined from the above, try to find it from the preview item topic name if available
                    if (!$effectiveTopicId && isset($previewItem['academic_topic_name'])) {
                        $effectiveTopic = AcademicTopic::where('name', $previewItem['academic_topic_name'])
                            ->where('academic_subject_id', $academicSubject->id)
                            ->first();
                        if ($effectiveTopic) {
                            $effectiveTopicId = $effectiveTopic->id;
                        }
                    }
                    
                    // Only proceed if we have a valid topic ID
                    if (!$effectiveTopicId) {
                        Log::error('Cannot import question without a valid topic ID', [
                            'question' => $questionText,
                            'topic' => $topic,
                            'preview_item_topic_id' => $previewItemTopicId,
                            'preview_item_topic_name' => $previewItem['academic_topic_name'] ?? null,
                            'subtopic' => $subtopic,
                            'user_id' => $userId
                        ]);
                        
                        // Add error to results so user knows what went wrong
                        $importResults['errors'][] = [
                            'row' => $previewItem['row_number'] ?? 'unknown',
                            'message' => 'Cannot import question: no valid topic ID specified. For subject-level import, academic_topic_id column is required in the file.'
                        ];
                        
                        continue; // Skip this question
                    }

                    $commonData = [
                        'question' => $questionMark, // Use the Mark object
                        'difficulty_level' => $previewItem['difficulty_level'] ?? 'medium',
                        'score' => $previewItem['score'] ?? 1,
                        'academic_topic_id' => $effectiveTopicId, // Use the validated topic ID
                        'academic_subtopic_id' => $subtopic?->id, // This might be null
                        'added_by' => $userId,
                        'modified_by' => $userId,
                    ];

                    switch (strtolower($questionType)) {
                        case 'multiple_choice':
                        case 'mcq':
                        case 'multiple choice':
                            $optionA = $previewItem['option_a'] ?? '';
                            $optionB = $previewItem['option_b'] ?? '';
                            $optionC = $previewItem['option_c'] ?? '';
                            $optionD = $previewItem['option_d'] ?? '';
                            $optionE = $previewItem['option_e'] ?? '';
                            
                            $optionAHtml = $previewItem['option_a_html'] ?? $optionA;
                            $optionBHtml = $previewItem['option_b_html'] ?? $optionB;
                            $optionCHtml = $previewItem['option_c_html'] ?? $optionC;
                            $optionDHtml = $previewItem['option_d_html'] ?? $optionD;
                            $optionEHtml = $previewItem['option_e_html'] ?? $optionE;
                            
                            $commonData['option_a'] = new Mark($optionA, $optionAHtml);
                            $commonData['option_b'] = new Mark($optionB, $optionBHtml);
                            $commonData['option_c'] = new Mark($optionC, $optionCHtml);
                            $commonData['option_d'] = new Mark($optionD, $optionDHtml);
                            $commonData['option_e'] = new Mark($optionE, $optionEHtml);
                            $commonData['answer']   = $previewItem['answer'] ?? '';

                            if (empty(trim($questionText)) || empty(trim($optionA)) || empty(trim($optionB))) {
                                $importResults['errors'][] = [
                                    'row'     => $previewItem['row_number'] ?? 'unknown',
                                    'message' => 'Multiple choice question requires at least options A and B.',
                                ];
                                continue 2;
                            }

                            $question = MultipleChoiceQuestion::create($commonData);
                            $importResults['multiple_choice'][] = $question->id;
                            $this->importedCount['multiple_choice']++;
                            break;

                        case 'true_false':
                        case 'true/false':
                        case 'tf':
                            $answer = $previewItem['answer'] ?? '';
                            if (is_bool($answer)) {
                                $commonData['answer'] = $answer;
                            } else {
                                $normalised = strtolower(trim((string) $answer));
                                if (in_array($normalised, ['true', '1', 'yes'], true)) {
                                    $commonData['answer'] = true;
                                } elseif (in_array($normalised, ['false', '0', 'no'], true)) {
                                    $commonData['answer'] = false;
                                } else {
                                    $importResults['errors'][] = [
                                        'row'     => $previewItem['row_number'] ?? 'unknown',
                                        'message' => 'True/False answer must be "true", "false", "1", "0", "yes", or "no". Got: "' . $answer . '"',
                                    ];
                                    continue 2;
                                }
                            }

                            if (empty(trim($questionText))) {
                                $importResults['errors'][] = [
                                    'row'     => $previewItem['row_number'] ?? 'unknown',
                                    'message' => 'Question text is required.',
                                ];
                                continue 2;
                            }

                            $question = TrueOrFalseQuestion::create($commonData);
                            $importResults['true_false'][] = $question->id;
                            $this->importedCount['true_false']++;
                            break;

                        case 'essay':
                        case 'short_answer':
                        case 'short answer':
                        case 'written':
                            $commonData['answer'] = $previewItem['answer'] ?? '';

                            if (empty(trim($questionText))) {
                                $importResults['errors'][] = [
                                    'row'     => $previewItem['row_number'] ?? 'unknown',
                                    'message' => 'Question text is required.',
                                ];
                                continue 2;
                            }

                            $question = EssayQuestion::create($commonData);
                            $importResults['essay'][] = $question->id;
                            $this->importedCount['essay']++;
                            break;

                        default:
                            $importResults['errors'][] = [
                                'row'     => $previewItem['row_number'] ?? 'unknown',
                                'message' => "Unknown question type \"{$questionType}\". Supported types: multiple_choice, true_false, essay.",
                            ];
                            continue 2;
                    }
                } catch (\Exception $e) {
                    Log::error('Error creating question from preview data', [
                        'error' => $e->getMessage(),
                        'preview_item' => $previewItem,
                        'topic_id' => $effectiveTopicId,
                    ]);
                    $importResults['errors'][] = [
                        'row'     => $previewItem['row_number'] ?? 'unknown',
                        'message' => 'Failed to save question: ' . $e->getMessage(),
                    ];
                    continue; // Continue to next question
                }
            }
        }

        return $importResults;
    }

    /**
     * Preview questions from a file without saving them
     */
    public function previewQuestions(mixed $file, AcademicSubject $academicSubject, ?AcademicTopic $topic = null, ?AcademicSubtopic $subtopic = null, ?int $userId = null): array
    {
        // Convert Symfony UploadedFile to Illuminate UploadedFile if needed
        if ($file instanceof SymfonyUploadedFile && !$file instanceof UploadedFile) {
            $file = new UploadedFile(
                $file->getPathname(),
                $file->getClientOriginalName(),
                $file->getClientMimeType(),
                $file->getError(),
                true // trusted file
            );
        }

        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException('File must be an instance of UploadedFile');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        switch ($extension) {
            case 'xlsx':
            case 'xls':
                return $this->previewFromExcel($file, $academicSubject, $topic, $subtopic, $userId);
            
            case 'docx':
            case 'doc':
            case 'pdf':
                // For Word and PDF files, we need to process them with AI first
                // Then return the preview of what would be imported
                $text = $this->academicChatService->extractFileContent($file);
                
                // If the extracted text is empty or doesn't contain meaningful content, 
                // return an empty preview
                $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
                if (empty($cleanText) || strlen($cleanText) < 10) { // At least 10 characters to consider it meaningful
                    return [
                        'multiple_choice' => [],
                        'true_false' => [],
                        'essay' => [],
                        'errors' => [
                            ['row' => 0, 'message' => 'The document appears to be empty or does not contain sufficient content to extract questions from.']
                        ]
                    ];
                }
                
                return $this->previewQuestionsFromText($text, $academicSubject, $topic, $subtopic, $userId);
            
            default:
                throw new \InvalidArgumentException("Unsupported file format: {$extension}. Supported formats: Excel (xlsx, xls), Word (docx, doc), PDF (pdf)");
        }
    }

    /**
     * Import questions from Excel file
     */
    private function importFromExcel(UploadedFile $file, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            
            return $this->processWorksheet($worksheet, $academicSubject, $topic, $subtopic, $userId, true); // Save to DB
        } catch (\Exception $e) {
            Log::error('Error importing questions from Excel', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            throw $e;
        }
    }

    /**
     * Preview questions from Excel file
     */
    private function previewFromExcel(UploadedFile $file, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            
            return $this->processWorksheet($worksheet, $academicSubject, $topic, $subtopic, $userId, false); // Don't save to DB, just preview
        } catch (\Exception $e) {
            Log::error('Error previewing questions from Excel', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            throw $e;
        }
    }

    /**
     * Process worksheet data and either import or preview questions
     */
    private function processWorksheet(Worksheet $worksheet, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId, bool $saveToDb = true): array
    {
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            throw new \InvalidArgumentException('The Excel file is empty or contains no data.');
        }

        // Get headers from first row
        $headers = array_shift($rows);
        $headers = array_map('strtolower', array_map('trim', $headers));

        // Find required columns
        $questionCol = $this->findColumnIndex($headers, ['question', 'questions', 'text']);
        $typeCol = $this->findColumnIndex($headers, ['type', 'question_type', 'qtype']);
        $topicIdCol = $this->findColumnIndex($headers, ['academic_topic_id', 'topic_id']);
        
        // Check if required columns exist
        if ($questionCol === null) {
            throw new \InvalidArgumentException('Required column "question" not found in Excel file. Please ensure your file has a "question" column.');
        }

        $answerCol = $this->findColumnIndex($headers, ['answer', 'correct_answer', 'answers']);
        $difficultyCol = $this->findColumnIndex($headers, ['difficulty', 'difficulty_level', 'level']);
        $scoreCol = $this->findColumnIndex($headers, ['score', 'points', 'marks']);
        $optionACol = $this->findColumnIndex($headers, ['option_a', 'a', 'option1']);
        $optionBCol = $this->findColumnIndex($headers, ['option_b', 'b', 'option2']);
        $optionCCol = $this->findColumnIndex($headers, ['option_c', 'c', 'option3']);
        $optionDCol = $this->findColumnIndex($headers, ['option_d', 'd', 'option4']);
        $optionECol = $this->findColumnIndex($headers, ['option_e', 'e', 'option5']);

        $importResults = [
            'multiple_choice' => [],
            'true_false' => [],
            'essay' => [],
            'errors' => [],
            'preview' => [] // Store preview data
        ];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 for header row, +1 for 1-indexed display
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                $questionType = $this->getColumnValue($row, $typeCol, 'multiple_choice'); // Default to multiple choice
                
                // Determine academic_topic_id: prioritize from Excel file, fallback to provided topic
                $excelTopicId = $this->getColumnValue($row, $topicIdCol, null);
                $effectiveTopic = null;
                
                if ($excelTopicId) {
                    // If academic_topic_id is provided in Excel, use that topic
                    $effectiveTopic = AcademicTopic::where('id', $excelTopicId)
                        ->where('academic_subject_id', $academicSubject->id) // Ensure it belongs to the same subject
                        ->first();
                    
                    if (!$effectiveTopic) {
                        $importResults['errors'][] = [
                            'row' => $rowNumber,
                            'message' => "Invalid academic_topic_id: {$excelTopicId}. Topic does not exist or does not belong to the selected subject."
                        ];
                        continue;
                    }
                } else {
                    // Otherwise use the topic passed to the method (could be null for subject-level import)
                    $effectiveTopic = $topic;
                }
                
                // If no effective topic is determined, show error
                if (!$effectiveTopic) {
                    $importResults['errors'][] = [
                        'row' => $rowNumber,
                        'message' => "academic_topic_id is required but not provided in Excel file and no default topic specified."
                    ];
                    continue;
                }

                $questionData = [
                    'question' => $this->getColumnValue($row, $questionCol, ''),
                    'answer' => $this->getColumnValue($row, $answerCol, ''),
                    'difficulty_level' => $this->getColumnValue($row, $difficultyCol, 'medium'),
                    'score' => (float) $this->getColumnValue($row, $scoreCol, 1),
                    'academic_topic_id' => $effectiveTopic->id,
                    'academic_subtopic_id' => $subtopic?->id,
                    'added_by' => $userId,
                    'modified_by' => $userId,
                ];

                // Validate required fields
                if (empty($questionData['question'])) {
                    $importResults['errors'][] = [
                        'row' => $rowNumber,
                        'message' => 'Question text is required'
                    ];
                    continue;
                }

                $question = null;
                
                // Prepare preview data regardless of whether we save or not
                $previewItem = [
                    'row_number' => $rowNumber,
                    'question' => $questionData['question'],
                    'type' => $questionType,
                    'answer' => $questionData['answer'],
                    'difficulty_level' => $questionData['difficulty_level'],
                    'score' => $questionData['score'],
                    'academic_topic_id' => $effectiveTopic->id,
                    'academic_topic_name' => $effectiveTopic->name,
                ];

                switch (strtolower($questionType)) {
                    case 'multiple_choice':
                    case 'mcq':
                    case 'multiple choice':
                        $optA = $this->getColumnValue($row, $optionACol, '');
                        $optB = $this->getColumnValue($row, $optionBCol, '');
                        $optC = $this->getColumnValue($row, $optionCCol, '');
                        $optD = $this->getColumnValue($row, $optionDCol, '');
                        $optE = $this->getColumnValue($row, $optionECol, '');
                        
                        // For Excel, use plain text for both up and down since formatting is not preserved
                        $questionData['option_a'] = new Mark($optA, $optA);
                        $questionData['option_b'] = new Mark($optB, $optB);
                        $questionData['option_c'] = new Mark($optC, $optC);
                        $questionData['option_d'] = new Mark($optD, $optD);
                        $questionData['option_e'] = new Mark($optE, $optE);

                        $previewItem['option_a'] = $optA;
                        $previewItem['option_b'] = $optB;
                        $previewItem['option_c'] = $optC;
                        $previewItem['option_d'] = $optD;
                        $previewItem['option_e'] = $optE;
                        
                        // Validate that multiple choice has options
                        if (empty($optA) || empty($optB)) {
                            $importResults['errors'][] = [
                                'row' => $rowNumber,
                                'message' => 'Multiple choice questions require at least options A and B'
                            ];
                            continue 2; // Continue the outer foreach loop
                        }

                        if ($saveToDb) {
                            $question = MultipleChoiceQuestion::create($questionData);
                            $importResults['multiple_choice'][] = $question->id;
                            $this->importedCount['multiple_choice']++;
                        } else {
                            $previewItem['id'] = 'preview_' . $rowNumber;
                        }
                        break;

                    case 'true_false':
                    case 'true/false':
                    case 'tf':
                        // Normalize the answer to boolean
                        $answer = strtolower(trim($questionData['answer']));
                        if ($answer === 'true' || $answer === '1' || $answer === 'yes') {
                            $questionData['answer'] = true;
                        } elseif ($answer === 'false' || $answer === '0' || $answer === 'no') {
                            $questionData['answer'] = false;
                        } else {
                            $importResults['errors'][] = [
                                'row' => $rowNumber,
                                'message' => 'True/False answer must be "true", "false", "1", "0", "yes", or "no"'
                            ];
                            continue 2; // Continue the outer foreach loop
                        }

                        if ($saveToDb) {
                            $question = TrueOrFalseQuestion::create($questionData);
                            $importResults['true_false'][] = $question->id;
                            $this->importedCount['true_false']++;
                        } else {
                            $previewItem['id'] = 'preview_' . $rowNumber;
                        }
                        break;

                    case 'essay':
                    case 'short_answer':
                    case 'short answer':
                    case 'written':
                        // Essay questions use the answer field for the sample answer
                        if ($saveToDb) {
                            $question = EssayQuestion::create($questionData);
                            $importResults['essay'][] = $question->id;
                            $this->importedCount['essay']++;
                        } else {
                            $previewItem['id'] = 'preview_' . $rowNumber;
                        }
                        break;

                    default:
                        $importResults['errors'][] = [
                            'row' => $rowNumber,
                            'message' => "Unknown question type: {$questionType}. Supported types: multiple_choice, true_false, essay"
                        ];
                        continue 2; // Continue the outer foreach loop
                }

                // Add to preview data regardless of save flag
                $importResults['preview'][] = $previewItem;

            } catch (\Exception $e) {
                $importResults['errors'][] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $importResults;
    }

    /**
     * Import questions from Word document
     */
    private function importFromWord(UploadedFile $file, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): array
    {
        try {
            // Extract content with HTML formatting preserved
            $extracted = $this->documentExtractor->extractFromWord($file);
            $plainText = $extracted['plain'];
            $htmlText = $extracted['html'];

            // Check if content is meaningful
            $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($htmlText)));
            if (empty($cleanText) || strlen($cleanText) < 10) {
                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'The Word document appears to be empty or does not contain sufficient content to extract questions from.']
                    ]
                ];
            }

            // Parse questions using AI with plain text, then match HTML formatting
            return $this->parseQuestionsFromText($plainText, $htmlText, $academicSubject, $topic, $subtopic, $userId, true);
        } catch (\Exception $e) {
            Log::error('Error importing questions from Word', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            throw $e;
        }
    }

    /**
     * Import questions from PDF
     */
    private function importFromPdf(UploadedFile $file, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): array
    {
        try {
            // Extract the text content from the PDF
            $text = $this->academicChatService->extractFileContent($file);

            // If the extracted text is empty or doesn't contain meaningful content, 
            // we shouldn't process it as questions
            $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
            if (empty($cleanText) || strlen($cleanText) < 10) { // At least 10 characters to consider it meaningful
                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'The PDF appears to be empty or does not contain sufficient content to extract questions from.']
                    ]
                ];
            }

            // Use AI to parse the content and extract questions
            return $this->parseQuestionsFromText($text, $academicSubject, $topic, $subtopic, $userId, true); // Save to DB
        } catch (\Exception $e) {
            Log::error('Error importing questions from PDF', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            throw $e;
        }
    }

    /**
     * Preview questions from text content using AI
     */
    private function previewQuestionsFromText(string $text, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): array
    {
        return $this->parseQuestionsFromText($text, $text, $academicSubject, $topic, $subtopic, $userId, false);
    }

    /**
     * Parse questions from text content using AI
     */
    private function parseQuestionsFromText(string $plainText, string $htmlText, AcademicSubject $academicSubject, ?AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId, bool $saveToDb = true): array
    {
        try {
            // Build a prompt for the AI to extract and format questions from the text
            $prompt = "Extract educational questions from the following text and format them as JSON. The questions should be related to the topic: {$topic->name}. ";

            if ($subtopic) {
                $prompt .= "The questions should also relate to the subtopic: {$subtopic->name}. ";
            }

            $prompt .= "Please provide the questions in the following JSON format:\n";
            $prompt .= "{\n";
            $prompt .= "  \"questions\": [\n";
            $prompt .= "    {\n";
            $prompt .= "      \"type\": \"multiple_choice\", // or \"true_false\" or \"essay\"\n";
            $prompt .= "      \"question\": \"The question text\",\n";
            $prompt .= "      \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"], // for multiple choice\n";
            $prompt .= "      \"answer\": \"A\", // for multiple choice, or true/false for true_false, or answer text for essay\n";
            $prompt .= "      \"difficulty_level\": \"medium\", // easy, medium, hard\n";
            $prompt .= "      \"score\": 1 // point value\n";
            $prompt .= "    }\n";
            $prompt .= "  ]\n";
            $prompt .= "}\n\n";
            $prompt .= "Text to extract questions from:\n";
            $prompt .= $plainText;

            // Call the AcademicChatService to process the request
            $result = $this->academicChatService->chat([
                'input' => $prompt,
                'request_type' => 'question_extraction',
                'subject' => $topic->academicSubject->name ?? 'General',
                'academic_level' => $topic->academicSubject->academicLevel->name ?? 'General',
                'topics' => [$topic->name],
                'subtopics' => $subtopic ? [$subtopic->name] : [],
            ]);

            if (!$result['success']) {
                Log::error('AI processing failed for question extraction', [
                    'error' => $result['error'] ?? 'Unknown error',
                    'topic_id' => $topic->id,
                    'subtopic_id' => $subtopic?->id,
                ]);

                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'AI processing failed: ' . ($result['error'] ?? 'Unknown error')]
                    ]
                ];
            }

            // Extract the JSON from the AI response
            $responseContent = $result['content'] ?? '';
            $jsonMatch = [];
            
            // Try to extract JSON from the response
            preg_match('/\{(?:[^{}]|(?R))*\}/s', $responseContent, $jsonMatch);
            
            if (empty($jsonMatch)) {
                Log::warning('No JSON found in AI response', [
                    'response_preview' => substr($responseContent, 0, 200),
                    'topic_id' => $topic->id,
                ]);

                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'AI did not return properly formatted questions']
                    ]
                ];
            }

            $jsonString = $jsonMatch[0];
            $data = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Invalid JSON in AI response', [
                    'json_string' => $jsonString,
                    'error' => json_last_error_msg(),
                    'topic_id' => $topic->id,
                ]);

                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'AI returned invalid JSON: ' . json_last_error_msg()]
                    ]
                ];
            }

            if (!isset($data['questions']) || !is_array($data['questions']) || empty($data['questions'])) {
                Log::warning('No questions array in AI response', [
                    'data_keys' => array_keys($data),
                    'topic_id' => $topic->id,
                ]);

                return [
                    'multiple_choice' => [],
                    'true_false' => [],
                    'essay' => [],
                    'errors' => [
                        ['row' => 0, 'message' => 'AI did not return any questions from the document content']
                    ]
                ];
            }

            // Process the extracted questions
            $importResults = [
                'multiple_choice' => [],
                'true_false' => [],
                'essay' => [],
                'errors' => [],
                'preview' => [] // Store preview data
            ];

            foreach ($data['questions'] as $index => $questionData) {
                try {
                    $questionType = $questionData['type'] ?? 'multiple_choice';
                    $questionText = $questionData['question'] ?? '';
                    
                    if (empty($questionText)) {
                        continue; // Skip empty questions entirely
                    }

                    // Create Mark object for the question text
                    $questionMark = new Mark($questionText, $questionText);

                    $commonData = [
                        'question' => $questionMark, // Use the Mark object
                        'difficulty_level' => $questionData['difficulty_level'] ?? 'medium',
                        'score' => $questionData['score'] ?? 1,
                        'academic_topic_id' => $topic->id,
                        'academic_subtopic_id' => $subtopic?->id,
                        'added_by' => $userId,
                        'modified_by' => $userId,
                    ];

                    // Prepare preview data
                    $previewItem = [
                        'id' => 'preview_' . ($index + 1),
                        'row_number' => $index + 1,
                        'question' => $questionText,
                        'type' => $questionType,
                        'difficulty_level' => $questionData['difficulty_level'] ?? 'medium',
                        'score' => $questionData['score'] ?? 1,
                    ];

                    // Validate that the question text is meaningful (not just whitespace)
                    if (empty(trim($questionText))) {
                        continue; // Skip questions with only whitespace
                    }

                    switch (strtolower($questionType)) {
                        case 'multiple_choice':
                            $opt_a = $questionData['options'][0] ?? '';
                            $opt_b = $questionData['options'][1] ?? '';
                            $opt_c = $questionData['options'][2] ?? '';
                            $opt_d = $questionData['options'][3] ?? '';
                            $opt_e = $questionData['options'][4] ?? '';
                            
                            // Find HTML versions from original document
                            $opt_a_html = $this->findHtmlForText($opt_a, $htmlText);
                            $opt_b_html = $this->findHtmlForText($opt_b, $htmlText);
                            $opt_c_html = $this->findHtmlForText($opt_c, $htmlText);
                            $opt_d_html = $this->findHtmlForText($opt_d, $htmlText);
                            $opt_e_html = $this->findHtmlForText($opt_e, $htmlText);
                            
                            $commonData['option_a'] = new Mark($opt_a, $opt_a_html);
                            $commonData['option_b'] = new Mark($opt_b, $opt_b_html);
                            $commonData['option_c'] = new Mark($opt_c, $opt_c_html);
                            $commonData['option_d'] = new Mark($opt_d, $opt_d_html);
                            $commonData['option_e'] = new Mark($opt_e, $opt_e_html);
                            $commonData['answer'] = $questionData['answer'] ?? '';

                            $previewItem['option_a'] = $opt_a;
                            $previewItem['option_b'] = $opt_b;
                            $previewItem['option_c'] = $opt_c;
                            $previewItem['option_d'] = $opt_d;
                            $previewItem['option_e'] = $opt_e;
                            $previewItem['option_a_html'] = $opt_a_html;
                            $previewItem['option_b_html'] = $opt_b_html;
                            $previewItem['option_c_html'] = $opt_c_html;
                            $previewItem['option_d_html'] = $opt_d_html;
                            $previewItem['option_e_html'] = $opt_e_html;
                            $previewItem['answer'] = $commonData['answer'];

                            // Validate that multiple choice question has meaningful content
                            if (empty(trim($questionText)) || 
                                (empty(trim($opt_a)) || empty(trim($opt_b)))) {
                                break; // Skip invalid multiple choice questions
                            }

                            if ($saveToDb) {
                                $question = MultipleChoiceQuestion::create($commonData);
                                $importResults['multiple_choice'][] = $question->id;
                                $this->importedCount['multiple_choice']++;
                            }
                            break;

                        case 'true_false':
                            $answer = $questionData['answer'];
                            if (is_bool($answer)) {
                                $commonData['answer'] = $answer;
                            } else {
                                $answer = strtolower(trim($answer));
                                $commonData['answer'] = ($answer === 'true' || $answer === '1' || $answer === 'yes');
                            }

                            $previewItem['answer'] = $commonData['answer'];

                            // Validate that true/false question has meaningful content
                            if (empty(trim($questionText))) {
                                break; // Skip invalid true/false questions
                            }

                            if ($saveToDb) {
                                $question = TrueOrFalseQuestion::create($commonData);
                                $importResults['true_false'][] = $question->id;
                                $this->importedCount['true_false']++;
                            }
                            break;

                        case 'essay':
                        case 'short_answer':
                            $commonData['answer'] = $questionData['answer'] ?? '';
                            $previewItem['answer'] = $commonData['answer'];

                            // Validate that essay question has meaningful content
                            if (empty(trim($questionText))) {
                                break; // Skip invalid essay questions
                            }

                            if ($saveToDb) {
                                $question = EssayQuestion::create($commonData);
                                $importResults['essay'][] = $question->id;
                                $this->importedCount['essay']++;
                            }
                            break;

                        default:
                            // For unknown question types, skip them
                            break; // Continue the foreach loop
                    }

                    // Add to preview data regardless of save flag
                    $importResults['preview'][] = $previewItem;
                } catch (\Exception $e) {
                    Log::error('Error processing question from AI response', [
                        'error' => $e->getMessage(),
                        'question_data' => $questionData,
                        'topic_id' => $topic->id,
                    ]);
                    continue ; // Continue to next question
                }
            }

            // If no questions were processed despite AI returning data, log a warning
            if ($this->importedCount['multiple_choice'] + $this->importedCount['true_false'] + $this->importedCount['essay'] === 0) {
                Log::info('AI returned questions but none were processed due to validation', [
                    'original_questions_count' => count($data['questions']),
                    'topic_id' => $topic->id,
                    'subtopic_id' => $subtopic?->id,
                ]);
            }

            return $importResults;
        } catch (\Exception $e) {
            Log::error('Error processing questions with AI', [
                'error' => $e->getMessage(),
                'topic_id' => $topic->id,
                'subtopic_id' => $subtopic?->id,
            ]);

            return [
                'multiple_choice' => [],
                'true_false' => [],
                'essay' => [],
                'errors' => [
                    ['row' => 0, 'message' => 'Error processing questions with AI: ' . $e->getMessage()]
                ]
            ];
        }
    }

    /**
     * Helper method to get a column value by index
     */
    private function getColumnValue(array $row, ?int $index, $default = null)
    {
        if ($index === null || !isset($row[$index])) {
            return $default;
        }
        
        $value = trim($row[$index]);
        return $value === '' ? $default : $value;
    }

    /**
     * Helper method to find column index by possible header names
     */
    private function findColumnIndex(array $headers, array $possibleNames): ?int
    {
        foreach ($possibleNames as $name) {
            $index = array_search(strtolower($name), $headers);
            if ($index !== false) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Reset the imported count
     */
    private function resetImportedCount(): void
    {
        $this->importedCount = [
            'multiple_choice' => 0,
            'true_false' => 0,
            'essay' => 0,
        ];
    }

    /**
     * Get the import statistics
     */
    public function getImportStatistics(): array
    {
        return $this->importedCount;
    }

    /**
     * Find HTML formatted version of plain text in the HTML content
     */
    private function findHtmlForText(string $plainText, string $htmlText): string
    {
        if (empty($plainText)) {
            return '';
        }

        // Remove extra whitespace from plain text
        $cleanPlain = trim(preg_replace('/\s+/', ' ', $plainText));
        
        // Try to find the text in the HTML, accounting for HTML tags
        $htmlLines = explode("\n", $htmlText);
        
        foreach ($htmlLines as $line) {
            $cleanLine = trim(preg_replace('/\s+/', ' ', strip_tags($line)));
            
            // Check if this line contains the text we're looking for
            if (stripos($cleanLine, $cleanPlain) !== false) {
                return trim($line);
            }
        }
        
        // If not found, return plain text
        return htmlspecialchars($plainText);
    }
}