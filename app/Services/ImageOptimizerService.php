<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChainFactory;

/**
 * Two-pass image compressor.
 *
 * Pass 1 — PHP GD (always runs, zero dependencies, works on Vercel):
 *   Re-encodes JPEG, PNG, GIF and WebP images using GD at a reduced
 *   quality level. This is the primary compression step.
 *
 * Pass 2 — spatie/image-optimizer CLI tools (optional):
 *   If system binaries like jpegoptim / pngquant / optipng are installed
 *   (local dev with `brew install ...`, or Docker with apt-get install),
 *   they squeeze the already-GD-compressed file even further.
 *   If the binaries are absent (Vercel), this pass silently skips.
 *
 * The file is modified in-place on the PHP temp disk, so there is zero
 * change to existing Storage::store() / storeAs() call sites.
 */
class ImageOptimizerService
{
    /**
     * JPEG/WebP quality for GD re-encoding. 0 (worst) – 100 (lossless).
     * 78 gives an excellent quality-to-size ratio without visible degradation.
     */
    private const JPEG_QUALITY = 78;

    /**
     * PNG compression level for GD. 0 (none) – 9 (max).
     * Higher compression is lossless but slower. 6 is a good balance.
     */
    private const PNG_COMPRESSION = 6;

    /**
     * Maximum dimension (width or height) to resize large images to.
     * Keeps images sharp while guaranteeing they stay well under 500KB.
     */
    private const MAX_DIMENSION = 1200;

    /**
     * Compress an UploadedFile in-place on the temp disk.
     *
     * Only runs on files whose MIME type starts with "image/".
     * Non-image files (PDFs, docs, etc.) are silently skipped.
     */
    public function optimizeUploadedFile(UploadedFile $file): void
    {
        $mime = $file->getMimeType() ?? '';

        // Skip non-image files (e.g. PDF activity docs)
        if (! str_starts_with($mime, 'image/')) {
            return;
        }

        // Skip SVGs — they are XML, not raster images
        if ($mime === 'image/svg+xml') {
            return;
        }

        $this->optimize($file->getRealPath());
    }

    /**
     * Compress a local image file in-place using two passes:
     *   1. PHP GD re-encoding (always runs, works on Vercel)
     *   2. spatie/image-optimizer CLI tools (runs only if binaries exist)
     */
    public function optimize(string $path): void
    {
        // Pass 1: PHP GD compression (no system binaries required)
        $this->gdCompress($path);

        // Pass 2: CLI tool optimizer (optional, skips silently if not installed)
        $this->cliOptimize($path);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pass 1: GD-based compression
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Re-encode and resize the image using PHP GD.
     *
     * Supports JPEG, PNG, GIF, and WebP. Unknown formats are skipped.
     * If GD is not compiled in, the method silently does nothing.
     */
    private function gdCompress(string $path): void
    {
        if (! extension_loaded('gd') || ! file_exists($path)) {
            return;
        }

        try {
            $info = @getimagesize($path);
            if (! $info) {
                return; // Not a recognised image format
            }

            $mimeType = $info['mime'] ?? '';

            [$image, $hasAlpha] = $this->gdLoad($path, $mimeType);
            if (! $image) {
                return;
            }

            // Get original dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Proportional resize if any dimension exceeds MAX_DIMENSION
            if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
                if ($width > $height) {
                    $newWidth = self::MAX_DIMENSION;
                    $newHeight = (int) ($height * (self::MAX_DIMENSION / $width));
                } else {
                    $newHeight = self::MAX_DIMENSION;
                    $newWidth = (int) ($width * (self::MAX_DIMENSION / $height));
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG and WebP
                if ($hasAlpha) {
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                }

                imagecopyresampled(
                    $resizedImage,
                    $image,
                    0, 0, 0, 0,
                    $newWidth,
                    $newHeight,
                    $width,
                    $height
                );

                imagedestroy($image);
                $image = $resizedImage;
            }

            $this->gdSave($image, $path, $mimeType, $hasAlpha);
            imagedestroy($image);
        } catch (\Throwable $e) {
            // GD compression is best-effort — never block an upload
            report($e);
        }
    }

    /**
     * Load an image resource from the given file path.
     *
     * @return array{0: \GdImage|false, 1: bool}  [image, hasAlpha]
     */
    private function gdLoad(string $path, string $mimeType): array
    {
        $hasAlpha = false;

        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => (function () use ($path, &$hasAlpha) {
                $img = @imagecreatefrompng($path);
                if ($img) {
                    $hasAlpha = true; // PNG may have transparency
                    imagesavealpha($img, true);
                }
                return $img;
            })(),
            'image/gif'  => @imagecreatefromgif($path),
            'image/webp' => (function () use ($path, &$hasAlpha) {
                $img = @imagecreatefromwebp($path);
                if ($img) {
                    $hasAlpha = true;
                    imagesavealpha($img, true);
                }
                return $img;
            })(),
            default      => false,
        };

        return [$image, $hasAlpha];
    }

    /**
     * Save the GD image back to disk with reduced quality / compression.
     */
    private function gdSave(\GdImage $image, string $path, string $mimeType, bool $hasAlpha): void
    {
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($image, $path, self::JPEG_QUALITY);
                break;

            case 'image/png':
                // PNG compression is lossless — alpha is preserved
                imagepng($image, $path, self::PNG_COMPRESSION);
                break;

            case 'image/gif':
                imagegif($image, $path);
                break;

            case 'image/webp':
                imagewebp($image, $path, self::JPEG_QUALITY);
                break;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pass 2: CLI optimizer (spatie/image-optimizer)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Run spatie/image-optimizer on the file.
     *
     * On Vercel (no CLI binaries installed) the optimizer chain finds no
     * usable tools and exits immediately — no exception is thrown.
     * On local dev or Docker with the tools installed, this squeezes the
     * already-GD-compressed file even further.
     */
    private function cliOptimize(string $path): void
    {
        try {
            $chain = OptimizerChainFactory::create();
            $chain->optimize($path);
        } catch (\Throwable $e) {
            // Silently swallow — CLI tools are optional
            report($e);
        }
    }
}
