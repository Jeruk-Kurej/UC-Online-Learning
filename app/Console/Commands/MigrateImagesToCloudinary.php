<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'migrate:images-to-cloudinary
                            {--tables= : Comma-separated table.column pairs (default: users.profile_photo_url,businesses.logo_url,companies.logo_url)}
                            {--limit=0 : Maximum rows per table to process (0 = no limit)}
                            {--dry-run : Do not write to disk or update database}
                            {--domain= : Only process URLs that contain this domain (optional)}
                            {--include-local : Also upload local extracted_images/ paths to Cloudinary}';

    protected $description = 'Download remote image URLs (with cookie auth for employee.uc.ac.id) and upload them to Cloudinary. Updates DB columns to new Cloudinary URL.';

    public function handle()
    {
        $disk = config('filesystems.default', config('filesystems.disks.public.driver', 'public'));
        $tablesOption = $this->option('tables') ?? 'users.profile_photo_url,businesses.logo_url,companies.logo_url';
        $tables = array_map('trim', explode(',', $tablesOption));
        $limit = (int) $this->option('limit');
        $dry = $this->option('dry-run');
        $domain = $this->option('domain');
        $includeLocal = $this->option('include-local');

        // Load UC cookies from config for authenticated downloads
        $ucCookieRaw = config('services.uc.cookie_raw', '');

        $this->info("Using disk: {$disk}");
        if ($ucCookieRaw) {
            $this->info("UC cookies loaded for employee.uc.ac.id auth.");
        }

        $stats = ['uploaded' => 0, 'skipped' => 0, 'failed' => 0];

        foreach ($tables as $pair) {
            if (!str_contains($pair, '.')) {
                $this->error("Invalid table.column format: {$pair}");
                continue;
            }

            [$table, $column] = explode('.', $pair, 2);

            // Build query: remote URLs + optionally local extracted_images paths
            $query = DB::table($table)->where(function ($q) use ($column, $includeLocal) {
                $q->where($column, 'like', 'http%');
                if ($includeLocal) {
                    $q->orWhere($column, 'like', 'extracted_images/%');
                }
            });

            if ($domain) {
                $query->where($column, 'like', "%{$domain}%");
            }
            if ($limit > 0) {
                $query->limit($limit);
            }

            $rows = $query->get();
            $count = $rows->count();
            $this->info("Found {$count} rows in {$table}.{$column}");

            foreach ($rows as $row) {
                $id = $row->id ?? null;
                $url = $row->{$column} ?? null;
                if (!$url) {
                    $stats['skipped']++;
                    continue;
                }

                // Skip already-cloudinary URLs
                if (str_contains($url, 'cloudinary.com') || str_contains($url, 'res.cloudinary.com')) {
                    $this->line("  ⏩ id={$id} already on Cloudinary");
                    $stats['skipped']++;
                    continue;
                }

                // Clean and convert Google Drive viewer links to direct download links
                if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
                    if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]+)/', $url, $matches)) {
                        $url = "https://drive.google.com/uc?export=download&confirm=t&id=" . $matches[1];
                    }
                }

                try {
                    $contents = null;
                    $basename = null;

                    // Case 1: Local file from extracted_images/
                    if (str_starts_with($url, 'extracted_images/')) {
                        $localPath = storage_path('app/public/' . $url);
                        if (!file_exists($localPath)) {
                            $this->error("  ❌ id={$id} local file missing: {$localPath}");
                            $stats['failed']++;
                            continue;
                        }
                        $contents = file_get_contents($localPath);
                        $basename = basename($url);
                        $this->line("  📁 id={$id} reading local: {$basename}");
                    }
                    // Case 2: employee.uc.ac.id or employee.ciputra.ac.id (needs cookies)
                    elseif ((str_contains($url, 'employee.uc.ac.id') || str_contains($url, 'employee.ciputra.ac.id')) && $ucCookieRaw) {
                        $this->line("  🔐 id={$id} downloading with cookies: {$url}");
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, (string)$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "Cookie: " . trim($ucCookieRaw),
                            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
                            "Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8",
                            "Referer: https://employee.uc.ac.id/index.php/login",
                            "Connection: keep-alive"
                        ]);
                        
                        $contents = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

                        // FALLBACK: If we got HTML from employee.uc.ac.id, try swapping to employee.ciputra.ac.id
                        if (str_contains($url, 'employee.uc.ac.id') && str_contains(strtolower($contentType ?? ''), 'text/html')) {
                            $fallbackUrl = str_replace('employee.uc.ac.id', 'employee.ciputra.ac.id', $url);
                            $this->line("    🔄 HTML detected on uc.ac.id. Trying fallback to ciputra.ac.id...");
                            
                            curl_setopt($ch, CURLOPT_URL, $fallbackUrl);
                            $contents = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                        }

                        curl_close($ch);

                        if ($httpCode !== 200 || !$contents) {
                            $this->error("  ❌ id={$id} HTTP {$httpCode}");
                            $stats['failed']++;
                            continue;
                        }

                        if (!str_contains(strtolower($contentType ?? ''), 'image')) {
                            $this->error("  ❌ id={$id} Not an image (Type: {$contentType})");
                            $stats['failed']++;
                            continue;
                        }

                        $basename = basename(parse_url($url, PHP_URL_PATH) ?? 'image.jpg');
                    }
                    // Case 3: Regular public URL
                    else {
                        $this->line("  🌐 id={$id} downloading: {$url}");
                        $response = Http::timeout(30)->get($url);
                        if (!$response->ok()) {
                            $this->error("  ❌ id={$id} HTTP {$response->status()}");
                            $stats['failed']++;
                            continue;
                        }
                        
                        $contentType = $response->header('Content-Type');
                        if (!str_contains(strtolower($contentType ?? ''), 'image')) {
                            $this->error("  ❌ id={$id} Not an image (Type: {$contentType}). File is likely private / requires Google login.");
                            $stats['failed']++;
                            continue;
                        }
                        
                        $contents = $response->body();
                        $basename = basename(parse_url($url, PHP_URL_PATH) ?? 'image.jpg');
                    }

                    if (!$contents || strlen($contents) < 100) {
                        $this->error("  ❌ id={$id} empty or too small response");
                        $stats['failed']++;
                        continue;
                    }

                    // Sanitize filename
                    $sanitized = preg_replace('/[^A-Za-z0-9_.-]/', '_', $basename);
                    $targetPath = "uco/{$table}/{$id}/{$sanitized}";

                    if ($dry) {
                        $this->info("  [dry-run] would store → {$disk}:{$targetPath}");
                        continue;
                    }

                    // Upload to Cloudinary (or configured disk)
                    if ($disk === 'cloudinary') {
                        $tempFile = tempnam(sys_get_temp_dir(), 'uco_mig_');
                        file_put_contents($tempFile, $contents);
                        
                        // Compress to WebP & Resize to max 1200px before uploading
                        $tempFile = $this->compressToWebp($tempFile);
                        
                        try {
                            $uploadResult = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($tempFile, [
                                'folder' => "uco/{$table}/{$id}",
                                'public_id' => pathinfo($sanitized, PATHINFO_FILENAME),
                                'overwrite' => true,
                                'resource_type' => 'image'
                            ]);
                            $newUrl = $uploadResult['secure_url'];
                            $stored = isset($newUrl);
                        } catch (\Exception $e) {
                            $this->error("  ❌ id={$id} Cloudinary Error: " . $e->getMessage());
                            $stored = false;
                        } finally {
                            if ($tempFile && file_exists($tempFile)) unlink($tempFile);
                        }
                    } else {
                        $stored = Storage::disk($disk)->put($targetPath, $contents);
                        $newUrl = Storage::disk($disk)->url($targetPath);
                    }

                    if (!$stored) {
                        $this->error("  ❌ id={$id} failed to store on {$disk}");
                        $stats['failed']++;
                        continue;
                    }

                    // Update DB with the new URL
                    DB::table($table)->where('id', $id)->update([$column => $newUrl]);
                    $this->info("  ✅ id={$id} → {$newUrl}");
                    $stats['uploaded']++;

                } catch (Exception $e) {
                    $this->error("  ❌ id={$id}: " . $e->getMessage());
                    $stats['failed']++;
                }
            }
        }

        $this->newLine();
        $this->info("Done! Uploaded: {$stats['uploaded']} | Skipped: {$stats['skipped']} | Failed: {$stats['failed']}");
        return 0;
    }

    /**
     * Compress image to WebP and resize to a maximum of 1200px.
     */
    private function compressToWebp(string $filePath, int $quality = 80): string
    {
        ini_set('memory_limit', '512M');
        if (!extension_loaded('gd')) {
            return $filePath;
        }

        try {
            $imageInfo = @getimagesize($filePath);
            if (!$imageInfo) {
                return $filePath;
            }

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

            if (!$image) {
                return $filePath;
            }

            // Convert palette images to truecolor to prevent "Palette image not supported by webp" error
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
            $this->warn("Image compression to webp failed: " . $e->getMessage());
        }

        return $filePath;
    }
}
