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

    // ─── Accessors ───

    public function getPhotoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'product');
    }

    public function getNameAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        return trim(strip_tags($cleaned));
    }

    public function getDescriptionAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        return trim(strip_tags($cleaned));
    }

    // ─── Relationships ───

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
