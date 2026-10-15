<?php

namespace App\Support;

/**
 * Google Drive share links: recognising them, reading the file id, and the addresses Drive serves a file under.
 */
final class GoogleDrive
{
    public static function isDriveUrl(string $url): bool
    {
        return str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com');
    }

    /**
     * File id from open?id=ID, file/d/ID/view or docs.google.com/file/d/ID/edit; shorter ids than $minLength are ignored.
     */
    public static function fileId(string $url, int $minLength = 25): ?string
    {
        return preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{'.$minLength.',})/', $url, $matches) ? $matches[1] : null;
    }

    /**
     * Thumbnail API: skips the virus-scan page and can be embedded in <img>.
     */
    public static function thumbnailUrl(string $id, string $size): string
    {
        return "https://drive.google.com/thumbnail?id={$id}&sz={$size}";
    }

    public static function viewUrl(string $id): string
    {
        return 'https://drive.google.com/uc?export=view&id='.$id;
    }

    public static function previewUrl(string $id): string
    {
        return "https://drive.google.com/file/d/{$id}/preview";
    }

    public static function downloadUrl(string $id): string
    {
        return 'https://drive.google.com/uc?export=download&confirm=t&id='.$id;
    }
}
