<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // --- Relationships ---

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_certification')->withTimestamps();
    }
}
