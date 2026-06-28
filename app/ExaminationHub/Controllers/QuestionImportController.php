    /**
     * Create a question based on type and data
     */
    private function createQuestion(array $questionData, string $type, int $topicId)
    {
        switch ($type) {
            case 'multiple_choice':
                return MultipleChoiceQuestion::create([
                    'question' => new Mark(null, $questionData['question']),
                    'option_a' => isset($questionData['options'][0]) ? new Mark(null, $questionData['options'][0]) : null,
                    'option_b' => isset($questionData['options'][1]) ? new Mark(null, $questionData['options'][1]) : null,
                    'option_c' => isset($questionData['options'][2]) ? new Mark(null, $questionData['options'][2]) : null,
                    'option_d' => isset($questionData['options'][3]) ? new Mark(null, $questionData['options'][3]) : null,
                    'option_e' => isset($questionData['options'][4]) ? new Mark(null, $questionData['options'][4]) : null,
                    'answer' => new Mark(null, $questionData['answer'] ?? null),
                    'score' => $questionData['points'] ?? 1,
                    'difficulty_level' => $questionData['difficulty_level'] ?? 'medium',
                    'academic_topic_id' => $topicId,
                    'added_by' => Auth::id(),
                ]);

            case 'true_false':
                return TrueOrFalseQuestion::create([
                    'question' => new Mark(null, $questionData['question']),
                    'answer' => (bool) ($questionData['answer'] ?? false),
                    'score' => $questionData['points'] ?? 1,
                    'difficulty_level' => $questionData['difficulty_level'] ?? 'medium',
                    'academic_topic_id' => $topicId,
                    'added_by' => Auth::id(),
                ]);

            case 'essay':
                return EssayQuestion::create([
                    'question' => new Mark(null, $questionData['question']),
                    'answer' => new Mark(null, $questionData['answer'] ?? null),
                    'score' => $questionData['points'] ?? 1,
                    'difficulty_level' => $questionData['difficulty_level'] ?? 'medium',
                    'academic_topic_id' => $topicId,
                    'added_by' => Auth::id(),
                ]);

            default:
                return null;
        }
    }