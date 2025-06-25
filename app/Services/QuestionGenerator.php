<?php

namespace App\Services;

use App\Exceptions\NotEnoughQuestionsException;
use App\Exceptions\NoTopicsException;
use App\Models\AcademicSubject;
use App\Models\EssayQuestion;
use App\Models\Examination;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;
use App\Templates\TemplateRenderer;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Imagick;
use PhpOffice\PhpWord\IOFactory;
use RuntimeException;

class QuestionGenerator
{
    /**
     * Convert sections with question objects to sections with question IDs only
     * This normalizes the data structure to always use IDs
     */
    public function normalizeQuestionsToIds(array $sections): array
    {
        foreach ($sections as &$section) {
            if (isset($section['questions']) && is_array($section['questions'])) {
                $section['questions'] = $this->extractQuestionIds($section['questions']);
            }
        }

        return $sections;
    }

    /**
     * Extract question IDs from a mixed array of IDs and objects
     * Returns an array of question IDs only
     */
    public function getQuestionIdsOnly(array $questions): array
    {
        return $this->extractQuestionIds($questions);
    }

    /**
     * Extract question IDs from questions array, handling both ID arrays and object arrays
     * This ensures we always work with IDs going forward
     */
    private function extractQuestionIds(array $questions): array
    {
        $questionIds = [];

        foreach ($questions as $question) {
            if (is_numeric($question)) {
                // It's already an ID
                $questionIds[] = (int)$question;
            } elseif (is_array($question) && isset($question['id'])) {
                // It's an array with an ID
                $questionIds[] = (int)$question['id'];
            } elseif (is_object($question) && isset($question->id)) {
                // It's a model object with an ID
                $questionIds[] = (int)$question->id;
            } elseif (is_object($question) && method_exists($question, 'getKey')) {
                // It's an Eloquent model
                $questionIds[] = (int)$question->getKey();
            }
        }

        return array_unique(array_filter($questionIds));
    }

    public static function generate(
        array $heading,
        array $sections,
        array $metadata = []
    ): array
    {
        // Ensure file uploads are handled first
        $sections = static::preprocessSections($sections);

        $usedQuestions = [
            'multiple_choice_questions' => [],
            'true_or_false_questions' => [],
            'essay_questions' => []
        ];

        collect($sections)->each(/**
         * @throws NoTopicsException
         * @throws NotEnoughQuestionsException
         */ function ($section, $index) use (
            &$sections,
            &$usedQuestions
        ) {
            $table = $section['type'];
            if (empty($section['topics']) || !is_array($section['topics'])) {
                throw new NoTopicsException();
            }
            $topicIds = collect($section['topics'])->map(fn($id) => (int)$id)->all();
            $subtopics = $section['subtopics'] ?? [];
            $sectionQuestions = [];


            // Process document if present (a file should already be stored as a path)
            if (isset($section['document'])) {
                $sections[$index] = (new QuestionGenerator())->processDocument($section, $sections[$index]);
            }

            if (!empty($subtopics)) {
                // Handle questions with subtopics
                collect($subtopics)->each(/**
                 * @throws NotEnoughQuestionsException
                 */ function ($subtopic) use ($table, &$sectionQuestions, &$usedQuestions) {
                    $count = (int)$subtopic['count'];

                    $questions = DB::table($table)
                        ->join('academic_subtopics', $table . '.academic_subtopic_id', '=', 'academic_subtopics.id')
                        ->where('academic_subtopics.academic_topic_id', $subtopic['topic_id']?? $subtopic['academic_topic_id'])
                        ->where('academic_subtopics.id', $subtopic['id'])
                        ->whereNotIn($table . '.id', $usedQuestions[$table])
                        ->inRandomOrder()
                        ->take($count)
                        ->pluck('' . $table . '.id')
                        ->all();

                    if (count($questions) < $count) {
                        throw new NotEnoughQuestionsException();
                    }

                    $sectionQuestions = array_merge($sectionQuestions, $questions);
                });
            } else {
                // Handle questions without subtopics (topic-level questions)
                $questions = DB::table($table)
                    ->whereIn('academic_topic_id', $topicIds)
                    ->whereNull('academic_subtopic_id')
                    ->whereNotIn('id', $usedQuestions[$table])
                    ->inRandomOrder()
                    ->take($section['count'])
                    ->pluck('' . $table . '.id')
                    ->all();

                if (count($questions) < $section['count']) {
                    throw new NotEnoughQuestionsException();
                }

                $sectionQuestions = $questions;
            }

            // Add questions to the tracking array for duplicate prevention
            $usedQuestions[$table] = array_merge($usedQuestions[$table], $sectionQuestions);

            // Build section
            $sections[] = [
                'name' => $section['name'],
                'type' => $table,
                'questions' => $sectionQuestions,
                'page' => $section['page'] ?? null,
                'document' => $section['document'] ?? null,
                'pdf_images' => $section['pdf_images'] ?? [],
                'extension' => $section['extension'] ?? null,
                'original_path' => $section['original_path'] ?? null,
                'instructions' => $section['instructions'],
            ];
        });
        $sections = array_slice($sections, 1);

        if (($heading['instructions']['up'] !== null) && isset($heading['template']) && $heading['template'] === 'twig') {
            $heading['up'] = TemplateRenderer::renderTwig($heading['instructions']['up'], $heading['duration'], $heading['title'], $metadata);
        }

        if ($heading['instructions']['down'] !== null) {
            if (isset($heading['template']) && $heading['template'] === 'twig') {
                $heading['down'] = TemplateRenderer::renderTwig($heading['instructions']['down'], $heading['duration'], $heading['title'], $metadata);
            }
            if (isset($heading['template']) && $heading['template'] === 'pug') {
                $heading['down'] = TemplateRenderer::renderPug($heading['instructions']['down'], $heading['duration'], $heading['title'], $metadata);
            }
        }

        return [
            'title' => $heading['title'],
            'heading' => $heading,
            'sections' => $sections,
        ];

    }

    /**
     * Preprocess sections to handle file uploads before any serialization
     */
    public static function preprocessSections(array $sections): array
    {
        return collect($sections)->map(function ($section) {
            if (isset($section['document']) && $section['document'] instanceof UploadedFile) {
                $fileInfo = (new static())->storeUploadedFile($section['document']);
                $section = array_merge($section, $fileInfo);
            }
            return $section;
        })->toArray();
    }

    private function processFileContent(string $filePath, string $storedPath, string $extension): array
    {
        $result = [
            'extension' => $extension,
            'original_path' => $storedPath,
            'document' => $storedPath,
            'pdf_images' => [],
        ];

        if (!file_exists($filePath)) {
            Log::warning('File does not exist for processing', ['path' => $filePath]);
            return $result;
        }

        switch ($extension) {
            case 'pdf':
                $result['pdf_images'] = $this->generatePdfImages($filePath, $storedPath);
                break;

            case 'doc':
            case 'docx':
                $result['document'] = $this->extractDocxText($filePath);
                break;

            case 'txt':
                $result['document'] = file_get_contents($filePath);
                break;

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
                // Images don't need additional processing, just the path
                $result['document'] = $storedPath;
                break;

            default:
                Log::warning('Unsupported file type', [
                    'extension' => $extension,
                    'path' => $filePath
                ]);
        }

        return $result;
    }

    private function storeUploadedFile(UploadedFile $file): array
    {
        $timestampedName = time() . '_' . $file->getClientOriginalName();

        try {
            // First, ensure the directory exists
            $documentsPath = storage_path('app/public/documents');
            if (!file_exists($documentsPath) && !mkdir($documentsPath, 0755, true) && !is_dir($documentsPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $documentsPath));
            }

            // Try the standard store method first
            $storedPath = $file->store('documents', 'public');

            if (!$storedPath) {
                // If that fails, try storeAs with a clean filename
                $cleanFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $timestampedName);
                $storedPath = $file->storeAs('documents', $cleanFilename, 'public');
            }

            if (!$storedPath) {
                // Last resort: manual file move
                $storedPath = 'documents/' . $timestampedName;
                $fullStoragePath = storage_path('app/public/' . $storedPath);

                if (!move_uploaded_file($file->getRealPath(), $fullStoragePath)) {
                    throw new RuntimeException('Failed to store file manually');
                }
            }

            // Verify the file exists
            if (!file_exists(storage_path('app/public/' . $storedPath))) {
                throw new RuntimeException('File was not stored correctly');
            }

        } catch (Exception $e) {
            Log::error('File storage failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'temp_path' => $file->getRealPath(),
                'storage_path' => storage_path('app/public/documents'),
                'storage_writable' => is_writable(storage_path('app/public')),
            ]);

            throw new RuntimeException('Failed to store uploaded file: ' . $e->getMessage());
        }

        $fullPath = storage_path('app/public/' . $storedPath);
        $extension = strtolower($file->getClientOriginalExtension());

        // Use the shared processing method
        return $this->processFileContent($fullPath, $storedPath, $extension);
    }

    private function processDocument(array $section, array $processedSection): array
    {
        $storedPath = $section['document'];
        $fullPath = storage_path('app/public/' . $storedPath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Use the shared processing method and merge with existing processed section
        $fileResult = $this->processFileContent($fullPath, $storedPath, $extension);

        return array_merge($processedSection, $fileResult);
    }

    private function generatePdfImages(string $pdfPath, string $originalPath): array
    {
        $outputDir = storage_path('app/public/pdf_pages');
        $images = [];

        if (!file_exists($outputDir) && !mkdir($outputDir, 0755, true) && !is_dir($outputDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $outputDir));
        }

        try {
            if (!extension_loaded('imagick')) {
                throw new RuntimeException('Imagick extension not available');
            }

            $imagick = new Imagick();
            $imagick->setResolution(300, 300);
            $imagick->readImage($pdfPath);

            foreach ($imagick as $i => $page) {
                $page->setImageFormat('jpg');
                $page->setImageCompression(Imagick::COMPRESSION_JPEG);
                $page->setImageCompressionQuality(90);

                $pdfBaseName = pathinfo($originalPath, PATHINFO_FILENAME);
                $filename = sprintf('pdf_page_%s_%s.jpg', $pdfBaseName, $i);
                $outputPath = $outputDir . '/' . $filename;

                $page->writeImage($outputPath);
                $images[] = 'pdf_pages/' . $filename;
            }

        } catch (Exception $e) {
            Log::error('PDF processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return ['pdf_error' => 'Failed to process PDF: ' . $e->getMessage()];
        } finally {
            if (isset($imagick)) {
                $imagick->clear();
                $imagick->destroy();
            }
        }

        return $images;
    }

    /**
     * Extract text content from DOCX files
     */
    private function extractDocxText(string $filePath): string
    {
        try {
            if (!class_exists(IOFactory::class)) {
                throw new RuntimeException('PhpWord library not available');
            }

            $phpWord = IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }
            return trim($text);
        } catch (Exception $e) {
            Log::error('DOCX processing failed', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);

            return 'Error processing document: ' . $e->getMessage();
        }
    }


    /**
     * @throws Exception
     */
    public function createExamination(
        AcademicSubject $academicSubject,
        array           $validatedData,
                        $team_id,
                        $creator_id
    ): Examination
    {
        $heading = $validatedData['heading'];

        $metadata = $validatedData['metadata'];

        $heading['instructions'] = $heading['instructions']['down'] ?? null;

        try {
            $examination = Examination::create([
                'heading' => $validatedData['heading'],
                'sections' => $this->processSections($validatedData['sections']),
                'metadata' => $metadata,
                'duration' => $heading['duration'],
                'instructions' => $heading['instructions'],
                'title' => $validatedData['title'],
                'academic_subject_id' => $academicSubject->id,
                'creator_id' => $creator_id,
                'team_id' => $team_id,

            ]);

            $examination->creator()->associate($creator_id);
            $examination->team()->associate($team_id);

            $academicSubject->examinations()->save($examination);

            return $examination;

        } catch (Exception $e) {
            Log::error('Examination creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'academic_subject_id' => $academicSubject->id,
                'team_id' => $team_id,
                'creator_id' => $creator_id,
            ]);

            throw $e;
        }
    }

    public function processSections(mixed $sections): mixed
    {
        foreach ($sections as $index => $section) {
            if (!empty($section['questions'])) {
                $sections[$index]['questions'] = $this->processQuestions($section);
            }

            if (isset($section['document'])) {
                $sections[$index] = $this->processDocument($section, $sections[$index]);
            }
        }
        return $sections;
    }

    /**
     * Process questions in a section - now simplified to only extract IDs and fetch complete questions
     */
    private function processQuestions(array $section): array
    {
        if (empty($section['questions'])) {
            return [];
        }

        // Extract question IDs (supports both ID arrays and object arrays for backward compatibility)
        $questionIds = $this->extractQuestionIds($section['questions']);

        if (empty($questionIds)) {
            return [];
        }
        return $questionIds;

    }

    /**
     * Fetch complete question objects based on question type and IDs
     * This is a public method that can be used anywhere to get formatted question data
     */
    public function fetchCompleteQuestions(array $questionIds, string $questionType): array
    {
        if (empty($questionIds)) {
            return [];
        }

        $questions = collect();

        try {
            switch (strtolower($questionType)) {
                case 'essay':
                    $questions = \App\Models\EssayQuestion::whereIn('id', $questionIds)
                        ->with(['subtopic.academicTopic'])
                        ->get();
                    break;

                case 'multiple_choice':
                    $questions = \App\Models\MultipleChoiceQuestion::whereIn('id', $questionIds)
                        ->with(['subtopic.academicTopic'])
                        ->get();
                    break;

                case 'true_or_false':
                    $questions = \App\Models\TrueOrFalseQuestion::whereIn('id', $questionIds)
                        ->with(['subtopic.academicTopic'])
                        ->get();
                    break;

                default:
                    Log::warning('Unknown question type', [
                        'type' => $questionType,
                        'ids' => $questionIds
                    ]);
                    return [];
            }

            // Format questions for consistent output
            return $questions->map(function ($question) use ($questionType) {
                return $this->formatQuestionForOutput($question, $questionType);
            })->toArray();

        } catch (Exception $e) {
            Log::error('Failed to fetch questions', [
                'type' => $questionType,
                'ids' => $questionIds,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }


    /**
     * Format question object for consistent output structure
     */
    private function formatQuestionForOutput($question, string $questionType): array
    {
        $baseFormat = [
            'id' => $question->id,
            'question' => $question->question,
            'score' => $question->score ?? 0,
            'difficulty_level' => $question->difficulty_level ?? 'medium',
            'type' => $questionType,
            'subtopic_id' => $question->academic_subtopic_id ?? null,
            'subtopic_name' => $question->subtopic->name ?? null,
            'topic_name' => $question->subtopic->academicTopic->name ?? null,
        ];

        // Add type-specific fields
        switch (strtolower($questionType)) {
            case 'essay':
                $baseFormat['answer'] = $question->answer ?? '';
                break;

            case 'multiple_choice':
                $baseFormat['options'] = [
                    'a' => $question->option_a ?? '',
                    'b' => $question->option_b ?? '',
                    'c' => $question->option_c ?? '',
                    'd' => $question->option_d ?? '',
                    'e' => $question->option_e ?? '',
                ];
                $baseFormat['answer'] = $question->answer ?? '';
                break;

            case 'true_or_false':
                $baseFormat['answer'] = $question->answer ?? false;
                break;
        }

        return $baseFormat;
    }

    private function convertQuestionToArray($question): array
    {
        $questionArray = $question->toArray();
        $markFields = $this->getMarkFieldsForQuestion($question);

        foreach ($markFields as $field) {
            if ($question->$field instanceof Mark) {
                $questionArray[$field] = $question->$field->toArray();
            }
        }

        return $questionArray;
    }

    private function getMarkFieldsForQuestion($question): array
    {
        if ($question instanceof MultipleChoiceQuestion) {
            return ['question', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e'];
        }

        return ['question', 'answer'];
    }

    public function formatExistingQuestions(array $questions): array
    {
        return collect($questions)->map(function ($question) {
            if (!is_array($question)) {
                return $question;
            }

            // Handle individual option fields (option_a, option_b, etc.)
            foreach (['question', 'answer', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $key) {
                if (!isset($question[$key])) {
                    continue;
                }

                $value = $question[$key];
                if ($value instanceof Mark) {
                    $question[$key] = $value->toArray();
                } elseif (is_string($value) && $this->isJsonString($value)) {
                    $question[$key] = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                }
            }

            // Handle options array for multiple choice questions
            if (isset($question['options']) && is_array($question['options'])) {
                $question['options'] = collect($question['options'])->map(function ($option, $key) {
                    if ($option instanceof Mark) {
                        return $option->down;
                    }

                    if (is_array($option) && isset($option['up'])) {
                        return $option['up'];
                    }

                    if (is_string($option) && $this->isJsonString($option)) {
                        $decoded = json_decode($option, true, 512, JSON_THROW_ON_ERROR);
                        return $decoded['up'] ?? $option;
                    }
                    return $option;
                })->toArray();
            }

            return $question;
        })->toArray();
    }

    /**
     * Helper method to check if a string is valid JSON
     */
    private function isJsonString($string): bool
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE);
    }
}
