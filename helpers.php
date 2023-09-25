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
