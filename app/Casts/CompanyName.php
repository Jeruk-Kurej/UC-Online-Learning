<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyName implements CastsAttributes
{
    // --- Reading ---

    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        $name = CleanText::clean($value);

        if ($name === strtoupper($name)) {
            $name = Str::title(Str::lower($name));
        }

        $name = preg_replace('/\bPt\b/i', 'PT', $name);
        $name = preg_replace('/\bCv\b/i', 'CV', $name);

        return preg_replace('/\bTbk\b/i', 'Tbk', $name);
    }

    // --- Writing ---

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
