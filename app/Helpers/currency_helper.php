<?php

/**
 * DealSach Currency Helper
 *
 * Load with: helper('currency');
 */

if (! function_exists('format_vnd')) {
    /**
     * Format an amount as Vietnamese Đồng (VND).
     *
     * Examples:
     *   format_vnd(199000)   → "199.000₫"
     *   format_vnd(0)        → "0₫"
     *   format_vnd(null)     → "0₫"
     *   format_vnd(1500000)  → "1.500.000₫"
     *
     * @param int|float|null $amount The amount to format.
     *
     * @return string Formatted currency string with ₫ suffix.
     */
    function format_vnd(int|float|null $amount): string
    {
        if ($amount === null || $amount == 0) {
            return '0₫';
        }

        return number_format($amount, 0, ',', '.') . '₫';
    }
}
