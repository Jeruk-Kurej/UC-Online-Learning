<?php

namespace App\Traits;

use App\Support\ReservedSlugs;
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

    protected static function generateUniqueSlug(string $value, mixed $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (static::slugIsTaken($slug, $ignoreId)) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    /**
     * Taken by another record, by an address the site already answers (`/about` for a person named About),
     * or by a record of a model that lives at the same addresses.
     */
    protected static function slugIsTaken(string $slug, mixed $ignoreId): bool
    {
        return in_array($slug, ReservedSlugs::for(Str::camel(class_basename(static::class))), true)
            || static::where('slug', $slug)->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()
            || collect(static::slugSharedWith())->contains(fn (string $model) => $model::where('slug', $slug)->exists());
    }

    /**
     * Models whose records share this model's addresses, so a slug must be free in their table too.
     */
    protected static function slugSharedWith(): array
    {
        return [];
    }

    /**
     * A unique slug for a new name, not counting this record's own slug.
     */
    public function freshSlugFor(string $value): string
    {
        return static::generateUniqueSlug($value, $this->getKey());
    }
}
