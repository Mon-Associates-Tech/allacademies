<?php

namespace App\ExaminationHub\Support;

class ParticipantAuthHelper
{
    /**
     * Generate a 6-character auth code from a participant's full name.
     *
     * Rules:
     *  - Split on whitespace; use only the first two words.
     *  - Two+ words: first 3 chars of word 1 + first 3 chars of word 2.
     *  - One word:   all 6 chars from that word.
     *  - If a word is shorter than its required slice, pad with zeros.
     *  - Result is always uppercase.
     */
    public static function generate(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

        if (count($parts) >= 2) {
            $a    = strtoupper(substr($parts[0], 0, 3));
            $b    = strtoupper(substr($parts[1], 0, 3));
            $auth = str_pad($a, 3, '0') . str_pad($b, 3, '0');
        } else {
            $word = strtoupper($parts[0] ?? '');
            $auth = str_pad(substr($word, 0, 6), 6, '0');
        }

        return $auth;
    }
}
