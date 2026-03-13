<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use ActivityLoggable;
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_SUBMITTED = 'submitted';

    const STATUS_GRADED = 'graded';

    const STATUS_LATE = 'late';

    const STATUS_NOT_SUBMITTED = 'not_submitted';

    protected $fillable = [
        'title',
        'description',
        'type', // 'quiz' or 'examination'
        'academic_subject_id',
        'user_id',
        'duration_in_minutes',
        'starts_at',
        'ends_at',
        'is_randomized', // whether students get different questions
        'status', // 'draft', 'published', 'completed'
        'instructions',
        'total_marks',
        'questions',
        'restrict_navigation',
        'max_tab_switches',
        'auto_submit_on_violation',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_randomized' => 'boolean',
        'questions' => 'array',
        'restrict_navigation' => 'boolean',
        'auto_submit_on_violation' => 'boolean',
    ];

    public function structure()
    {
        return [
            'question' => [
                'up' => '<p>Question text</p>',
                'down' => 'Question text',
                'summary' => 'Question summary',
            ],
            'answer' => [
                'up' => '<p>Answer text</p>',
                'down' => 'Answer text',
                'summary' => 'Answer summary',
            ],
            'option_a' => [
                'up' => '<p>Option A text</p>',
                'down' => 'Option A text',
                'summary' => 'Option A summary',
            ],
            'option_b' => [
                'up' => '<p>Option B text</p>',
                'down' => 'Option B text',
                'summary' => 'Option B summary',
            ],
            'option_c' => [
                'up' => '<p>Option C text</p>',
                'down' => 'Option C text',
                'summary' => 'Option C summary',
            ],
            'option_d' => [
                'up' => '<p>Option D text</p>',
                'down' => 'Option D text',
                'summary' => 'Option D summary',
            ],
            'option_e' => [
                'up' => '<p>Option E text</p>',
                'down' => 'Option E text',
                'summary' => 'Option E summary',
            ],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    // Assignment can be assigned to academic groups
    public function academicGroups(): BelongsToMany
    {
        return $this->belongsToMany(AcademicGroup::class, 'assignment_academic_group');
    }

    // Assignment can be assigned to academic levels
    public function academicLevels(): BelongsToMany
    {
        return $this->belongsToMany(AcademicLevel::class, 'assignment_academic_level');
    }

    // Assignment can be assigned to specific students
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'assignment_student');
    }

    // Assignment can be assigned to student groups
    public function studentGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudentGroup::class, 'assignment_student_group');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(AcademicTopic::class, 'assignment_topic');
    }

    public function subtopics(): BelongsToMany
    {
        return $this->belongsToMany(AcademicSubtopic::class, 'assignment_subtopic');
    }

    public function assignmentSections(): HasMany
    {
        return $this->hasMany(AssignmentSection::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AssignmentNotification::class);
    }

    // Get all students who should receive this assignment
    public function getEligibleStudents()
    {
        $students = collect();

        // From academic groups
        foreach ($this->academicGroups as $group) {
            $students = $students->merge($group->students);
        }

        // From academic levels
        foreach ($this->academicLevels as $level) {
            $students = $students->merge($level->students);
        }

        // From student groups
        foreach ($this->studentGroups as $group) {
            $students = $students->merge($group->students);
        }

        // Directly assigned students
        $students = $students->merge($this->students);

        return $students->unique('id');
    }

    /**
     * Generate questions for this assignment based on the configuration
     * If is_randomized is false, returns fixed questions
     * If is_randomized is true, generates random questions from the pool
     */
    public function generateQuestionsForStudentDeprecated($studentId = null)
    {
        if (! $this->questions) {
            return collect();
        }

        $generatedQuestions = collect();

        foreach ($this->questions as $questionConfig) {
            $type = $questionConfig['type'];
            $count = $questionConfig['count'] ?? 1;
            $difficulty = $questionConfig['difficulty'] ?? 'all';
            $topicIds = $questionConfig['topic_ids'] ?? [];
            $subtopicIds = $questionConfig['subtopic_ids'] ?? [];
            $specificIds = $questionConfig['specific_ids'] ?? [];

            // Debug: Log what we're processing
            \Log::info("Processing question type: {$type}, count: {$count}");

            // If specific question IDs are provided and not randomized, use them
            if (! empty($specificIds) && ! $this->is_randomized) {
                $questions = $this->getQuestionsByTypeAndIds($type, $specificIds);
                $generatedQuestions = $generatedQuestions->merge($questions);

                continue;
            }

            // Generate questions based on criteria
            $query = $this->buildQuestionQuery($type, $difficulty, $topicIds, $subtopicIds);

            // Debug: Log the SQL query
            \Log::info("Query for {$type}: ".$query->toSql(), $query->getBindings());

            if ($this->is_randomized) {
                // For randomized assignments, pick random questions
                $availableQuestions = $query->get();
                if ($availableQuestions->count() >= $count) {
                    $selectedQuestions = $availableQuestions->random($count);
                } else {
                    $selectedQuestions = $availableQuestions;
                }
            } else {
                // For non-randomized assignments, take first N questions consistently
                $selectedQuestions = $query->take($count)->get();
            }

            // Debug: Log what we found
            \Log::info("Found {$selectedQuestions->count()} questions for type {$type}");

            $generatedQuestions = $generatedQuestions->merge($selectedQuestions);
        }

        return $generatedQuestions->map(function ($question) {
            return [
                'type' => $this->getQuestionTypeName($question),
                'model' => $question,
                'points' => $question->score ?? 1,
                'difficulty_level' => $question->difficulty_level ?? 'medium',
            ];
        });
    }

    /**
     * Get preview questions for non-randomized assignments
     */
    public function getPreviewQuestions()
    {
        if ($this->is_randomized) {
            return null; // Cannot preview randomized questions
        }

        return $this->generateQuestionsForStudent();
    }

    /**
     * Get question statistics for this assignment
     */
    public function getQuestionStatistics()
    {
        if (! $this->questions) {
            return [
                'total_questions' => 0,
                'by_type' => [],
                'by_difficulty' => [],
                'estimated_duration' => 0,
            ];
        }

        $stats = [
            'total_questions' => 0,
            'by_type' => [],
            'by_difficulty' => [],
            'estimated_duration' => 0,
        ];

        foreach ($this->questions as $questionConfig) {
            $type = $questionConfig['type'];
            $count = $questionConfig['count'] ?? 1;
            $difficulty = $questionConfig['difficulty'] ?? 'all';

            $stats['total_questions'] += $count;
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + $count;

            // Estimate duration based on question type
            $durationPerQuestion = match ($type) {
                'essay_question' => 5, // 5 minutes per essay
                'multiple_choice_question' => 1, // 1 minute per MCQ
                'true_or_false_question' => 0.5, // 30 seconds per T/F
                default => 1
            };
            $stats['estimated_duration'] += $count * $durationPerQuestion;

            if ($difficulty !== 'all') {
                $stats['by_difficulty'][$difficulty] = ($stats['by_difficulty'][$difficulty] ?? 0) + $count;
            }
        }

        return $stats;
    }

    public function buildQuestionQuery($type, $difficulty, $topicIds = [], $subtopicIds = [])
    {
        $model = match ($type) {
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
            default => throw new \InvalidArgumentException("Unknown question type: {$type}")
        };

        $query = $model::query();

        // Apply difficulty filter
        if ($difficulty !== 'all') {
            $query->where('difficulty_level', $difficulty);
        }

        // Apply topic/subtopic filters
        if (! empty($subtopicIds)) {
            // All question types have academic_subtopic_id
            $query->whereIn('academic_subtopic_id', $subtopicIds);
        } elseif (! empty($topicIds)) {
            // All question types have both direct topic and subtopic relationships
            $query->where(function ($q) use ($topicIds) {
                $q->whereIn('academic_topic_id', $topicIds)
                    ->orWhereHas('subtopic', function ($s) use ($topicIds) {
                        $s->whereIn('academic_topic_id', $topicIds);
                    });
            });
        } else {
            // Filter by assignment's subject if no specific topics/subtopics
            // All question types can be filtered the same way
            $query->where(function ($q) {
                $q->whereHas('academicTopic', function ($t) {
                    $t->where('academic_subject_id', $this->academic_subject_id);
                })->orWhereHas('subtopic.academicTopic', function ($t) {
                    $t->where('academic_subject_id', $this->academic_subject_id);
                });
            });
        }

        return $query;
    }

    public function getQuestionsByTypeAndIds($type, $ids)
    {
        $model = match ($type) {
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
            default => throw new \InvalidArgumentException("Unknown question type: {$type}")
        };

        return $model::whereIn('id', $ids)->get();
    }

    public function getQuestionTypeName($question)
    {
        return match (get_class($question)) {
            MultipleChoiceQuestion::class => 'multiple_choice_question',
            TrueOrFalseQuestion::class => 'true_or_false_question',
            EssayQuestion::class => 'essay_question',
            default => 'unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::STATUS_GRADED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_LATE => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::STATUS_NOT_SUBMITTED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function subject()
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    /**
     * Check if this assignment has embedded questions (book-based)
     * or references to database questions (topic-based)
     */
    public function hasEmbeddedQuestions(): bool
    {
        if (! $this->questions || ! is_array($this->questions)) {
            return false;
        }

        foreach ($this->questions as $questionConfig) {
            // If we find a 'questions' array with full question data, it's embedded
            if (isset($questionConfig['questions']) && is_array($questionConfig['questions'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate questions for this assignment based on the configuration
     * Handles both embedded questions and database-referenced questions
     */
    public function generateQuestionsForStudent($studentId = null)
    {
        if (! $this->questions) {
            return collect();
        }

        // Check if this is a book-based assignment with embedded questions
        if ($this->hasEmbeddedQuestions()) {
            return $this->getEmbeddedQuestions();
        }

        // Otherwise, generate from database questions (existing logic)
        $generatedQuestions = collect();

        foreach ($this->questions as $questionConfig) {
            $type = $questionConfig['type'];
            $count = $questionConfig['count'] ?? 1;
            $difficulty = $questionConfig['difficulty'] ?? 'all';
            $topicIds = $questionConfig['topic_ids'] ?? [];
            $subtopicIds = $questionConfig['subtopic_ids'] ?? [];
            $specificIds = $questionConfig['specific_ids'] ?? [];

            // If specific question IDs are provided and not randomized, use them
            if (! empty($specificIds) && ! $this->is_randomized) {
                $questions = $this->getQuestionsByTypeAndIds($type, $specificIds);
                $generatedQuestions = $generatedQuestions->merge($questions);

                continue;
            }

            // Generate questions based on criteria
            $query = $this->buildQuestionQuery($type, $difficulty, $topicIds, $subtopicIds);

            if ($this->is_randomized) {
                $availableQuestions = $query->get();
                if ($availableQuestions->count() >= $count) {
                    $selectedQuestions = $availableQuestions->random($count);
                } else {
                    $selectedQuestions = $availableQuestions;
                }
            } else {
                $selectedQuestions = $query->take($count)->get();
            }

            $generatedQuestions = $generatedQuestions->merge($selectedQuestions);
        }

        return $generatedQuestions->map(function ($question) {
            return [
                'type' => $this->getQuestionTypeName($question),
                'model' => $question,
                'points' => $question->score ?? 1,
                'difficulty_level' => $question->difficulty_level ?? 'medium',
            ];
        });
    }

    /**
     * Get embedded questions from book-based assignments
     */
    public function getEmbeddedQuestions()
    {
        $allQuestions = collect();

        foreach ($this->questions as $questionConfig) {
            if (! isset($questionConfig['questions'])) {
                continue;
            }

            $questions = $questionConfig['questions'];
            $count = $questionConfig['count'] ?? count($questions);

            // If randomized, shuffle and take count
            if ($this->is_randomized && count($questions) > $count) {
                shuffle($questions);
                $questions = array_slice($questions, 0, $count);
            }

            // Format questions for consistency
            foreach ($questions as $question) {
                $normalized = [
                    'id' => $question['id'] ?? uniqid('embedded_'),
                    'type' => $this->normalizeQuestionType($questionConfig['type']),
                    'question' => $question['question'] ?? '',
                    'points' => $question['points'] ?? 1,
                    'difficulty_level' => $question['difficulty'] ?? 'medium',
                    'explanation' => $question['explanation'] ?? null,
                    'learning_objective' => $question['learning_objective'] ?? null,
                    'is_embedded' => true,
                ];

                // Handle options and convert text-based correct answer to letter
                if (isset($question['options'])) {
                    $optionsData = $this->normalizeQuestionOptionsWithAnswer(
                        $question['options'],
                        $question['correct_answer'] ?? $question['answer'] ?? null
                    );

                    $normalized['options'] = $optionsData['options'];
                    $normalized['answer'] = $optionsData['answer'];
                } else {
                    $normalized['options'] = $this->extractOptions($question);
                    $normalized['answer'] = $question['correct_answer'] ?? $question['answer'] ?? null;
                }

                $allQuestions->push($normalized);
            }
        }

        return $allQuestions;
    }

    /**
     * Normalize question options to use A-E indexing and convert text answer to letter
     */
    public function normalizeQuestionOptionsWithAnswer(array $options, $correctAnswerText): array
    {
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $normalized = [];
        $correctAnswerLetter = null;

        foreach ($options as $index => $optionText) {
            // If already using letter indices, keep them
            if (is_string($index) && in_array($index, $letters)) {
                $letterIndex = $index;
            } else {
                // Convert numeric index to letter
                $letterIndex = is_numeric($index) ? ($letters[$index] ?? 'A') : 'A';
            }

            $normalized[$letterIndex] = $optionText;

            // Check if this option matches the correct answer text
            if ($correctAnswerText && strcasecmp(trim($optionText), trim($correctAnswerText)) === 0) {
                $correctAnswerLetter = $letterIndex;
            }
        }

        // If correct answer is already a letter, use it
        if ($correctAnswerText && in_array(strtoupper($correctAnswerText), $letters)) {
            $correctAnswerLetter = strtoupper($correctAnswerText);
        }

        // Log warning if we couldn't match the text to a letter
        if (! $correctAnswerLetter && $correctAnswerText) {
            \Log::warning('Could not match correct answer text to option', [
                'correct_answer_text' => $correctAnswerText,
                'options' => $normalized,
            ]);

            // Fallback: if correctAnswerText is numeric, use it as index
            if (is_numeric($correctAnswerText)) {
                $correctAnswerLetter = $letters[$correctAnswerText] ?? null;
            }
        }

        return [
            'options' => $normalized,
            'answer' => $correctAnswerLetter,
        ];
    }

    /**
     * Normalize question options to use A-E indexing (without answer conversion)
     */
    public function normalizeQuestionOptions(array $options): array
    {
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $normalized = [];

        foreach ($options as $index => $optionText) {
            // If already using letter indices, keep them
            if (is_string($index) && in_array($index, $letters)) {
                $normalized[$index] = $optionText;
            } else {
                // Convert numeric index to letter
                $letterIndex = is_numeric($index) ? ($letters[$index] ?? 'A') : 'A';
                $normalized[$letterIndex] = $optionText;
            }
        }

        return $normalized;
    }

    /**
     * Extract options from embedded question
     */
    public function extractOptions($question): ?array
    {
        // For multiple choice questions
        if (isset($question['options'])) {
            return $this->normalizeQuestionOptions($question['options']);
        }

        // For true/false questions
        if (isset($question['type']) && ($question['type'] === 'true_false' || $question['type'] === 'true_or_false_question')) {
            return ['A' => 'True', 'B' => 'False'];
        }

        return null;
    }

    /**
     * Normalize question type names
     */
    public function normalizeQuestionType($type): string
    {
        return match ($type) {
            'multiple_choice', 'multiple_choice_question' => 'multiple_choice_question',
            'true_false', 'true_or_false_question' => 'true_or_false_question',
            'essay', 'essay_question' => 'essay_question',
            default => $type
        };
    }
}
