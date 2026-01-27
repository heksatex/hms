<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('money_to_int')) {
    function money_to_int($value, $scale = 2)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // hilangkan koma ribuan kalau ada
        if (is_string($value)) {
            $value = str_replace(',', '', $value);
        }

        return (int) round((float)$value * pow(10, $scale));
    }
}