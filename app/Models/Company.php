<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, \App\Traits\HasImage, \App\Traits\HasSlug, \App\Traits\HasApprovalStatus;

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
        'province',
        'city',
        'logo_url',

        // Platform management
        'is_visible',
        'approval_status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'name' => \App\Casts\CompanyName::class,
            'job_description' => \App\Casts\CleanText::class,
        ];
    }

    // ─── Accessors ───

    public function getLogoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'company');
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

    // ─── Relationships ───

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
