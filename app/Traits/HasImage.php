<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait HasImage
{
    /**
     * Boot the trait to automatically delete Cloudinary images when models are deleted.
     */
    public static function bootHasImage()
    {
        static::deleting(function ($model) {
            $fields = ['profile_photo_url', 'logo_url', 'photo_url'];
            foreach ($fields as $field) {
                try {
                    $value = $model->getRawOriginal($field);
                    if ($value) {
                        self::deleteCloudinaryImage($value);
                    }
                } catch (\Throwable $e) {
                    // Ignore if field doesn't exist on the model
                }
            }
        });
    }

    /**
     * Delete an image from Cloudinary if the URL points to Cloudinary.
     */
    public static function deleteCloudinaryImage(?string $url)
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) {
            return;
        }

        try {
            $publicId = self::getCloudinaryPublicId($url);
            if ($publicId) {
                \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
                Log::info("[HasImage] Successfully deleted Cloudinary asset: {$publicId}");
            }
        } catch (\Throwable $e) {
            Log::warning("[HasImage] Failed to delete Cloudinary asset: " . $e->getMessage());
        }
    }

    /**
     * Extract public ID from Cloudinary URL.
     */
    public static function getCloudinaryPublicId(?string $url): ?string
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) return null;

        $parts = explode('/', trim($path, '/'));
        $uploadIndex = array_search('upload', $parts);
        if ($uploadIndex === false) {
            return null;
        }

        $startIndex = $uploadIndex + 1;
        if (isset($parts[$startIndex]) && str_starts_with($parts[$startIndex], 'v') && is_numeric(substr($parts[$startIndex], 1))) {
            $startIndex++;
        }

        $publicIdWithExt = implode('/', array_slice($parts, $startIndex));
        return preg_replace('/\.[^.]+$/', '', $publicIdWithExt);
    }

    /**
     * Resolve image URL handling Google Drive, Local Storage, and External URLs.
     * Fallback to UI-Avatars if no valid image found.
     */
    protected function resolveImage(?string $path, string $type = 'business'): string
    {
        $name = $this->name ?? 'UCO';
        $fallback = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&color=FFFFFF&background=F97316";

        if (!$path) {
            return $fallback;
        }

        // Clean path (remove <br>, strip tags, trim)
        $path = preg_replace('/<br\s*\/?>/i', ' ', $path);
        $path = trim(strip_tags($path));

        if (!$path) {
            return $fallback;
        }

        // 1. Handle Google Drive Links
        if (Str::contains($path, ['drive.google.com', 'docs.google.com'])) {
            return $this->convertGoogleDriveLink($path);
        }

        // 2. Handle Absolute URLs (External)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // 3. Handle Local Storage Paths
        // Normalize: strip leading /storage/ prefix since disk('public') paths
        // are relative to storage/app/public (e.g. 'profile-photos/file.jpg')
        $normalizedPath = $path;
        if (str_starts_with($normalizedPath, '/storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('/storage/'));
        } elseif (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return Storage::disk('public')->url($normalizedPath);
        }

        // Also check the original path as a last resort
        if ($normalizedPath !== $path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $fallback;
    }

    /**
     * Convert Google Drive sharing link to a direct image link.
     */
    private function convertGoogleDriveLink(string $url): string
    {
        // Extract ID from various Google Drive URL formats:
        // - drive.google.com/open?id=ID
        // - drive.google.com/file/d/ID/view
        // - docs.google.com/file/d/ID/edit
        $id = '';
        if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{25,})/', $url, $matches)) {
            $id = $matches[1];
        }

        if ($id) {
            // Use the undocumented thumbnail API which bypasses virus scan warnings and allows <img> embedding
            return "https://drive.google.com/thumbnail?id={$id}&sz=w1000";
        }

        return $url;
    }

    /**
     * Safely delete a file from local public storage or Cloudinary.
     */
    public function deleteFileFromStorage(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
            return;
        }

        // Handle Cloudinary URL
        if (str_contains($pathOrUrl, 'cloudinary.com')) {
            try {
                self::deleteCloudinaryImage($pathOrUrl);
            } catch (\Throwable $e) {
                // silently swallow
            }
            return;
        }

        // Normalize local storage path
        $relativePath = $pathOrUrl;
        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            $relativePath = parse_url($relativePath, PHP_URL_PATH) ?? $relativePath;
        }

        if (str_starts_with($relativePath, '/storage/')) {
            $relativePath = substr($relativePath, strlen('/storage/'));
        } elseif (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, strlen('storage/'));
        }

        $relativePath = ltrim($relativePath, '/');

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
