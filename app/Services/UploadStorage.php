<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

/**
 * Where the files uploaded through the forms (photos, logos, documents) are kept: the `public` disk by default, or
 * Cloudinary when UPLOADS_DISK=cloudinary, for hosts whose own disk does not last (such as Vercel). Returns the value
 * for the database: a /storage path or the Cloudinary address, both of which HasImage reads and deletes.
 */
class UploadStorage
{
    public function store(UploadedFile $file, string $directory, ?string $name = null): string
    {
        $name ??= $file->hashName();

        if (config('filesystems.uploads_disk') === 'cloudinary') {
            return $this->toCloudinary($file, $directory, $name);
        }

        return '/storage/'.$file->storeAs($directory, $name, 'public');
    }

    private function toCloudinary(UploadedFile $file, string $directory, string $name): string
    {
        $result = Cloudinary::uploadApi()->upload((string) $file->getRealPath(), [
            'public_id' => "uco/{$directory}/".pathinfo($name, PATHINFO_FILENAME),
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }
}
