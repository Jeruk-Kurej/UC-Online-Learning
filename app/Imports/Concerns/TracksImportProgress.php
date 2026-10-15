<?php

namespace App\Imports\Concerns;

use Illuminate\Support\Facades\Cache;

/**
 * Per-row progress counters in the cache, read by the import progress bar. Needs $importId on the importer.
 */
trait TracksImportProgress
{
    private function updateProgress(string $status): void
    {
        if (! $this->importId) {
            return;
        }

        $prefix = "import_{$this->importId}";
        Cache::increment("{$prefix}_current");
        Cache::increment($status === 'success' ? "{$prefix}_success" : "{$prefix}_skipped");
    }
}
