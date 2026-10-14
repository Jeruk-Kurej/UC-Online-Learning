<?php

namespace App\Models;

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
            'name' => \App\Casts\CleanText::class,
            'description' => \App\Casts\CleanText::class,
        ];
    }

    // ─── Accessors ───

    public function getPhotoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'product');
    }

    // ─── Relationships ───

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
