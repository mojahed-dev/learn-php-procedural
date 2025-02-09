<?php

/**
 * Formats a monetary amount into a currency string.
 *
 * @param float $amount The amount to format.
 * @return string The formatted amount as a dollar value.
 */
function formatDollarAmount(float $amount): string 
{
    $isNegative = $amount < 0;

    // Format amount with two decimal places and prepend with "$" sign
    return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
}

/**
 * Formats a date string into a more readable format.
 *
 * @param string $date The date in 'YYYY-MM-DD' format.
 * @return string The formatted date (e.g., "Jan 1, 2024").
 */
function formatDate(string $date): string 
{
    return date('M j, Y', strtotime($date));
}
