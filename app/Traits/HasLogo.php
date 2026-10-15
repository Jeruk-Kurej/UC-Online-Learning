<?php

namespace App\Traits;

/**
 * Logo of a Business or Company: always a URL (the UI-Avatars placeholder when there is no logo).
 * Needs HasImage.
 */
trait HasLogo
{
    public function getLogoUrlAttribute($value): string
    {
        return $this->resolveImage($value);
    }

    public function getHasLogoAttribute(): bool
    {
        return $this->isRealImage($this->logo_url);
    }

    public function getLogoThumbUrlAttribute(): ?string
    {
        return storage_image_url($this->logo_url, 'logo_thumb');
    }
}
