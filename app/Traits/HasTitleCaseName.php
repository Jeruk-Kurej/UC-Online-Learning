<?php

namespace App\Traits;

/**
 * Region name for dropdowns: the upper-case IndoRegion name in title case ("JAWA TIMUR" → "Jawa Timur").
 */
trait HasTitleCaseName
{
    public function getDisplayNameAttribute(): string
    {
        return ucwords(strtolower($this->name));
    }
}
