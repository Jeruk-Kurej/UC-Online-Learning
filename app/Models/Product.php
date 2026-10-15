<?php

namespace App\Models;

use App\Casts\CleanText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, \App\Traits\HasImage;

    protected $fillable = [
        'business_id',
        'type',
        'name',
        'description',
        'price',
        'price_type',
        'photo_url',
        'photo_caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'name' => CleanText::class,
            'description' => CleanText::class,
        ];
    }

    // --- Accessors ---

    public function getPhotoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'product');
    }

    public function getHasPhotoAttribute(): bool
    {
        return (bool) $this->getRawOriginal('photo_url');
    }

    // --- Price as shown to visitors: a number only for fixed or negotiable prices ---

    public function getShowsPriceAttribute(): bool
    {
        return is_numeric($this->price) && $this->price_type !== 'unspecified' && $this->price_type !== 'customize';
    }

    public function getFormattedPriceAttribute(): ?string
    {
        return $this->shows_price ? number_format((float) $this->price, 0, ',', '.') : null;
    }

    public function getPriceTextAttribute(): string
    {
        return $this->price_type === 'customize' ? 'Customize by Order' : 'Price Unspecified';
    }

    public function getPriceTypeLabelAttribute(): ?string
    {
        return match ($this->price_type) {
            'fixed' => 'Fixed Price',
            'negotiable' => 'Negotiable',
            default => null,
        };
    }

    // --- Relationships ---

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
