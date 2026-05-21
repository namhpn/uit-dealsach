<?php

if (! function_exists('format_vnd')) {
    function format_vnd(int|float|null $amount): string
    {
        if ($amount === null || (float) $amount <= 0) {
            return '0đ';
        }

        return number_format((float) $amount, 0, ',', '.') . 'đ';
    }
}
