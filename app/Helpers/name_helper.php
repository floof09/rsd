<?php

if (!function_exists('name_middle_initial')) {
    /**
     * Get the first Unicode letter from the middle name as an initial with a period (e.g., "Anne Marie" -> "A.").
     */
    function name_middle_initial(?string $middle): ?string
    {
        $middle = is_string($middle) ? trim($middle) : '';
        if ($middle === '') { return null; }
        // Normalize whitespace
        $middle = preg_replace('/\s+/u', ' ', $middle);
        // Find first Unicode letter
        if (preg_match('/[\p{L}\p{M}]/u', $middle, $m)) {
            $ch = $m[0];
            return strtoupper($ch) . '.';
        }
        return null;
    }
}

if (!function_exists('format_full_name')) {
    /**
     * Format a person's full name with options.
     *
     * Options:
     * - middleStyle: 'full' | 'initial' (default 'full')
     */
    function format_full_name(?string $first, ?string $middle, ?string $last, ?string $suffix, array $opts = []): string
    {
        $first = is_string($first) ? trim($first) : '';
        $middle = is_string($middle) ? trim($middle) : '';
        $last = is_string($last) ? trim($last) : '';
        $suffix = is_string($suffix) ? trim($suffix) : '';

        $middleStyle = $opts['middleStyle'] ?? 'full';

        $parts = [];
        if ($first !== '') { $parts[] = $first; }
        if ($middle !== '') {
            if ($middleStyle === 'initial') {
                $mi = name_middle_initial($middle);
                if ($mi) { $parts[] = $mi; }
            } else {
                $parts[] = $middle;
            }
        }
        if ($last !== '') { $parts[] = $last; }

        $name = trim(implode(' ', $parts));
        if ($suffix !== '') {
            $name .= ' ' . $suffix;
        }
        return $name !== '' ? $name : '—';
    }
}
