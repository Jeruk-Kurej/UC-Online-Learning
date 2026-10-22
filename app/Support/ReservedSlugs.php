<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * Words a slug may not take because the site already answers that address: the fixed path parts that sit where
 * the slug sits in a route (`/about` beside `/{user}`, `/business/create` beside `/business/{business}`), and for
 * top-level slugs the folders in public/. Read from the routes, so a new page reserves its word by itself.
 */
class ReservedSlugs
{
    private static array $cache = [];

    public static function for(string $parameter): array
    {
        return self::$cache[$parameter] ??= self::collect($parameter);
    }

    private static function collect(string $parameter): array
    {
        $paths = collect(Route::getRoutes()->getRoutes())->map(fn ($route) => explode('/', trim($route->uri(), '/')));

        $prefixes = $paths
            ->map(function (array $parts) use ($parameter) {
                $position = array_search('{'.$parameter.'}', $parts, true);

                return $position === false ? null : array_slice($parts, 0, $position);
            })
            ->reject(fn (?array $prefix) => $prefix === null)
            ->unique(fn (array $prefix) => implode('/', $prefix));

        return $prefixes
            ->flatMap(fn (array $prefix) => $paths
                ->filter(fn (array $parts) => count($parts) > count($prefix) && array_slice($parts, 0, count($prefix)) === $prefix)
                ->map(fn (array $parts) => $parts[count($prefix)])
                ->merge($prefix === [] ? self::publicFolders() : []))
            ->reject(fn (string $part) => str_contains($part, '{'))
            ->unique()
            ->values()
            ->all();
    }

    private static function publicFolders(): array
    {
        return is_dir(public_path()) ? array_map('basename', File::directories(public_path())) : [];
    }
}
