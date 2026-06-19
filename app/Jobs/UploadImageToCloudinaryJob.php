<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UploadImageToCloudinaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $column;
    protected $url;
    protected $folder;
    protected $identifier;

    /**
     * Create a new job instance.
     */
    public function __construct($model, string $column, string $url, string $folder, ?string $identifier)
    {
        $this->model = $model;
        $this->column = $column;
        $this->url = $url;
        $this->folder = $folder;
        $this->identifier = $identifier;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new \Illuminate\Queue\Middleware\RateLimited('image-uploads')];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = $this->cleanUrl($this->url);
        if (!$url) return;

        // If already Cloudinary URL, skip
        if (str_contains($url, 'cloudinary.com') || str_contains($url, 'res.cloudinary.com')) {
            return;
        }

        $tmpFile = null;
        try {
            Log::info("[UploadImageToCloudinaryJob] Starting async download: {$url} for model " . get_class($this->model) . " (ID: {$this->model->id})");

            // Rate-limit safeguard to avoid hammering external servers during concurrent imports
            usleep(random_int(100000, 300000));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, (string)$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $curlHeaders = [
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer: https://employee.uc.ac.id/index.php/login',
            ];
            
            $isUniversityPortal = str_contains($url, 'employee.uc.ac.id') || str_contains($url, 'employee.ciputra.ac.id');
            if ($isUniversityPortal) {
                $cookie = config('services.uc.cookie_raw', '');
                if ($cookie) {
                    $curlHeaders[] = "Cookie: " . trim($cookie);
                }
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            // Fallback for employee redirection
            if ($isUniversityPortal && str_contains(strtolower($contentType ?? ''), 'text/html')) {
                $fallbackUrl = str_replace('employee.uc.ac.id', 'employee.ciputra.ac.id', $url);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fallbackUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
                $contents = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                curl_close($ch);
            }

            if ($contents === false || $httpCode !== 200 || strlen($contents) < 50 || !str_contains(strtolower($contentType ?? ''), 'image')) {
                Log::warning("[UploadImageToCloudinaryJob] Download failed or not an image. HTTP: {$httpCode}, Type: {$contentType}");
                return;
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'uco_img_');
            file_put_contents($tmpFile, $contents);
            unset($contents);

            // Compress WebP
            $tmpFile = $this->compressToWebp($tmpFile);

            $sanitizedId = Str::slug($this->identifier ?? 'unknown');
            $urlHash = substr(md5($url), 0, 8);
            $publicId = "uco/{$this->folder}/{$sanitizedId}_{$urlHash}";

            $uploadResult = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($tmpFile, [
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image'
            ]);

            if ($tmpFile && file_exists($tmpFile)) {
                unlink($tmpFile);
            }

            if (isset($uploadResult['secure_url'])) {
                $this->model->update([$this->column => $uploadResult['secure_url']]);
                Log::info("[UploadImageToCloudinaryJob] Successfully uploaded to Cloudinary: " . $uploadResult['secure_url']);
            }

        } catch (\Throwable $e) {
            if ($tmpFile && file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            Log::error("[UploadImageToCloudinaryJob] Failed to process image: " . $e->getMessage());
        }
    }

    private function compressToWebp(string $filePath, int $quality = 80): string
    {
        if (!extension_loaded('gd')) return $filePath;

        try {
            $imageInfo = @getimagesize($filePath);
            if (!$imageInfo) return $filePath;

            $mime = $imageInfo['mime'];
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = @imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $image = @imagecreatefromgif($filePath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($filePath);
                    break;
                default:
                    $data = file_get_contents($filePath);
                    $image = @imagecreatefromstring($data);
                    break;
            }

            if (!$image) return $filePath;

            if (!imageistruecolor($image)) {
                $trueColorImage = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagealphablending($trueColorImage, false);
                imagesavealpha($trueColorImage, true);
                imagecopy($trueColorImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $trueColorImage;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxDim = 1200;
            if ($width > $maxDim || $height > $maxDim) {
                if ($width > $height) {
                    $newWidth = $maxDim;
                    $newHeight = (int)($height * ($maxDim / $width));
                } else {
                    $newHeight = $maxDim;
                    $newWidth = (int)($width * ($maxDim / $height));
                }
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resizedImage;
            }

            $outputPath = $filePath . '.webp';
            if (imagewebp($image, $outputPath, $quality)) {
                imagedestroy($image);
                unlink($filePath);
                return $outputPath;
            }
            imagedestroy($image);
        } catch (\Throwable $e) {
            Log::warning("[UploadImageToCloudinaryJob] WebP compression failed: " . $e->getMessage());
        }

        return $filePath;
    }

    private function cleanUrl(?string $url): ?string
    {
        if (!$url) return null;
        $url = strip_tags($url);
        $url = preg_replace('/[^\x20-\x7E]/', '', $url);
        $url = trim($url);

        if (preg_match('/(https?:\/\/.*)$/i', $url, $matches)) {
            $url = $matches[1];
        }

        if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
            if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]+)/', $url, $matches)) {
                $url = "https://drive.google.com/uc?export=download&confirm=t&id=" . $matches[1];
            }
        }

        return (filter_var($url, FILTER_VALIDATE_URL)) ? $url : null;
    }
}
