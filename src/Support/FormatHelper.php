<?php

namespace ComposerInsights\Support;

use DateTime;

class FormatHelper
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
    public static function humanNumber(int|string $number): string
    {
        if (!is_numeric($number)) {
            return (string) $number;
        }

        $number = (int) $number;

        return match (true) {
            $number >= 1_000_000_000 => round($number / 1_000_000_000, 1) . 'B',
            $number >= 1_000_000     => round($number / 1_000_000, 1) . 'M',
            $number >= 1_000         => round($number / 1_000, 1) . 'k',
            default                  => (string) $number,
        };
    }

    /**
     * Returns a human-friendly time difference string like "2 days ago".
     *
     * @param string $datetime A valid date-time string (e.g., from GitHub API).
     * @param bool $full Whether to return full string ("2 days, 3 hours ago") or just the first unit.
     * @return string
     */
    public static function timeAgo(string $datetime, bool $full = false): string
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // Compute weeks separately (not assigning to $diff->w)
        $weeks = (int) floor($diff->d / 7);
        $daysRemaining = $diff->d - $weeks * 7;

        $units = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        // Build parts array using individual values
        $parts = [];
        foreach ($units as $key => $label) {
            $value = match ($key) {
                'y' => $diff->y,
                'm' => $diff->m,
                'w' => $weeks,
                'd' => $daysRemaining,
                'h' => $diff->h,
                'i' => $diff->i,
                's' => $diff->s,
            };

            if ($value) {
                $parts[] = $value . ' ' . $label . ($value > 1 ? 's' : '');
            }
        }

        return $parts ? implode(', ', $full ? $parts : [reset($parts)]) . ' ago' : 'just now';
    }
}
