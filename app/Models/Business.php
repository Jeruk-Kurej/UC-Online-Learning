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
    use HasFactory, \App\Traits\HasImage, \App\Traits\HasLogo, \App\Traits\HasSlug, \App\Traits\HasApprovalStatus;

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

        // --- Platform management ---
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

    // --- Accessors ---

    // --- Model events ---

    protected static function booted(): void
    {
        static::saving(function (Business $business) {
            if ($business->approval_status !== 'approved') {
                $business->is_featured = false;
            }
        });
    }

    // --- Relationships ---

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

    // --- Scopes ---

    public function scopeEntrepreneur($query)
    {
        return $query->where('type', 'entrepreneur');
    }

    // --- Public people: a business stays public while one of its owners or co-owners is visible; hidden people are left out ---

    protected function whereSomeoneVisible($query): void
    {
        $query->whereHas('user', fn ($q) => $q->where('is_visible', true))
            ->orWhereHas('members', fn ($q) => $q->where('is_visible', true));
    }

    public function hasVisiblePeople(): bool
    {
        return (bool) $this->user?->is_visible || $this->members->contains('is_visible', true);
    }

    public function getPublicOwnerAttribute(): ?User
    {
        if ($this->user?->is_visible) {
            return $this->user;
        }

        return $this->visibleMembers()->first() ?? $this->user;
    }

    public function getPublicCoOwnersAttribute(): \Illuminate\Support\Collection
    {
        $shownOwnerId = $this->public_owner?->id;

        return $this->visibleMembers()
            ->reject(fn (User $member) => in_array($member->id, [$shownOwnerId, $this->user_id], true))
            ->values();
    }

    private function visibleMembers(): \Illuminate\Support\Collection
    {
        return $this->members
            ->where('is_visible', true)
            ->sortBy(fn (User $member) => [$member->pivot->created_at, $member->id])
            ->values();
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

    public function getOfferingModeAttribute(): string
    {
        return $this->offering_type ?? 'product';
    }

    public function getInstagramHandlesAttribute(): array
    {
        $handles = array_filter(array_map('trim', preg_split('/[,;\s]+/', (string) $this->instagram)));

        return array_values(array_filter(array_map(fn (string $handle) => ltrim($handle, '@'), $handles), fn (string $handle) => ! empty($handle)));
    }

    public function whatsappLinkFor(Product $offering): ?string
    {
        $phone = $this->whatsapp ?: ($this->public_owner->whatsapp ?? null);
        $digits = $phone ? preg_replace('/[^0-9]/', '', $phone) : null;

        return $digits
            ? 'https://wa.me/'.$digits.'?text='.urlencode('Hi, I am interested in your '.$offering->type.': "'.$offering->name.'" listed on UC Online Learning.')
            : null;
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
