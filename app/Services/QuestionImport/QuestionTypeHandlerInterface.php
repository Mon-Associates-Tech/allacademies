<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;

/**
 * Contract every question-type handler must implement so the orchestrator
 * (DocumentAiQuestionImportService) can stay completely type-agnostic.
 */
interface QuestionTypeHandlerInterface
{
    /**
     * The JSON array key this type lives under in the AI response,
     * e.g. "multiple_choice", "true_false", "essay".
     */
    public function key(): string;

    /**
     * The fragment of prompt text describing this type's extraction rules
     * and required JSON shape. Injected into the shared combined prompt.
     */
    public function promptInstructions(): string;

    /**
     * Validate + normalize one raw item from the AI response.
     * Return null to silently skip an invalid/incomplete item.
     */
    public function normalize(array $item): ?array;

    /**
     * Build the Eloquent ::create() payload for one normalized item.
     * Return null if the item can't be turned into a savable row.
     */
    public function buildModelData(array $item, AcademicTopic $topic, ?AcademicSubtopic $subtopic, ?int $userId): ?array;

    /**
     * Persist one row and return its ID.
     */
    public function create(array $modelData): int;

    /**
     * Optional non-fatal warning for a normalized item (e.g. low-confidence
     * answer). Return null if nothing to flag.
     */
    public function warningFor(array $item, int $index): ?string;
}
