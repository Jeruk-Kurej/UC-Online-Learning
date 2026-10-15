<?php

namespace App\Http\Controllers\Concerns;

use App\Factories\ImporterFactory;
use App\Imports\UCOStudentImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Stores an uploaded import file, picks the importer from its content and queues it.
 */
trait QueuesImports
{
    /**
     * @return array{0: string, 1: string} the import id and the detected format label
     */
    private function queueImport(UploadedFile $file): array
    {
        $importId = (string) Str::uuid();
        $path = $file->store('imports', 'local');

        $importer = ImporterFactory::make((string) $file->getRealPath(), $importId, $file->getClientOriginalName());
        Excel::queueImport($importer, (string) $path, 'local');

        return [$importId, $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response'];
    }
}
