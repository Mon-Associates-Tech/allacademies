<?php

namespace App\Support;

class Wordy
{
    public function __construct(
        public readonly int $plus,
        public readonly int $minus,
        public readonly int $zero
    ) {}

    public function percentage(): array
    {
        $decimal = ($this->plus + $this->minus + $this->zero) / 5;

        return [
            (int) round($this->plus / $decimal),
            (int) round($this->minus / $decimal),
            (int) round($this->zero / $decimal),
        ];
    }

    public static function changes(array $current, array $incoming)
    {
        [$original, $changes] = [$current, $incoming];
        [$plus, $minus, $zero] = [0, 0, 0];
        $lines = ['institution', 'college', 'school', 'faculty', 'department'];

        foreach ($lines as $line) {
            $current = data_get($original, $line);
            $incoming = data_get($changes, $line);
            $current = $current ? explode(' ', $current) : [];
            $incoming = $incoming ? explode(' ', $incoming) : [];
            $current = array_count_values($current);
            $incoming = array_count_values($incoming);
            $words = array_keys([...$current, ...$incoming]);

            foreach ($words as $word) {
                $difference = data_get($current, $word, 0) - data_get($incoming, $word, 0);

                if ($difference > 0) {
                    $minus += $difference;
                } elseif ($difference < 0) {
                    $plus += abs($difference);
                } else {
                    $zero += 1;
                }
            }
        }

        return new static($plus, $minus, $zero);
    }
}
