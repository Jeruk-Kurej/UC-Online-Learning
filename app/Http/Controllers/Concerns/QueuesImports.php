<?php

namespace App\Http\Controllers\Concerns;

use App\Factories\ImporterFactory;
use App\Imports\UCOStudentImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Stores an uploaded import file, picks the importer from its content and queues it; the JSON replies are the ones the
 * import progress card (components/import-manager.js) reads.
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

        try {
            $importer = ImporterFactory::make((string) $file->getRealPath(), $importId, $file->getClientOriginalName())->deleteFileAfterImport($path);
            Excel::queueImport($importer, (string) $path, 'local');
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);

            throw $e;
        }

        return [$importId, $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response'];
    }

    private function importQueuedJson(string $importId, string $format, string $message): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'importId' => $importId,
            'format' => $format,
            'message' => $message,
        ]);
    }

    private function importFailedJson(\Exception $e): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'Import failed: '.$e->getMessage(),
        ], 500);
    }
}
