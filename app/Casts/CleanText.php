<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CleanText implements CastsAttributes
{
    // --- Reading ---

    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        return static::clean($value);
    }

    // --- Writing ---

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    // --- Cleaning ---

    public static function clean(mixed $value): string
    {
        $withoutBreaks = preg_replace('/<br\s*\/?>/i', ' ', (string) $value);

        return trim(strip_tags($withoutBreaks));
    }
}
