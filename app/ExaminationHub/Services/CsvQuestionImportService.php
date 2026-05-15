<?php

namespace App\Examinations\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Imports questions from a CSV file into the normalised question array
 * format used by ExamQuestionPersistenceService.
 *
 * Supported CSV formats
 * ─────────────────────
 * multiple_choice:
 *   question, option_a, option_b, option_c, option_d, correct_answer, points, difficulty_level
 *
 * true_false:
 *   question, correct_answer, points, difficulty_level
 *
 * essay / short_answer:
 *   question, answer, points, difficulty_level
 */
class CsvQuestionImportService
{
    /** Maximum rows processed in a single import (protects against huge uploads). */
    private const MAX_ROWS = 500;

    private array $errors  = [];
    private array $results = [];

    // ─── Public API ───────────────────────────────────────────────────────────

    /**
     * Import questions from an uploaded CSV file.
     *
     * @return array{questions: array, errors: array, imported: int, skipped: int}
     */
    public function importFromCsv(UploadedFile $file, string $questionType): array
    {
        $this->errors  = [];
        $this->results = [];

        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return $this->response([], [['row' => 0, 'message' => 'Could not open the uploaded file.']]);
        }

        $rawHeaders = fgetcsv($handle);
        if ($rawHeaders === false || $rawHeaders === null) {
            fclose($handle);
            return $this->response([], [['row' => 0, 'message' => 'CSV file appears to be empty.']]);
        }

        $headers = array_map(fn ($h) => strtolower(trim($h)), $rawHeaders);

        if (!$this->validateCsvStructure($headers, $questionType)) {
            fclose($handle);
            return $this->response([], $this->errors);
        }

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($rowNumber > self::MAX_ROWS + 1) {
                $this->errors[] = [
                    'row'     => $rowNumber,
                    'message' => 'Import stopped: maximum of ' . self::MAX_ROWS . ' rows allowed.',
                ];
                break;
            }

            // Skip blank rows silently
            if (empty(array_filter($row))) {
                continue;
            }

            $mapped = array_combine($headers, array_pad($row, count($headers), ''));

            try {
                $question = $this->parseQuestionRow($mapped, $questionType);
                $this->results[] = $question;
            } catch (\InvalidArgumentException $e) {
                $this->errors[] = [
                    'row'     => $rowNumber,
                    'message' => $e->getMessage(),
                ];
            }
        }

        fclose($handle);

        return $this->response($this->results, $this->errors);
    }

    /**
     * Validate that all required columns are present for the given question type.
     */
    public function validateCsvStructure(array $headers, string $questionType): bool
    {
        $required = $this->getRequiredColumns($questionType);
        $missing  = array_diff($required, $headers);

        if (!empty($missing)) {
            $this->errors[] = [
                'row'     => 1,
                'message' => 'Missing required columns: ' . implode(', ', $missing)
                           . '. Expected: ' . implode(', ', $required),
            ];
            return false;
        }

        return true;
    }

    /**
     * Parse a single mapped CSV row into a normalised question array.
     *
     * @throws \InvalidArgumentException on validation failure
     */
    public function parseQuestionRow(array $row, string $questionType): array
    {
        $questionText = trim($row['question'] ?? '');
        if ($questionText === '') {
            throw new \InvalidArgumentException("Question text is empty.");
        }

        $points    = $this->parsePoints($row['points'] ?? '1');
        $difficulty = $this->parseDifficulty($row['difficulty_level'] ?? 'medium');

        return match ($questionType) {
            'multiple_choice' => $this->parseMultipleChoice($row, $questionText, $points, $difficulty),
            'true_false'      => $this->parseTrueFalse($row, $questionText, $points, $difficulty),
            'essay',
            'short_answer'    => $this->parseEssay($row, $questionText, $points, $difficulty),
            default           => throw new \InvalidArgumentException("Unknown question type: {$questionType}"),
        };
    }

    /**
     * Return the required column names for a given question type.
     */
    public function getRequiredColumns(string $questionType): array
    {
        return match ($questionType) {
            'multiple_choice' => ['question', 'option_a', 'option_b', 'correct_answer'],
            'true_false'      => ['question', 'correct_answer'],
            'essay',
            'short_answer'    => ['question'],
            default           => throw new \InvalidArgumentException("Unknown question type: {$questionType}"),
        };
    }

    // ─── Type-specific parsers ────────────────────────────────────────────────

    private function parseMultipleChoice(array $row, string $question, float $points, string $difficulty): array
    {
        $options = array_values(array_filter([
            trim($row['option_a'] ?? ''),
            trim($row['option_b'] ?? ''),
            trim($row['option_c'] ?? ''),
            trim($row['option_d'] ?? ''),
        ]));

        if (count($options) < 2) {
            throw new \InvalidArgumentException("Multiple choice question requires at least option_a and option_b.");
        }

        $correctRaw = strtoupper(trim($row['correct_answer'] ?? ''));

        // Accept either 'A', 'B', 'C', 'D' or the actual answer text
        $answerMap  = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
        $answerIndex = $answerMap[$correctRaw] ?? null;

        if ($answerIndex === null) {
            // Try matching the answer text directly against options
            $match = array_search(strtolower($correctRaw), array_map('strtolower', $options));
            if ($match === false) {
                throw new \InvalidArgumentException(
                    "correct_answer '{$correctRaw}' is not a valid option letter (A–D) or matching option text."
                );
            }
            $answerIndex = $match;
        }

        if (!isset($options[$answerIndex])) {
            throw new \InvalidArgumentException(
                "correct_answer '{$correctRaw}' refers to an option that does not exist."
            );
        }

        return [
            'type'             => 'multiple_choice',
            'question'         => $question,
            'options'          => $options,
            'answer'           => $correctRaw,        // A / B / C / D
            'answer_text'      => $options[$answerIndex],
            'points'           => $points,
            'difficulty_level' => $difficulty,
        ];
    }

    private function parseTrueFalse(array $row, string $question, float $points, string $difficulty): array
    {
        $raw    = strtolower(trim($row['correct_answer'] ?? ''));
        $boolMap = ['true' => true, '1' => true, 'yes' => true, 'false' => false, '0' => false, 'no' => false];

        if (!array_key_exists($raw, $boolMap)) {
            throw new \InvalidArgumentException(
                "correct_answer for true/false must be 'true' or 'false', got '{$raw}'."
            );
        }

        $answer = $boolMap[$raw];

        return [
            'type'             => 'true_false',
            'question'         => $question,
            'options'          => ['True', 'False'],
            'answer'           => $answer ? 'True' : 'False',
            'points'           => $points,
            'difficulty_level' => $difficulty,
        ];
    }

    private function parseEssay(array $row, string $question, float $points, string $difficulty): array
    {
        return [
            'type'             => 'essay',
            'question'         => $question,
            'answer'           => trim($row['answer'] ?? ''),
            'points'           => $points,
            'difficulty_level' => $difficulty,
        ];
    }

    // ─── Value parsers ────────────────────────────────────────────────────────

    private function parsePoints(string $raw): float
    {
        $val = filter_var(trim($raw), FILTER_VALIDATE_FLOAT);
        if ($val === false || $val <= 0) {
            return 1.0;
        }
        return round($val, 2);
    }

    private function parseDifficulty(string $raw): string
    {
        $clean = strtolower(trim($raw));
        return in_array($clean, ['easy', 'medium', 'hard'], true) ? $clean : 'medium';
    }

    // ─── Response builder ─────────────────────────────────────────────────────

    private function response(array $questions, array $errors): array
    {
        $skipped = count($errors);

        Log::info('CsvQuestionImportService: import complete', [
            'imported' => count($questions),
            'skipped'  => $skipped,
        ]);

        return [
            'questions' => $questions,
            'errors'    => $errors,
            'imported'  => count($questions),
            'skipped'   => $skipped,
        ];
    }
}
