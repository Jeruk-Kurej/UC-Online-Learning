<?php

namespace App\Imports\Concerns;

use Carbon\Carbon;

/**
 * Dates from spreadsheet cells: a bare year becomes 1 January of that year, anything unreadable becomes null.
 */
trait ParsesImportDates
{
    private function parseDate(?string $val): ?string
    {
        if (!$val) return null;

        if (preg_match('/^\d{4}$/', trim($val))) {
            return trim($val) . '-01-01';
        }

        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
