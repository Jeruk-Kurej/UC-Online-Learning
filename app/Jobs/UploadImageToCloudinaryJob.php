<?php

namespace App\Jobs;

use App\Services\ImageOptimizerService;
use App\Support\GoogleDrive;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadImageToCloudinaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    protected $column;

    protected $url;

    protected $folder;

    protected $identifier;

    public function __construct($model, string $column, string $url, string $folder, ?string $identifier)
    {
        $this->model = $model;
        $this->column = $column;
        $this->url = $url;
        $this->folder = $folder;
        $this->identifier = $identifier;
    }

    public function middleware(): array
    {
        return [new RateLimited('image-uploads')];
    }

    public function handle(): void
    {
        $url = $this->cleanUrl($this->url);
        if (! $url || str_contains($url, 'cloudinary.com')) {
            return;
        }

        $tmpFile = null;
        try {
            Log::info("[UploadImageToCloudinaryJob] Starting async download: {$url} for model ".get_class($this->model)." (ID: {$this->model->id})");

            // --- Rate limit so concurrent imports do not hammer external servers ---
            usleep(random_int(100000, 300000));

            [$contents, $httpCode, $contentType] = $this->download($url);
            if ($contents === false || $httpCode !== 200 || strlen($contents) < 50 || ! str_contains(strtolower($contentType ?? ''), 'image')) {
                Log::warning("[UploadImageToCloudinaryJob] Download failed or not an image. HTTP: {$httpCode}, Type: {$contentType}");

                return;
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'uco_img_');
            file_put_contents($tmpFile, $contents);
            unset($contents);

            $tmpFile = $this->compress($tmpFile);

            $uploadResult = Cloudinary::uploadApi()->upload($tmpFile, [
                'public_id' => $this->publicId($url),
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            if (isset($uploadResult['secure_url'])) {
                $this->model->update([$this->column => $uploadResult['secure_url']]);
                Log::info('[UploadImageToCloudinaryJob] Successfully uploaded to Cloudinary: '.$uploadResult['secure_url']);
            }
        } catch (\Throwable $e) {
            Log::error('[UploadImageToCloudinaryJob] Failed to process image: '.$e->getMessage());
        } finally {
            if ($tmpFile && file_exists($tmpFile)) {
                unlink($tmpFile);
            }
        }
    }

    // --- Download ---

    /**
     * Body, HTTP status and content type of the image; a university portal link that answers with its login page is
     * retried on the employee.ciputra.ac.id host.
     *
     * @return array{0: string|false, 1: int, 2: string|null}
     */
    private function download(string $url): array
    {
        $headers = $this->requestHeaders($url);
        $response = $this->fetch($url, $headers);

        if ($this->isUniversityPortal($url) && str_contains(strtolower($response[2] ?? ''), 'text/html')) {
            $response = $this->fetch(str_replace('employee.uc.ac.id', 'employee.ciputra.ac.id', $url), $headers);
        }

        return $response;
    }

    private function fetch(string $url, array $headers): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, $this->curlOptions($url, $headers));
        $contents = curl_exec($ch);

        if ($contents === false) {
            Log::warning('[UploadImageToCloudinaryJob] Download error: '.curl_error($ch));
        }

        return [$contents, curl_getinfo($ch, CURLINFO_HTTP_CODE), curl_getinfo($ch, CURLINFO_CONTENT_TYPE)];
    }

    /**
     * The server certificate is checked (curl's default); only http and https are followed, up to five redirects.
     */
    private function curlOptions(string $url, array $headers): array
    {
        return [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ];
    }

    private function requestHeaders(string $url): array
    {
        $headers = [
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
        ];

        if (! $this->isUniversityPortal($url)) {
            return $headers;
        }

        $headers[] = 'Referer: https://employee.uc.ac.id/index.php/login';

        if ($cookie = config('services.uc.cookie_raw', '')) {
            $headers[] = 'Cookie: '.trim($cookie);
        }

        return $headers;
    }

    /**
     * Only a link whose host is the portal itself gets the portal's referer and session cookie.
     */
    private function isUniversityPortal(string $url): bool
    {
        return in_array(strtolower((string) parse_url($url, PHP_URL_HOST)), ['employee.uc.ac.id', 'employee.ciputra.ac.id'], true);
    }

    // --- Upload ---

    private function compress(string $path): string
    {
        try {
            return app(ImageOptimizerService::class)->convertToWebp($path);
        } catch (\Throwable $e) {
            Log::warning('[UploadImageToCloudinaryJob] WebP compression failed: '.$e->getMessage());

            return $path;
        }
    }

    private function publicId(string $url): string
    {
        $sanitizedId = Str::slug($this->identifier ?? 'unknown');
        $urlHash = substr(md5($url), 0, 8);

        return "uco/{$this->folder}/{$sanitizedId}_{$urlHash}";
    }

    // --- Link cleaning: text around the link removed, Google Drive links turned into direct downloads ---

    private function cleanUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $url = strip_tags($url);
        $url = preg_replace('/[^\x20-\x7E]/', '', $url);
        $url = trim($url);

        if (preg_match('/(https?:\/\/.*)$/i', $url, $matches)) {
            $url = $matches[1];
        }

        if (GoogleDrive::isDriveUrl($url) && ($id = GoogleDrive::fileId($url, 1))) {
            $url = GoogleDrive::downloadUrl($id);
        }

        return (filter_var($url, FILTER_VALIDATE_URL)) ? $url : null;
    }
}
