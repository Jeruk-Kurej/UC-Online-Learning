<?php

namespace App\Imports\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

/**
 * Progress of an import in the cache (read by the import progress card): totals before, counters per row, skipped
 * rows with their reason, and the completed state. Needs $importId, $successCount, $skippedCount and $errors on the
 * importer, plus the log lines it writes (startedLogLine, completedLogLine, skippedLogLine) and headerRows().
 * The progress card is for admins and may name an email; the log is read by more people and never carries personal data.
 * The uploaded sheet (names, phones, links) is deleted when the import has finished or failed.
 */
trait TracksImportProgress
{
    protected ?string $uploadedPath = null;

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
                $this->forgetUploadedFile();

                if (! $this->importId) {
                    return;
                }
                $prefix = "import_{$this->importId}";
                $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
                $progress['status'] = 'completed';
                Cache::put($prefix, $progress, now()->addMinutes(60));
                Log::info($this->completedLogLine());
            },
            ImportFailed::class => function () {
                $this->forgetUploadedFile();
            },
        ];
    }

    // --- The uploaded sheet ---

    public function deleteFileAfterImport(string $path): static
    {
        $this->uploadedPath = $path;

        return $this;
    }

    private function forgetUploadedFile(): void
    {
        if ($this->uploadedPath) {
            Storage::disk('local')->delete($this->uploadedPath);
        }
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

    /**
     * The exception text without personal data: a database error keeps the driver's message, not the SQL and its values.
     */
    private function safeMessage(\Throwable $e): string
    {
        if ($e instanceof QueryException) {
            return $e->errorInfo[2] ?? $e->getPrevious()?->getMessage() ?? class_basename($e);
        }

        return $e->getMessage();
    }

    private function skip(string $reason): void
    {
        $this->skippedCount++;
        $this->errors[] = $reason;
        Log::warning($this->skippedLogLine(preg_replace('/\S+@\S+/', '<email>', $reason)));
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
