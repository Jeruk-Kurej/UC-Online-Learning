<?php

namespace App\Traits;

use App\Support\GoogleDrive;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasImage
{
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
                }
            }
        });
    }

    public static function deleteCloudinaryImage(?string $url)
    {
        if (! $url || ! str_contains($url, 'cloudinary.com')) {
            return;
        }

        try {
            $publicId = self::getCloudinaryPublicId($url);
            if ($publicId) {
                Cloudinary::uploadApi()->destroy($publicId);
                Log::info("[HasImage] Successfully deleted Cloudinary asset: {$publicId}");
            }
        } catch (\Throwable $e) {
            Log::warning('[HasImage] Failed to delete Cloudinary asset: '.$e->getMessage());
        }
    }

    public static function getCloudinaryPublicId(?string $url): ?string
    {
        if (! $url || ! str_contains($url, 'cloudinary.com')) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! $path) {
            return null;
        }

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
     * Whether a resolved image URL is a real image rather than the placeholder below.
     */
    protected function isRealImage(?string $url): bool
    {
        return $url && ! str_contains($url, rawurlencode(self::PLACEHOLDER_MARK));
    }

    // --- Placeholder: the first letter on brand orange, drawn here so no name is sent to another site ---

    private const PLACEHOLDER_MARK = 'data-placeholder="uco"';

    protected function placeholderImage(): string
    {
        $initial = e(mb_strtoupper(mb_substr(trim((string) $this->name), 0, 1)) ?: 'U');

        return 'data:image/svg+xml,'.rawurlencode(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" '.self::PLACEHOLDER_MARK.'>'
            .'<rect width="64" height="64" fill="#a65300"/>'
            .'<text x="32" y="32" dy=".35em" text-anchor="middle" font-family="Arial, sans-serif" font-size="30" font-weight="600" fill="#ffffff">'.$initial.'</text>'
            .'</svg>'
        );
    }

    /**
     * Resolve image URL handling Google Drive, Local Storage, and External URLs.
     * Falls back to the placeholder when there is no usable image (an old UI-Avatars address counts as none).
     */
    protected function resolveImage(?string $path, string $type = 'business'): string
    {
        $fallback = $this->placeholderImage();

        if (! $path) {
            return $fallback;
        }

        $path = preg_replace('/<br\s*\/?>/i', ' ', $path);
        $path = trim(strip_tags($path));

        if (! $path || str_contains($path, 'ui-avatars.com')) {
            return $fallback;
        }

        // --- 1. Google Drive links ---
        if (GoogleDrive::isDriveUrl($path)) {
            $id = GoogleDrive::fileId($path);

            return $id ? GoogleDrive::thumbnailUrl($id, 'w1000') : $path;
        }

        // --- 2. Absolute URLs ---
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // --- 3. Local storage paths (relative to storage/app/public, without /storage/) ---
        $normalizedPath = $path;
        if (str_starts_with($normalizedPath, '/storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('/storage/'));
        } elseif (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($normalizedPath)) {
            return Storage::disk('public')->url($normalizedPath);
        }

        if ($normalizedPath !== $path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $fallback;
    }

    public function deleteFileFromStorage(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
            return;
        }

        if (str_contains($pathOrUrl, 'cloudinary.com')) {
            try {
                self::deleteCloudinaryImage($pathOrUrl);
            } catch (\Throwable $e) {
            }

            return;
        }

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
