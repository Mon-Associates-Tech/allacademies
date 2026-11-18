<?php

namespace App\ExaminationHub\Support;

class ParticipantAuthHelper
{
    /**
     * Generate a short auth code from a participant's full name.
     *
     * Rules:
     *  - Split on whitespace.
     *  - Exactly 2 names: first character of first word + entire second word.
     *  - More than 2 names: first character of first word + entire last word.
     *  - One name: the word itself.
     *  - Result is always uppercase.
     */
    public static function generate(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

        // Strip email domain parts (anything after @) from each word
        $parts = array_map(function ($part) {
            $pos = strpos($part, '@');
            return $pos !== false ? substr($part, 0, $pos) : $part;
        }, $parts);

        // Re-filter empty parts after stripping
        $parts = array_filter($parts, fn ($p) => $p !== '');
        $parts = array_values($parts);

        if (count($parts) === 1) {
            return strtoupper($parts[0]);
        }

        $firstChar = strtoupper(substr($parts[0], 0, 1));

        if (count($parts) === 2) {
            $secondWord = strtoupper($parts[1]);
        } else {
            $secondWord = strtoupper(end($parts));
        }

        return $firstChar . $secondWord;
    }
}
