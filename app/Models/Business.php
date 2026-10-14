<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Business extends Model
{
    use HasFactory, \App\Traits\HasImage, \App\Traits\HasSlug, \App\Traits\HasApprovalStatus;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'position',
        'established_date',
        'description',
        'province',
        'city',
        'address',
        'phone_number',
        'whatsapp',
        'email',
        'website',
        'instagram',
        'operational_status',
        'offering_type',
        'unique_value_proposition',
        'target_market',
        'customer_base_size',
        'employee_count',
        'revenue_range',
        'academic_heritage',
        'company_profile_url',
        'logo_url',
        'business_scale',
        'business_legality',
        'product_legality',

        // Platform management
        'is_visible',
        'approval_status',
        'rejection_reason',
        'is_featured',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'established_date' => 'date',
            'name' => \App\Casts\CompanyName::class,
            'description' => \App\Casts\CleanText::class,
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    // ─── Accessors ───

    public function getLogoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'business');
    }

    public function getProfileQualityScoreAttribute()
    {
        $score = 0;
        $total = 10;
        
        if (!empty($this->getRawOriginal('logo_url'))) $score++;
        if (!empty($this->description)) $score++;
        if (!empty($this->unique_value_proposition)) $score++;
        if (!empty($this->city) || !empty($this->province) || !empty($this->address)) $score++;
        if (!empty($this->phone_number) || !empty($this->whatsapp)) $score++;
        if (!empty($this->website) || !empty($this->instagram)) $score++;
        if (!empty($this->company_profile_url)) $score++;
        if (!empty($this->target_market)) $score++;
        
        // Count related models (without triggering N+1 if loaded, but using count() if not)
        if ($this->products()->count() > 0) $score++;
        if ($this->legalDocuments()->count() > 0 || $this->certifications()->count() > 0) $score++;

        return round(($score / $total) * 100);
    }

    // ─── Model events ───

    protected static function booted(): void
    {
        static::saving(function (Business $business) {
            if ($business->approval_status !== 'approved') {
                $business->is_featured = false;
            }
        });
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

    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('sort_order');
    }

    public function legalDocuments()
    {
        return $this->belongsToMany(LegalDocument::class, 'business_legal_document')->withTimestamps();
    }

    public function certifications()
    {
        return $this->belongsToMany(Certification::class, 'business_certification')->withTimestamps();
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'business_user')->withPivot('position')->withTimestamps();
    }

    // ─── Scopes ───

    public function scopeEntrepreneur($query)
    {
        return $query->where('type', 'entrepreneur');
    }

    public function scopeIntrapreneur($query)
    {
        return $query->where('type', 'intrapreneur');
    }

    public function canBeManagedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        return $this->members()->where('users.id', $user->id)->exists();
    }

    public function isProductMode(): bool
    {
        return $this->offering_type === 'product' || $this->offering_type === 'both';
    }

    public function isServiceMode(): bool
    {
        return $this->offering_type === 'service' || $this->offering_type === 'both';
    }
}
