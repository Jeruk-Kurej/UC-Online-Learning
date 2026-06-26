<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin \Illuminate\Database\Eloquent\Builder
 *
 * ── Database columns ──────────────────────────────────────────
 * @property int                             $id
 * @property string                          $name
 * @property string                          $email
 * @property string|null                     $password
 * @property string|null                     $google_id
 * @property string                          $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null                     $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property string|null                     $slug
 * @property string|null                     $prefix_title
 * @property string|null                     $suffix_title
 * @property string|null                     $personal_email
 * @property string|null                     $phone_number
 * @property string|null                     $mobile_number
 * @property string|null                     $whatsapp
 * @property string|null                     $linkedin
 * @property string|null                     $current_status
 * @property string|null                     $nis
 * @property string|null                     $year_of_enrollment
 * @property string|null                     $graduate_year
 * @property string|null                     $major
 * @property string|null                     $testimony
 * @property string|null                     $profile_photo_url
 * @property string|null                     $activities_doc_url
 * @property string|null                     $expertise_certification_url
 * @property bool                            $is_visible
 * @property bool                            $is_featured
 * @property bool                            $is_featured_testimony
 * @property string|null                     $student_status
 * @property string|null                     $ai_sentiment
 * @property float|null                      $ai_score
 * @property string|null                     $ai_rejection_reason
 * @property \Illuminate\Support\Carbon|null $submitted_at
 *
 * ── Computed / Accessor properties ────────────────────────────
 * @property-read string                     $full_titled_name
 * @property-read string                     $display_status
 *
 * ── Relationships ──────────────────────────────────────────────
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Business>  $businesses
 * @property-read int|null                   $businesses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Company>   $companies
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skill>     $skills
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Business>  $memberOfBusinesses
 */
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

        'profile_photo_url',
        'activities_doc_url',
        'expertise_certification_url',

        // Platform management
        'is_visible',
        'is_featured',
        'is_featured_testimony',
        'student_status',
        'show_contact_details',

        // AI Analysis
        'ai_sentiment',
        'ai_score',
        'ai_rejection_reason',
        'activities_caption',
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
            'is_featured' => 'boolean',
            'is_featured_testimony' => 'boolean',
            'show_contact_details' => 'boolean',
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->slug) && !empty($user->name)) {
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });
    }

    private static function generateUniqueSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
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

    public function getActivitiesDocUrlAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return array_filter(array_map('trim', preg_split('/[;,]+/', $value)));
    }

    public function setActivitiesDocUrlAttribute($value)
    {
        $this->attributes['activities_doc_url'] = is_array($value) ? json_encode(array_values($value)) : $value;
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

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function sentCollabs()
    {
        return $this->hasMany(Collab::class, 'sender_id');
    }

    public function receivedCollabs()
    {
        return $this->hasMany(Collab::class, 'recipient_id');
    }

    public function collabStatusWith(?User $user)
    {
        if (!$user) {
            return null;
        }

        $collab = Collab::where(function ($query) use ($user) {
            $query->where('sender_id', $this->id)->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->where('recipient_id', $this->id);
        })->first();

        return $collab ? $collab->status : null;
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

    public function getDisplayStatusAttribute(): string
    {
        return str_contains(strtolower($this->student_status ?? ''), 'alumni') ? 'Alumni' : 'Student';
    }
}
