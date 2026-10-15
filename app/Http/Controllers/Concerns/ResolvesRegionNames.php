<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Str;

/**
 * The province and city selects send IndoRegion ids; the tables store names.
 */
trait ResolvesRegionNames
{
    protected function resolveRegionNames(array $data, bool $titleCase = false): array
    {
        foreach (['province' => Province::class, 'city' => Regency::class] as $field => $model) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                $name = $model::find($data[$field])?->name;
                if ($name !== null) {
                    $data[$field] = $titleCase ? Str::title(strtolower($name)) : $name;
                }
            }
        }

        return $data;
    }
}
