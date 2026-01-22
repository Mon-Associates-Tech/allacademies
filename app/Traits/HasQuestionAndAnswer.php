<?php

namespace App\Traits;

use App\Models\MultipleChoiceQuestion;
use App\Support\Mark;
use Illuminate\Database\Eloquent\Model;

trait HasQuestionAndAnswer
{
    /**
     * Extract the best available text from a question field structure
     * Priority order: down, up, html, summary
     */
    public function extractBestText(object|array|string|null $fieldData): ?string
    {
        // Handle null or empty values
        if (empty($fieldData)) {
            return null;
        }

        // Handle string values directly
        if (is_string($fieldData)) {
            return $fieldData;
        }

        // Handle Mark objects specifically
        if ($fieldData instanceof Mark) {
            if (! empty($fieldData->down) && trim($fieldData->down) !== '') {
                return $fieldData->down;
            }
            if (! empty($fieldData->up) && trim($fieldData->up) !== '') {
                return $fieldData->up;
            }
            if (! empty($fieldData->html) && trim($fieldData->html) !== '') {
                return (string) $fieldData->html;
            }
            if (! empty($fieldData->summary) && trim($fieldData->summary) !== '') {
                return $fieldData->summary;
            }
        }

        // Handle other objects
        if (is_object($fieldData)) {
            if (property_exists($fieldData, 'down') && ! empty($fieldData->down) && trim($fieldData->down) !== '') {
                return $fieldData->down;
            }
            if (property_exists($fieldData, 'up') && ! empty($fieldData->up) && trim($fieldData->up) !== '') {
                return $fieldData->up;
            }
            if (property_exists($fieldData, 'html') && ! empty($fieldData->html) && trim($fieldData->html) !== '') {
                return (string) $fieldData->html;
            }
            if (property_exists($fieldData, 'summary') && ! empty($fieldData->summary) && trim($fieldData->summary) !== '') {
                return $fieldData->summary;
            }
        }

        // Handle array structure
        if (is_array($fieldData)) {
            if (! empty($fieldData['down']) && trim($fieldData['down']) !== '') {
                return $fieldData['down'];
            }
            if (! empty($fieldData['up']) && trim($fieldData['up']) !== '') {
                return $fieldData['up'];
            }
            if (! empty($fieldData['html']) && trim($fieldData['html']) !== '') {
                return $fieldData['html'];
            }
            if (! empty($fieldData['summary']) && trim($fieldData['summary']) !== '') {
                return $fieldData['summary'];
            }
        }

        return null;
    }

    /**
     * Process question data to extract clean text values
     */
    public function processQuestionData(object|array $questionData): array
    {
        $processed = [];

        // Extract question text
        if (isset($questionData['question'])) {
            $processed['question'] = $this->extractBestText($questionData['question']);
        }

        // Extract answer text
        if (isset($questionData['answer'])) {
            $processed['answer'] = $this->extractBestText($questionData['answer']);
        }

        // Process options and unify them
        $processed['options'] = $this->unifyOptions($questionData);

        return $processed;
    }

    /**
     * Unify individual option fields into a single options array
     */
    public function unifyOptions(object|array $questionData): array
    {
        $options = [];
        $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];
        $letters = ['A', 'B', 'C', 'D', 'E'];

        foreach ($optionKeys as $index => $key) {
            $optionValue = null;

            if (is_array($questionData) && isset($questionData[$key])) {
                $optionValue = $questionData[$key];
            } elseif (is_object($questionData)) {
                // For Laravel models, check if attribute exists and get its value
                if ($questionData instanceof Model) {
                    if (array_key_exists($key, $questionData->getAttributes()) || $questionData->hasGetMutator($key)) {
                        $optionValue = $questionData->getAttribute($key);
                    }
                } else {
                    // For regular objects, use property_exists
                    if (property_exists($questionData, $key)) {
                        $optionValue = $questionData->$key;
                    }
                }
            }

            if ($optionValue !== null) {
                $optionText = $this->extractBestText($optionValue);
                if (! empty($optionText)) {
                    $options[$letters[$index]] = $optionText;
                }
            }
        }

        return $options;
    }

    /**
     * Process a question model to extract clean data
     */
    public function processQuestionModel(mixed $question): array
    {
        $processed = [];

        // Handle question field
        if ($question instanceof Model) {
            // For Laravel models, check if attribute exists
            if (array_key_exists('question', $question->getAttributes()) || $question->hasGetMutator('question')) {
                $processed['question'] = $this->extractBestText($question->getAttribute('question'));
            }

            // Handle answer field
            if (array_key_exists('answer', $question->getAttributes()) || $question->hasGetMutator('answer')) {
                $processed['answer'] = $this->extractBestText($question->getAttribute('answer'));
            }

            // Handle options for multiple choice questions
            if ($question instanceof MultipleChoiceQuestion) {
                $options = [];
                $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];
                $letters = ['A', 'B', 'C', 'D', 'E'];

                foreach ($optionKeys as $index => $key) {
                    if (array_key_exists($key, $question->getAttributes()) || $question->hasGetMutator($key)) {
                        $optionText = $this->extractBestText($question->getAttribute($key));
                        if (! empty($optionText)) {
                            $options[$letters[$index]] = $optionText;
                        }
                    }
                }

                $processed['options'] = $options;
            }
        } else {
            // For non-Laravel models, use property_exists
            if (property_exists($question, 'question')) {
                $processed['question'] = $this->extractBestText($question->question);
            }

            if (property_exists($question, 'answer')) {
                $processed['answer'] = $this->extractBestText($question->answer);
            }
        }

        return $processed;
    }

    /**
     * Debug method to inspect the structure of question data
     */
    public function debugQuestionData(mixed $question): array
    {
        $debug = [
            'question_class' => get_class($question),
            'question_data' => [],
            'option_data' => [],
        ];

        if ($question instanceof Model) {
            $debug['model_attributes'] = $question->getAttributes();

            // Debug question field
            if (array_key_exists('question', $question->getAttributes()) || $question->hasGetMutator('question')) {
                $questionValue = $question->getAttribute('question');
                $debug['question_data'] = [
                    'raw' => $questionValue,
                    'type' => gettype($questionValue),
                    'class' => is_object($questionValue) ? get_class($questionValue) : null,
                    'extracted' => $this->extractBestText($questionValue),
                ];

                if ($questionValue instanceof Mark) {
                    $debug['question_data']['mark_properties'] = [
                        'up' => $questionValue->up,
                        'down' => $questionValue->down,
                        'html' => $questionValue->html,
                        'summary' => $questionValue->summary,
                    ];
                }
            }

            // Debug option fields for MCQ
            if ($question instanceof MultipleChoiceQuestion) {
                $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];
                foreach ($optionKeys as $key) {
                    if (array_key_exists($key, $question->getAttributes()) || $question->hasGetMutator($key)) {
                        $optionValue = $question->getAttribute($key);
                        $debug['option_data'][$key] = [
                            'raw' => $optionValue,
                            'type' => gettype($optionValue),
                            'class' => is_object($optionValue) ? get_class($optionValue) : null,
                            'extracted' => $this->extractBestText($optionValue),
                        ];

                        if ($optionValue instanceof Mark) {
                            $debug['option_data'][$key]['mark_properties'] = [
                                'up' => $optionValue->up,
                                'down' => $optionValue->down,
                                'html' => $optionValue->html,
                                'summary' => $optionValue->summary,
                            ];
                        }
                    }
                }
            }
        }

        return $debug;
    }
}
