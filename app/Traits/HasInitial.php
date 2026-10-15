<?php

namespace App\Traits;

/**
 * First letter of the name, upper-cased, for avatar and logo placeholders.
 */
trait HasInitial
{
    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}
