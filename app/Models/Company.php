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

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ─── Accessors ───

    public function getLogoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'company');
    }

    public function getNameAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        $name = trim(strip_tags($cleaned));

        if ($name === strtoupper($name)) {
            $name = \Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($name));
        }

        $name = preg_replace('/\bPt\b/i', 'PT', $name);
        $name = preg_replace('/\bCv\b/i', 'CV', $name);
        $name = preg_replace('/\bTbk\b/i', 'Tbk', $name);

        return $name;
    }

    public function getAchievementsListAttribute(): array
    {
        if (empty($this->achievement)) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $this->achievement);
        $items = [];
        foreach ($lines as $line) {
            $parts = preg_split('/\s+-\s+/', $line);
            foreach ($parts as $part) {
                $part = trim($part);
                if (empty($part)) continue;
                if (str_starts_with($part, '-')) {
                    $part = trim(substr($part, 1));
                }
                if (!empty($part)) {
                    $items[] = $part;
                }
            }
        }
        return $items;
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
