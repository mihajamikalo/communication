<?php

if (! function_exists('format_ar')) {
    function format_ar(float|int $amount): string
    {
        return number_format($amount, 0, ',', ' ') . ' Ar';
    }
}

if (! function_exists('format_ar_short')) {
    function format_ar_short(float|int $amount): string
    {
        if ($amount >= 1000000) {
            return round($amount / 1000000, 2) . 'M Ar';
        }

        return format_ar($amount);
    }
}
