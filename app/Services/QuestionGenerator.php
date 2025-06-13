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
            if (empty($section['topics']) || $section['topics'][0] === 0) {
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
                        ->where('academic_subtopics.academic_topic_id', $subtopic['topic_id'])
                        ->where('academic_subtopics.id', $subtopic['id'])
                        ->whereNotIn($table . '.id', $usedQuestions[$table])
                        ->inRandomOrder()
                        ->take($count)
                        ->get()
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
                    ->get()
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

                // Merge file information into the section
                $section = array_merge($section, $fileInfo);

                // Remove the original UploadedFile object
                unset($section['document']);

                // Set document content if it was extracted (for txt/docx files)
                if (isset($fileInfo['document'])) {
                    $section['document'] = $fileInfo['document'];
                }
            }
            return $section;
        })->toArray();
    }

    /**
     * Store uploaded file and return the path
     */
    private function storeUploadedFile(UploadedFile $file): array
    {
        // Store the file
        $storedPath = $file->store('documents', 'public');
        $fullPath = storage_path('app/public/' . $storedPath);

        // Get file extension
        $extension = strtolower($file->getClientOriginalExtension());

        // Initialize a result array with basic info
        $result = [
            'original_path' => $storedPath,
            'extension' => $extension,
            'original_name' => $file->getClientOriginalName(),
        ];

        // Process different file types
        switch ($extension) {
            case 'pdf':
                $result['pdf_images'] = $this->generatePdfImages($fullPath, $storedPath);
                break;

            case 'docx':
                $result['document'] = $this->extractDocxText($fullPath);
                break;

            case 'txt':
                $result['document'] = file_get_contents($fullPath);
                break;

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
                // Images don't need additional processing, just the path
                break;

            default:
                Log::warning('Unsupported file type uploaded', [
                    'extension' => $extension,
                    'filename' => $file->getClientOriginalName()
                ]);
        }

        return $result;
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

    private function processDocument(array $section, array $processedSection): array
    {
        $path = storage_path('app/public/' . $section['document']);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $processedSection['extension'] = $ext;
        $processedSection['original_path'] = $section['document'];
        $processedSection['pdf_images'] = [];

        if (in_array($ext, ['doc', 'docx']) && file_exists($path)) {
            $processedSection['document'] = $this->extractDocxText($path);
        }

        if (file_exists($path)) {
            $processedSection['pdf_images'] = $this->generatePdfImages($path, $section['document']);
        }

        return $processedSection;
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

    private function processQuestions(array $section): array
    {
        $firstQuestion = $section['questions'][0] ?? null;

        if ($firstQuestion !== null && !is_array($firstQuestion)) {
            return $this->fetchCompleteQuestions($section['questions'], $section['type']);
        }

        return $this->formatExistingQuestions($section['questions']);
    }

    private function fetchCompleteQuestions(array $questionIds, string $sectionType): array
    {
        $modelMap = [
            'true_or_false_questions' => TrueOrFalseQuestion::class,
            'multiple_choice_questions' => MultipleChoiceQuestion::class,
            'essay_questions' => EssayQuestion::class,
        ];

        $modelClass = $modelMap[$sectionType] ?? null;
        if (!$modelClass) {
            return [];
        }

        \Illuminate\Log\log('modelClass', [$modelClass]);

        // Extract IDs from stdClass objects or use directly if they're already integers
        $ids = collect($questionIds)->map(function ($item) {
            if (is_object($item) && isset($item->id)) {
                return $item->id;
            }
            return $item;
        })->toArray();

        $questions = $modelClass::whereIn('id', $ids)->get();

        return $questions->map(function ($question) {
            return $this->convertQuestionToArray($question);
        })->toArray();
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

    private function formatExistingQuestions(array $questions): array
    {
        return collect($questions)->map(function ($question) {
            if (!is_array($question)) {
                return $question;
            }

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
