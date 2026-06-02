<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * Thin wrapper around spatie/image-optimizer.
 *
 * Call optimizeUploadedFile() before ->store() in any controller that
 * accepts image uploads. The file is compressed in-place on the temp
 * disk, so there is zero change to existing storage logic.
 */
class ImageOptimizerService
{
    /**
     * Compress an UploadedFile in-place on the temp disk.
     *
     * Only runs on files whose MIME type starts with "image/".
     * Non-image files (PDFs, docs, etc.) are silently skipped.
     */
    public function optimizeUploadedFile(UploadedFile $file): void
    {
        // Skip non-image files (e.g. PDF activity docs)
        $mime = $file->getMimeType() ?? '';
        if (! str_starts_with($mime, 'image/')) {
            return;
        }

        // Skip SVGs — CLI optimizers don't handle them, and svgo
        // requires Node.js which may not be available at runtime.
        if ($mime === 'image/svg+xml') {
            return;
        }

        $this->optimize($file->getRealPath());
    }

    /**
     * Run the optimizer chain on a local file path.
     *
     * The file is overwritten in-place with the compressed version.
     * If the optimizers are not installed (e.g. local dev without
     * system binaries), the call silently does nothing.
     */
    public function optimize(string $path): void
    {
        try {
            $chain = OptimizerChainFactory::create();
            $chain->optimize($path);
        } catch (\Throwable $e) {
            // Silently swallow — compression is best-effort.
            // On local dev without CLI tools, uploads still work fine.
            report($e);
        }
    }
}
