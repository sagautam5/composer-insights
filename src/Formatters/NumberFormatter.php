<?php

namespace ComposerInsights\Formatters;

class NumberFormatter
{
    /**
     * Converts a number to a human-readable format.
     *
     * Examples:
     *  - 1200   => "1.2k"
     *  - 500000 => "500k"
     *  - 1200000 => "1.2M"
     *
     * @param int|string $number
     * @return string
     */
    public static function humanize(int|string|null $number): string
    {
        if ($number === null) {
            return 'N/A';
        }
        
        if (!is_numeric($number)) {
            return (string) $number;
        }

        $number = (int) $number;

        return match (true) {
            $number >= 1_000_000_000 => round($number / 1_000_000_000, 1) . ' B',
            $number >= 1_000_000     => round($number / 1_000_000, 1) . ' M',
            $number >= 1_000         => round($number / 1_000, 1) . ' k',
            default                  => (string) $number,
        };
    }
}
