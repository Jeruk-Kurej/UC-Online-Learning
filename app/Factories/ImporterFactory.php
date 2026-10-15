<?php

namespace App\Factories;

use App\Imports\UCOStudentImport;
use App\Imports\FormResponseImport;
use Illuminate\Support\Facades\Log;

class ImporterFactory
{
    /**
     * Peek at the file to determine which importer class to use.
     */
    public static function make(string $path, string $importId, string $originalName = ''): object
    {
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));

        // --- Detect the import type from the CSV content first ---
        $detectedType = null;
        if ($ext === 'csv' && file_exists($path)) {
            $handle = fopen($path, 'r');
            if ($handle) {
                $headers = fgetcsv($handle);
                if ($headers) {
                    $normHeaders = array_map(function($h) {
                        return strtolower(trim(str_replace([' ', '_', '?'], '', $h)));
                    }, $headers);

                    $selectedIdx = array_search('selected', $normHeaders);
                    $categoryIdx = array_search('category', $normHeaders);

                    if ($selectedIdx !== false && $categoryIdx !== false) {
                        while (($row = fgetcsv($handle)) !== false) {
                            $selectedVal = strtolower(trim($row[$selectedIdx] ?? ''));
                            $categoryVal = strtolower(trim($row[$categoryIdx] ?? ''));

                            if (str_contains($categoryVal, 'intrapreneur') && in_array($selectedVal, ['true', '1', 'yes', 'selected', 'y'])) {
                                $detectedType = 'intrapreneur';
                                break;
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        // --- Fall back to the file name ---
        if (!$detectedType) {
            $lowerName = strtolower($originalName ?: basename($path));
            if (str_contains($lowerName, 'intrapreneur')) {
                $detectedType = 'intrapreneur';
            } elseif (str_contains($lowerName, 'entrepreneur')) {
                $detectedType = 'entrepreneur';
            } else {
                $detectedType = 'entrepreneur';
            }
        }

        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info('Import: XLSX filename heuristic → UCOStudentImport');
                return new UCOStudentImport($importId);
            }
            Log::info('Import: XLSX default → FormResponseImport (' . $detectedType . ')');
            $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.xlsx' : 'entrepreneur.xlsx';
            return new FormResponseImport($importId, $constructedName);
        }

        // --- CSV: search the first 2 KB for markers ---
        $handle = fopen($path, 'r');
        if (! $handle) {
            Log::info('Import: falling back to Form Response format (' . $detectedType . ')');
            $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.csv' : 'entrepreneur.csv';
            return new FormResponseImport($importId, $constructedName);
        }
        $peek = fread($handle, 2048);
        fclose($handle);

        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            Log::info('Import: detected UCO Student Profile format via content markers');
            return new UCOStudentImport($importId);
        }

        Log::info('Import: falling back to Form Response format (' . $detectedType . ')');
        $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.csv' : 'entrepreneur.csv';
        return new FormResponseImport($importId, $constructedName);
    }
}
