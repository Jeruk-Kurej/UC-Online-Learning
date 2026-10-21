<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Collection;
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

    /**
     * The id of the saved province, found without regard to case (the tables keep names in capitals or title case).
     */
    protected function selectedProvinceId(?string $savedName): ?string
    {
        return $savedName ? Province::whereRaw('LOWER(name) = ?', [mb_strtolower($savedName)])->value('id') : null;
    }

    /**
     * The id of the saved city in the city list: the list holds IndoRegion names in capitals, a saved record may hold the same name title-cased.
     */
    protected function selectedCityId(Collection $cities, ?string $savedName): ?string
    {
        return $savedName ? $cities->first(fn ($city) => strcasecmp($city->name, $savedName) === 0)?->id : null;
    }
}
