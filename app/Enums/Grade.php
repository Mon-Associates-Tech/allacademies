<?php

namespace App\Enums;

enum Grade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case F = 'F';

    /**
     * Get the grade based on percentage score
     */
    public static function fromPercentage(float $percentage): self
    {
        return match (true) {
            $percentage >= 90 => self::A,
            $percentage >= 80 => self::B,
            $percentage >= 70 => self::C,
            $percentage >= 60 => self::D,
            default => self::F,
        };
    }

    /**
     * Get the minimum percentage required for this grade
     */
    public function getMinimumPercentage(): int
    {
        return match ($this) {
            self::A => 90,
            self::B => 80,
            self::C => 70,
            self::D => 60,
            self::F => 0,
        };
    }

    /**
     * Get the maximum percentage for this grade
     */
    public function getMaximumPercentage(): int
    {
        return match ($this) {
            self::A => 100,
            self::B => 89,
            self::C => 79,
            self::D => 69,
            self::F => 59,
        };
    }

    /**
     * Get grade description
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::A => 'Excellent',
            self::B => 'Good',
            self::C => 'Satisfactory',
            self::D => 'Needs Improvement',
            self::F => 'Failing',
        };
    }

    /**
     * Check if this is a passing grade
     */
    public function isPassing(): bool
    {
        return $this !== self::F;
    }

    /**
     * Get all grade options as an array
     */
    public static function getOptions(): array
    {
        return [
            self::A->value => self::A->getDescription(),
            self::B->value => self::B->getDescription(),
            self::C->value => self::C->getDescription(),
            self::D->value => self::D->getDescription(),
            self::F->value => self::F->getDescription(),
        ];
    }

    /**
     * Get grade range as string
     */
    public function getRange(): string
    {
        if ($this === self::A) {
            return '90-100%';
        }

        return $this->getMinimumPercentage().'-'.$this->getMaximumPercentage().'%';
    }
}
