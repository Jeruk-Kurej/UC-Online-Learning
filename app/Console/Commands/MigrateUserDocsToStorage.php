<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MigrateUserDocsToStorage extends Command
{
    protected $signature = 'migrate:user-docs {--limit=0} {--user=} {--dry-run}';
    protected $description = 'Migrate Google Drive links in user activities and certifications to self-hosted WEBP/PDFs on local storage.';

    public function handle()
    {
        ini_set('memory_limit', '512M');
        $limit = (int) $this->option('limit');
        $userId = $this->option('user');
        $dryRun = $this->option('dry-run');

        $query = User::where(function ($q) {
            $q->whereNotNull('activities_doc_url')
              ->orWhereNotNull('expertise_certification_url');
        });

        if ($userId) {
            $query->where('id', $userId);
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $users = $query->get();
        $this->info("Found {$users->count()} users to process.");

        $manager = new ImageManager(new Driver());

        foreach ($users as $user) {
            $this->info("Processing User ID: {$user->id} ({$user->name})");

            $updated = false;

            // Process activities_doc_url
            if (!empty($user->activities_doc_url)) {
                $newUrls = $this->processUrls($user->activities_doc_url, "users/{$user->id}/activities", $manager, $dryRun);
                if ($newUrls !== (is_array($user->activities_doc_url) ? implode(';', $user->activities_doc_url) : $user->activities_doc_url)) {
                    $user->activities_doc_url = $newUrls;
                    $updated = true;
                }
            }

            // Process expertise_certification_url
            if (!empty($user->expertise_certification_url)) {
                $newUrls = $this->processUrls($user->expertise_certification_url, "users/{$user->id}/certs", $manager, $dryRun);
                if ($newUrls !== (is_array($user->expertise_certification_url) ? implode(';', $user->expertise_certification_url) : $user->expertise_certification_url)) {
                    $user->expertise_certification_url = $newUrls;
                    $updated = true;
                }
            }

            if ($updated && !$dryRun) {
                $user->save();
                $this->info("  [✓] Updated database for User ID {$user->id}");
            }
        }

        $this->info('Migration complete.');
    }

    private function processUrls($urlsInput, string $folder, ImageManager $manager, bool $dryRun): string
    {
        if (is_array($urlsInput)) {
            $urls = $urlsInput;
        } else {
            $urls = array_filter(array_map('trim', preg_split('/[;,]+/', $urlsInput)));
        }
        $processedUrls = [];

        foreach ($urls as $url) {
            // Already self-hosted? Keep it.
            if (str_starts_with($url, '/storage/') || str_contains($url, env('APP_URL'))) {
                $this->line("  [Skip] Already self-hosted: {$url}");
                $processedUrls[] = $url;
                continue;
            }

            $downloadUrl = $url;
            $isGoogleDrive = false;

            if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
                if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]+)/', $url, $matches)) {
                    $downloadUrl = "https://drive.google.com/uc?export=download&confirm=t&id=" . $matches[1];
                    $isGoogleDrive = true;
                }
            }

            $this->line("  [Download] Fetching: {$downloadUrl}");
            $response = Http::timeout(60)->get($downloadUrl);

            if (!$response->ok() || strlen($response->body()) < 100) {
                $this->error("  [Error] Failed to download or file too small: {$downloadUrl}");
                $processedUrls[] = $url; // keep original if failed
                continue;
            }

            $contents = $response->body();
            $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
            
            // Refine content type for Google Drive generic binary type
            if ($isGoogleDrive && str_contains(strtolower($contentType), 'application/octet-stream')) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $detectedType = $finfo->buffer($contents);
                if ($detectedType) {
                    $contentType = $detectedType;
                }
            }

            $isImage = str_contains(strtolower($contentType), 'image');
            $isPdf = str_contains(strtolower($contentType), 'pdf');

            $extension = 'bin';
            $finalContents = $contents;

            if ($isImage) {
                try {
                    $image = $manager->decode($contents);
                    $image->scaleDown(1200, 1200);
                    $webp = $image->encodeUsingFileExtension('webp', 80);
                    $finalContents = $webp->toString();
                    $extension = 'webp';
                    $this->line("  [Process] Converted to WEBP");
                } catch (\Exception $e) {
                    $this->error("  [Error] Failed to process image: {$e->getMessage()}");
                    // fallback to original
                    $extension = 'jpg'; 
                }
            } else {
                if ($isPdf) {
                    $extension = 'pdf';
                }
                $this->line("  [Process] Saved as {$extension}");
            }

            $filename = Str::random(20) . '.' . $extension;
            $storagePath = "{$folder}/{$filename}";

            if ($dryRun) {
                $this->info("  [Dry-Run] Would upload {$storagePath}");
                $processedUrls[] = $url;
                continue;
            }

            try {
                // Save to public disk
                Storage::disk('public')->put($storagePath, $finalContents);
                $newUrl = Storage::disk('public')->url($storagePath);
                
                $processedUrls[] = $newUrl;
                $this->info("  [Uploaded] {$newUrl}");
            } catch (\Exception $e) {
                $this->error("  [Error] Storage upload failed: {$e->getMessage()}");
                $processedUrls[] = $url; // keep original if failed
            }
        }

        return implode(';', $processedUrls);
    }
}
