<?php

namespace App\Factories;

use App\Imports\FormResponseImport;
use App\Imports\UCOStudentImport;
use Illuminate\Support\Facades\Log;

class ImporterFactory
{
    /**
     * Peek at the file to determine which importer class to use.
     */
    public static function make(string $path, string $importId, string $originalName = ''): object
    {
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));
        $type = self::hasSelectedIntrapreneur($path, $ext) ? 'intrapreneur' : self::typeFromName($originalName ?: basename($path));

        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info('Import: XLSX filename heuristic → UCOStudentImport');

                return new UCOStudentImport($importId);
            }
            Log::info('Import: XLSX default → FormResponseImport ('.$type.')');

            return new FormResponseImport($importId, "{$type}.xlsx");
        }

        if (self::looksLikeUcoStudentCsv($path)) {
            Log::info('Import: detected UCO Student Profile format via content markers');

            return new UCOStudentImport($importId);
        }

        Log::info('Import: falling back to Form Response format ('.$type.')');

        return new FormResponseImport($importId, "{$type}.csv");
    }

    // --- Form sheet type: a CSV with a selected intrapreneur row is the Intrapreneur sheet, otherwise the file name decides ---

    private static function hasSelectedIntrapreneur(string $path, string $ext): bool
    {
        if ($ext !== 'csv' || ! file_exists($path)) {
            return false;
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            return false;
        }

        $found = false;
        $headers = fgetcsv($handle);
        if ($headers) {
            $normHeaders = array_map(fn ($h) => strtolower(trim(str_replace([' ', '_', '?'], '', $h))), $headers);

            $selectedIdx = array_search('selected', $normHeaders);
            $categoryIdx = array_search('category', $normHeaders);

            if ($selectedIdx !== false && $categoryIdx !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $selectedVal = strtolower(trim($row[$selectedIdx] ?? ''));
                    $categoryVal = strtolower(trim($row[$categoryIdx] ?? ''));

                    if (str_contains($categoryVal, 'intrapreneur') && in_array($selectedVal, ['true', '1', 'yes', 'selected', 'y'])) {
                        $found = true;
                        break;
                    }
                }
            }
        }
        fclose($handle);

        return $found;
    }

    private static function typeFromName(string $name): string
    {
        return str_contains(strtolower($name), 'intrapreneur') ? 'intrapreneur' : 'entrepreneur';
    }

    // --- UCO Student Profile CSV: its header rows (first 2 KB) name the NIS and Sub Prodi columns ---

    private static function looksLikeUcoStudentCsv(string $path): bool
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return false;
        }
        $peek = fread($handle, 2048);
        fclose($handle);

        return stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false;
    }
}
