<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \App\Traits\HasImage;

    protected $fillable = [
        // System
        'email',
        'password',
        'google_id',
        'role',
        'email_verified_at',

        // CSV: Identity
        'submitted_at',
        'prefix_title',
        'name',
        'suffix_title',
        'personal_email',

        // CSV: Contact
        'phone_number',
        'mobile_number',
        'whatsapp',
        'linkedin',

        // CSV: Academic
        'current_status',
        'nis',
        'year_of_enrollment',
        'graduate_year',
        'major',

        // CSV: Profile extras
        'testimony',
        'cv_url',
        'profile_photo_url',
        'activities_doc_url',

        // Platform management
        'is_visible',
        'student_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'submitted_at' => 'datetime',
            'password' => 'hashed',
            'is_visible' => 'boolean',
        ];
    }

    // ─── Accessors ───

    public function getProfilePhotoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'profile');
    }

    public function getTestimonyAttribute($value)
    {
        $cleaned = preg_replace('/<br\s*\/?>/i', ' ', $value);
        return trim(strip_tags($cleaned));
    }

    // ─── Relationships ───

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill')->withTimestamps();
    }

    public function memberOfBusinesses()
    {
        return $this->belongsToMany(Business::class, 'business_user')->withPivot('position')->withTimestamps();
    }

    // ─── Helpers ───

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEntrepreneur(): bool
    {
        return strtolower($this->current_status ?? '') === 'entrepreneur';
    }

    public function isIntrapreneur(): bool
    {
        return strtolower($this->current_status ?? '') === 'intrapreneur';
    }

    public function getFullTitledNameAttribute(): string
    {
        return trim(($this->prefix_title ?? '') . ' ' . $this->name . ' ' . ($this->suffix_title ?? ''));
    }

    /**
     * Public-facing status: "Student" or "Alumni" only.
     */
    public function getDisplayStatusAttribute(): string
    {
        return $this->student_status === 'alumni' ? 'Alumni' : 'Student';
    }
}
