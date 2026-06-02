<?php

use App\Services\ImageOptimizerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ─────────────────────────────────────────────────────────────────────────────
// Safety / skip checks
// ─────────────────────────────────────────────────────────────────────────────

test('it skips non-image files like PDFs', function () {
    $service = new ImageOptimizerService();

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    expect(fn () => $service->optimizeUploadedFile($file))->not->toThrow(Throwable::class);
});

test('it skips SVG files', function () {
    $service = new ImageOptimizerService();

    $file = UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml');

    expect(fn () => $service->optimizeUploadedFile($file))->not->toThrow(Throwable::class);
});

test('it does not throw for a fake UploadedFile JPEG', function () {
    $service = new ImageOptimizerService();

    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    expect(fn () => $service->optimizeUploadedFile($file))->not->toThrow(Throwable::class);
});

// ─────────────────────────────────────────────────────────────────────────────
// GD compression — these verify the PHP-native path that runs on Vercel
// ─────────────────────────────────────────────────────────────────────────────

test('GD compresses a JPEG and reduces its file size', function () {
    $service = new ImageOptimizerService();

    // Create a real JPEG saved at 100% quality (largest possible)
    $tempPath = tempnam(sys_get_temp_dir(), 'uco_test_') . '.jpg';
    $img = imagecreatetruecolor(400, 400);
    for ($i = 0; $i < 400; $i += 5) {
        imageline($img, 0, $i, 400, 400 - $i, imagecolorallocate($img, $i % 255, 100, 200));
    }
    imagejpeg($img, $tempPath, 100); // 100% quality = biggest possible file
    imagedestroy($img);

    $sizeBefore = filesize($tempPath);

    $service->optimize($tempPath);

    $sizeAfter = filesize($tempPath);
    @unlink($tempPath);

    // GD re-encodes at 78% quality — should always be smaller
    expect($sizeAfter)->toBeLessThan($sizeBefore);
})->skipOnWindows();

test('GD compresses a PNG and reduces its file size', function () {
    $service = new ImageOptimizerService();

    // Write a PNG at compression level 0 (no compression, largest file)
    $tempPath = tempnam(sys_get_temp_dir(), 'uco_test_') . '.png';
    $img = imagecreatetruecolor(400, 400);
    for ($i = 0; $i < 400; $i += 5) {
        imageline($img, 0, $i, 400, 400 - $i, imagecolorallocate($img, $i % 255, 120, 180));
    }
    imagepng($img, $tempPath, 0); // 0 = no zlib compression
    imagedestroy($img);

    $sizeBefore = filesize($tempPath);

    $service->optimize($tempPath);

    $sizeAfter = filesize($tempPath);
    @unlink($tempPath);

    // GD saves at compression level 6 — should always be smaller
    expect($sizeAfter)->toBeLessThan($sizeBefore);
})->skipOnWindows();

test('GD compresses a WebP and reduces its file size', function () {
    $service = new ImageOptimizerService();

    // Write a WebP at 100% quality
    $tempPath = tempnam(sys_get_temp_dir(), 'uco_test_') . '.webp';
    $img = imagecreatetruecolor(400, 400);
    for ($i = 0; $i < 400; $i += 5) {
        imageline($img, 0, $i, 400, 400 - $i, imagecolorallocate($img, $i % 255, 80, 220));
    }
    imagewebp($img, $tempPath, 100);
    imagedestroy($img);

    $sizeBefore = filesize($tempPath);

    $service->optimize($tempPath);

    $sizeAfter = filesize($tempPath);
    @unlink($tempPath);

    expect($sizeAfter)->toBeLessThan($sizeBefore);
})->skipOnWindows();

// ─────────────────────────────────────────────────────────────────────────────
// CLI optimizer (Pass 2) — conditional on jpegoptim being installed
// ─────────────────────────────────────────────────────────────────────────────

test('CLI optimizer further reduces JPEG if jpegoptim is installed', function () {
    $service = new ImageOptimizerService();

    $hasJpegoptim = ! empty(trim(shell_exec('which jpegoptim 2>/dev/null')));
    if (! $hasJpegoptim) {
        $this->markTestSkipped('jpegoptim not installed — skipping CLI optimizer test. Run: brew install jpegoptim');
    }

    $tempPath = tempnam(sys_get_temp_dir(), 'uco_test_') . '.jpg';
    $img = imagecreatetruecolor(800, 800);
    for ($i = 0; $i < 800; $i += 3) {
        imageline($img, 0, $i, 800, 800 - $i, imagecolorallocate($img, $i % 255, 100, 200));
    }
    imagejpeg($img, $tempPath, 95);
    imagedestroy($img);

    $sizeBefore = filesize($tempPath);
    $service->optimize($tempPath);
    $sizeAfter = filesize($tempPath);
    @unlink($tempPath);

    expect($sizeAfter)->toBeLessThanOrEqual($sizeBefore);
});

test('GD resizes large images to max 1200px and keeps size well under 500KB', function () {
    $service = new ImageOptimizerService();

    // Create a very large mock image (3000 x 2000 pixels)
    $tempPath = tempnam(sys_get_temp_dir(), 'uco_large_') . '.jpg';
    $img = imagecreatetruecolor(3000, 2000);
    // Draw simple background
    imagefilledrectangle($img, 0, 0, 3000, 2000, imagecolorallocate($img, 240, 240, 240));
    // Draw some lines to add detail
    for ($i = 0; $i < 2000; $i += 50) {
        imageline($img, 0, $i, 3000, $i, imagecolorallocate($img, 100, 150, 200));
    }
    imagejpeg($img, $tempPath, 95); // Save at high quality
    imagedestroy($img);

    $originalSize = filesize($tempPath);

    // Optimize
    $service->optimize($tempPath);

    // Get optimized dimensions and size
    $info = getimagesize($tempPath);
    $finalSize = filesize($tempPath);
    @unlink($tempPath);

    expect($info)->not->toBeFalse();
    $width = $info[0];
    $height = $info[1];

    // Verify it was resized proportionally (width should be 1200, height 800)
    expect($width)->toBe(1200);
    expect($height)->toBe(800);

    // Verify final size is well under 500KB (512,000 bytes)
    expect($finalSize)->toBeLessThan(512000);
})->skipOnWindows();

