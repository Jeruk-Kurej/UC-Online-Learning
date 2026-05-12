<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasImage
{
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
}
