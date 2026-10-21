<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (Category $cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    // --- Loading ---

    /**
     * Gives each business or work profile its category from one query, or from categories already loaded, instead of one
     * category query per eager-loaded list (every query costs 20 to 30 ms on the live database).
     */
    public static function attachTo(iterable $items, ?Collection $known = null): void
    {
        $items = collect($items);
        $ids = $items->pluck('category_id')->filter()->unique()->values();
        $categories = ($known ?? ($ids->isEmpty() ? new Collection : static::whereIn('id', $ids)->get()))->keyBy('id');

        $items->each(fn (Model $item) => $item->setRelation('category', $categories->get($item->category_id)));
    }

    // --- Relationships ---

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
