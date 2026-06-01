<?php

use Cloudinary\Cloudinary;

if (! function_exists('cloudinary_url')) {
    /**
     * Generate a Cloudinary transformed URL for a stored public id.
     * If the input already looks like a URL, return it unchanged.
     *
     * @param string|null $publicId
     * @param array $options  Transformation options (width, height, crop, fetch_format, quality, etc.)
     * @return string|null
     */
    function cloudinary_url(?string $publicId, array $options = []): ?string
    {
        if (!$publicId) {
            return null;
        }

        // If it's already an absolute URL, return as-is
        if (str_starts_with($publicId, 'http://') || str_starts_with($publicId, 'https://') || str_starts_with($publicId, 'data:')) {
            return $publicId;
        }

        // If Cloudinary SDK is available, use it to build a transformed URL
        try {
            if (function_exists('cloudinary')) {
                /** @var Cloudinary $cl */
                $cl = cloudinary();

                // Use Cloudinary image builder and pass transformation params
                $image = $cl->image($publicId);

                // Default to auto format/quality if not provided
                if (! array_key_exists('fetch_format', $options) && ! array_key_exists('format', $options)) {
                    $options['fetch_format'] = 'auto';
                }
                if (! array_key_exists('quality', $options)) {
                    $options['quality'] = 'auto';
                }

                // If width/height provided as integers, ensure they are ints
                if (isset($options['width'])) $options['width'] = (int) $options['width'];
                if (isset($options['height'])) $options['height'] = (int) $options['height'];

                return (string) $image->toUrl($options);
            }
        } catch (Exception $e) {
            // silently fail and fallback to Storage::url
        }

        // If Cloudinary couldn't generate a URL, return null (do not call Storage::exists())
        return null;
    }
}

if (! function_exists('storage_image_url')) {
    /**
     * Return a storage-backed image URL. If the app uses Cloudinary as the filesystem disk,
     * generate a Cloudinary transformed URL. Otherwise, fall back to Storage::url().
     *
     * @param string|null $path
     * @param array $options
     * @return string|null
     */
    /**
     * @param string|null $path
     * @param array|string|null $options  Either an options array or a preset name string
     * @return string|null
     */
    function storage_image_url(?string $path, $options = []): ?string
    {
        if (! $path) {
            return null;
        }

        // Allow passing a preset name as string
        $preset = null;
        if (is_string($options) && $options !== '') {
            $preset = $options;
            $options = [];
        }
        if (is_array($options) && array_key_exists('preset', $options)) {
            $preset = $options['preset'];
            unset($options['preset']);
        }

        // Preset map: named transform sets used across views
        $presets = [
            'hero' => ['width' => 1400, 'height' => 420, 'crop' => 'fit', 'quality' => 'auto', 'fetch_format' => 'auto'],
            'gallery_full' => ['width' => 1200, 'height' => 720, 'quality' => 'auto', 'fetch_format' => 'auto'],
            'gallery_thumb' => ['width' => 800, 'height' => 480, 'quality' => 'auto', 'fetch_format' => 'auto'],
            'logo_thumb' => ['width' => 128, 'height' => 128, 'crop' => 'fit', 'quality' => 'auto', 'fetch_format' => 'auto'],
            'profile_thumb' => ['width' => 128, 'height' => 128, 'crop' => 'fit', 'quality' => 'auto', 'fetch_format' => 'auto'],
            'lqip' => ['width' => 20, 'quality' => 1, 'fetch_format' => 'auto'],
        ];

        if ($preset && isset($presets[$preset])) {
            // merge preset options with provided options (explicit options win)
            $options = array_merge($presets[$preset], $options ?: []);
        }

        try {
            $disk = config('filesystems.default');
        } catch (\Throwable $e) {
            $disk = null;
        }

        if ($disk === 'cloudinary') {
            try {
                $url = cloudinary_url($path, $options);
                if ($url) {
                    return $url;
                }
            } catch (\Throwable $e) {
                // If Cloudinary SDK throws (e.g. resource not found), swallow and fall back to Storage::url()
            }
        }

        try {
            return Illuminate\Support\Facades\Storage::url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
