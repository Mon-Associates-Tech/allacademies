<?php

if (!function_exists('fisher_yates_shuffle')) {
    function fisher_yates_shuffle($array, $seed)
    {
        @mt_srand($seed);
        for ($i = count($array) - 1; $i > 0; --$i) {
            $j = @mt_rand(0, $i);
            $tmp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $tmp;
        }
        mt_srand();

        return $array;
    }
}

function convertMinutesToHoursMinutes($minutes): string
{
    // Ensure input is a non-negative integer
    $minutes = (int)$minutes;
    if ($minutes < 0) {
        return "Invalid duration";
    }

    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    $result = [];
    if ($hours > 0) {
        $result[] = $hours . ' Hour' . ($hours > 1 ? 's' : '');
    }
    if ($remainingMinutes > 0 || $hours === 0) {
        $result[] = $remainingMinutes . ' Minute' . ($remainingMinutes != 1 ? 's' : '');
    }

    return implode(' ', $result);
}
