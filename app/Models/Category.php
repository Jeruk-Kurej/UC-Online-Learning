<?php

namespace App\Models;

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
