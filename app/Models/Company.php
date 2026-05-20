<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, \App\Traits\HasImage;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'position',
        'level_position',
        'job_description',
        'year_started_working',
        'achievement',
        'company_scale',
        'logo_url',

        // Platform management
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    // ─── Accessors ───

    public function getLogoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'company');
    }

    public function getNameAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        return strtoupper(trim(strip_tags($cleaned)));
    }

    public function getJobDescriptionAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        return trim(strip_tags($cleaned));
    }

    // ─── Auto-generate slug ───

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->slug)) {
                $company->slug = static::generateUniqueSlug($company->name);
            }
        });
    }

    private static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    // ─── Relationships ───

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ─── Scopes ───

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)
            ->whereHas('user', fn ($q) => $q->where('is_visible', true));
    }
}
