<?php

namespace App\Imports\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

/**
 * Progress of an import in the cache (read by the import progress card): totals before, counters per row, skipped
 * rows with their reason, and the completed state. Needs $importId, $successCount, $skippedCount and $errors on the
 * importer, plus the log lines it writes (startedLogLine, completedLogLine, skippedLogLine) and headerRows().
 */
trait TracksImportProgress
{
    abstract protected function headerRows(): int;

    abstract protected function startedLogLine(int $total): string;

    abstract protected function completedLogLine(): string;

    abstract protected function skippedLogLine(string $reason): string;

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                if (! $this->importId) {
                    return;
                }
                $total = max(0, max($event->getReader()->getTotalRows()) - $this->headerRows());
                $prefix = "import_{$this->importId}";

                Cache::put($prefix, ['status' => 'processing', 'errors' => []], now()->addMinutes(60));
                Cache::forever("{$prefix}_total", $total);
                Cache::forever("{$prefix}_current", 0);
                Cache::forever("{$prefix}_success", 0);
                Cache::forever("{$prefix}_skipped", 0);

                Log::info($this->startedLogLine($total));
            },
            AfterImport::class => function () {
                if (! $this->importId) {
                    return;
                }
                $prefix = "import_{$this->importId}";
                $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
                $progress['status'] = 'completed';
                Cache::put($prefix, $progress, now()->addMinutes(60));
                Log::info($this->completedLogLine());
            },
        ];
    }

    private function updateProgress(string $status): void
    {
        if (! $this->importId) {
            return;
        }

        $prefix = "import_{$this->importId}";
        Cache::increment("{$prefix}_current");
        Cache::increment($status === 'success' ? "{$prefix}_success" : "{$prefix}_skipped");
    }

    private function skip(string $reason): void
    {
        $this->skippedCount++;
        $this->errors[] = $reason;
        Log::warning($this->skippedLogLine($reason));
        $this->updateProgress('skipped');

        if ($this->importId) {
            $prefix = "import_{$this->importId}";
            $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
            if (! is_array($progress)) {
                $progress = ['status' => 'processing', 'errors' => []];
            }
            if (! isset($progress['errors']) || ! is_array($progress['errors'])) {
                $progress['errors'] = [];
            }
            $progress['errors'][] = $reason;
            Cache::put($prefix, $progress, now()->addMinutes(60));
        }
    }
}
