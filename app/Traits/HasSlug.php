<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    // --- Boot ---

    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $source = $model->{$model->getSlugSourceColumn()};

            if (empty($model->slug) && ! empty($source)) {
                $model->slug = static::generateUniqueSlug($source);
            }
        });
    }

    // --- Routing ---

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // --- Slug generation ---

    protected function getSlugSourceColumn(): string
    {
        return 'name';
    }

    protected static function generateUniqueSlug(string $value): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }
}
