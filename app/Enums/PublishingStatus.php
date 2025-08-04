<?php

namespace App\Enums;

enum PublishingStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    /**
     * Get the display label for the status
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
        };
    }

    /**
     * Get the description for the status
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Book is saved as draft and not visible to users',
            self::PUBLISHED => 'Book is published and visible to all users',
        };
    }

    /**
     * Get the color class for UI display
     */
    public function getColorClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-yellow-100 text-yellow-800',
            self::PUBLISHED => 'bg-green-100 text-green-800',
        };
    }

    /**
     * Get the icon for the status
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
            self::PUBLISHED => 'M5 13l4 4L19 7',
        };
    }

    /**
     * Check if the status allows public access
     */
    public function isPublic(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Check if the status is editable
     */
    public function isEditable(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Get all status options for dropdowns
     */
    public static function getOptions(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->getLabel(),
            self::PUBLISHED->value => self::PUBLISHED->getLabel(),
        ];
    }

    /**
     * Get the default status
     */
    public static function default(): self
    {
        return self::PUBLISHED;
    }

    /**
     * Convert legacy boolean/integer status to string enum
     * Handles: 0, 1, '0', '1', false, true, 'draft', 'published'
     */
    public static function fromLegacy($status): self
    {
        // Handle null/empty values
        if ($status === null || $status === '') {
            return self::default();
        }

        // Handle boolean values
        if (is_bool($status)) {
            return $status ? self::PUBLISHED : self::DRAFT;
        }

        // Handle integer/numeric values
        if (is_numeric($status)) {
            return (int) $status === 1 ? self::PUBLISHED : self::DRAFT;
        }

        // Handle string values
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'published', '1', 'true', 'active', 'public' => self::PUBLISHED,
            'draft', '0', 'false', 'inactive', 'private' => self::DRAFT,
            default => self::default(),
        };
    }

    /**
     * Check if a value represents published status (handles legacy formats)
     */
    public static function isPublished($status): bool
    {
        return self::fromLegacy($status) === self::PUBLISHED;
    }

    /**
     * Check if a value represents draft status (handles legacy formats)
     */
    public static function isDraft($status): bool
    {
        return self::fromLegacy($status) === self::DRAFT;
    }

    /**
     * Get status label from any format (legacy or new)
     */
    public static function getLabelFromLegacy($status): string
    {
        return self::fromLegacy($status)->getLabel();
    }
}
