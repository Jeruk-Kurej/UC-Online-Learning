<?php

namespace App\Models;

use App\Casts\CleanText;
use App\Support\GoogleDrive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class User
 *
 * @mixin Model
 * @mixin Builder
 *
 * ── Database columns ──────────────────────────────────────────
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $google_id
 * @property string $role
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $slug
 * @property string|null $prefix_title
 * @property string|null $suffix_title
 * @property string|null $personal_email
 * @property string|null $phone_number
 * @property string|null $mobile_number
 * @property string|null $whatsapp
 * @property string|null $linkedin
 * @property string|null $current_status
 * @property string|null $nis
 * @property string|null $year_of_enrollment
 * @property string|null $graduate_year
 * @property string|null $major
 * @property string|null $testimony
 * @property string|null $profile_photo_url
 * @property string|null $activities_doc_url
 * @property string|null $expertise_certification_url
 * @property bool $is_visible
 * @property bool $is_featured
 * @property bool $is_featured_testimony
 * @property string|null $student_status
 * @property string|null $ai_sentiment
 * @property float|null $ai_score
 * @property string|null $ai_rejection_reason
 * @property Carbon|null $submitted_at
 *
 * ── Computed / Accessor properties ────────────────────────────
 * @property-read string                     $full_titled_name
 * @property-read string                     $display_status
 * @property-read array                      $activity_files
 * @property-read string                     $initials
 * @property-read bool                       $has_profile_photo
 * @property-read string|null                $profile_photo_thumb_url
 * @property-read string|null                $profile_photo_mini_url
 * @property-read string                     $role_label
 * @property-read string                     $directory_view
 * @property-read array                      $activity_gallery_urls
 * @property-read array                      $certification_files
 *
 * ── Relationships ──────────────────────────────────────────────
 * @property-read Collection<int, Business>  $businesses
 * @property-read int|null                   $businesses_count
 * @property-read Collection<int, Company>   $companies
 * @property-read Collection<int, Skill>     $skills
 * @property-read Collection<int, Business>  $memberOfBusinesses
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, \App\Traits\HasImage, \App\Traits\HasSlug, \App\Traits\HasInitial, \App\Traits\HasWhatsAppLink;

    protected $fillable = [
        // --- System ---
        'email',
        'password',
        'google_id',
        'role',
        'email_verified_at',

        // --- CSV: Identity ---
        'submitted_at',
        'prefix_title',
        'name',
        'suffix_title',
        'personal_email',

        // --- CSV: Contact ---
        'phone_number',
        'mobile_number',
        'whatsapp',
        'linkedin',

        // --- CSV: Academic ---
        'current_status',
        'nis',
        'year_of_enrollment',
        'graduate_year',
        'major',

        // --- CSV: Profile extras ---
        'testimony',

        'profile_photo_url',
        'activities_doc_url',
        'expertise_certification_url',

        // --- Platform management ---
        'is_visible',
        'is_featured',
        'is_featured_testimony',
        'student_status',
        'show_contact_details',

        // --- AI Analysis ---
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
            'testimony' => CleanText::class,
            'password' => 'hashed',
            'is_visible' => 'boolean',
            'is_featured' => 'boolean',
            'is_featured_testimony' => 'boolean',
            'show_contact_details' => 'boolean',
        ];
    }

    // --- Accessors ---

    public function getProfilePhotoUrlAttribute($value)
    {
        return $this->resolveImage($value, 'profile');
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

    public function getActivityFilesAttribute(): array
    {
        return collect($this->activities_doc_url)->map(function (string $url) {
            $name = basename(parse_url($url, PHP_URL_PATH) ?? $url);
            $previewUrl = null;

            if (GoogleDrive::isDriveUrl($url)) {
                $id = GoogleDrive::fileId($url);
                $previewUrl = $id ? GoogleDrive::thumbnailUrl($id, 'w400') : null;
            } elseif (in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $previewUrl = $url;
            }

            return ['url' => $url, 'previewUrl' => $previewUrl, 'name' => $name];
        })->values()->all();
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1)).strtoupper(substr(explode(' ', $this->name)[1] ?? '', 0, 1));
    }

    public function getHasProfilePhotoAttribute(): bool
    {
        return $this->isRealImage($this->profile_photo_url);
    }

    public function getProfilePhotoThumbUrlAttribute(): ?string
    {
        return storage_image_url($this->profile_photo_url, ['width' => 300, 'height' => 300, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']);
    }

    public function getProfilePhotoMiniUrlAttribute(): ?string
    {
        return storage_image_url($this->profile_photo_url, ['width' => 150, 'height' => 150, 'crop' => 'thumb']);
    }

    public function getLinkedinUrlAttribute(): ?string
    {
        if (! $this->linkedin) {
            return null;
        }

        return str_starts_with($this->linkedin, 'http') ? $this->linkedin : 'https://linkedin.com/in/'.$this->linkedin;
    }

    public function getFocusLabelAttribute(): string
    {
        return $this->current_status ?: (ucfirst((string) $this->role) ?: '-');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role ?? '') {
            'admin' => 'Administrator',
            'alumni' => 'UCO Alumni',
            'student' => 'UCO Student',
            default => ucfirst($this->role ?? 'Member'),
        };
    }

    public function getDirectoryViewAttribute(): string
    {
        return match (true) {
            $this->isIntrapreneur() => 'intrapreneur',
            $this->isEntrepreneur() => 'entrepreneur',
            $this->companies->isNotEmpty() => 'intrapreneur',
            default => 'entrepreneur',
        };
    }

    public function getActivityGalleryUrlsAttribute(): array
    {
        return collect($this->activities_doc_url)->map(function ($url) {
            $id = GoogleDrive::isDriveUrl($url) ? GoogleDrive::fileId($url) : null;

            return $id ? GoogleDrive::viewUrl($id) : $url;
        })->values()->all();
    }

    public function getCertificationFilesAttribute(): array
    {
        if (! $this->expertise_certification_url) {
            return [];
        }

        $urls = array_filter(array_map('trim', preg_split('/[;,]+/', $this->expertise_certification_url)));

        return array_values(array_map(fn (string $url) => $this->describeCertificationFile($url), $urls));
    }

    // --- How a certification link is previewed: Drive and PDF files as documents, Cloudinary PDFs as their JPG rendering ---

    private function describeCertificationFile(string $url): array
    {
        $file = ['originalUrl' => $url, 'previewUrl' => $url, 'isPdf' => false, 'isImage' => false, 'isGoogleDrive' => false, 'driveId' => null];

        if (GoogleDrive::isDriveUrl($url)) {
            $file['isGoogleDrive'] = true;
            if ($id = GoogleDrive::fileId($url)) {
                $file['driveId'] = $id;
                $file['previewUrl'] = GoogleDrive::previewUrl($id);
            }
            $file['isPdf'] = true;

            return $file;
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        if ($extension === 'pdf') {
            $file['isPdf'] = true;
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $file['isImage'] = true;
        } elseif (str_contains(strtolower($url), '.pdf')) {
            $file['isPdf'] = true;
        } else {
            $file['isImage'] = true;
        }

        if ($file['isPdf'] && str_contains($url, 'cloudinary.com')) {
            $file['isPdf'] = false;
            $file['isImage'] = true;
            $file['previewUrl'] = preg_replace('/\.pdf$/i', '.jpg', $url);
        }

        return $file;
    }

    // --- Relationships ---

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

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function sentCollabs()
    {
        return $this->hasMany(Collab::class, 'sender_id');
    }

    public function collabStatusWith(?User $user)
    {
        if (! $user) {
            return null;
        }

        $collab = Collab::where(function ($query) use ($user) {
            $query->where('sender_id', $this->id)->where('recipient_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->where('recipient_id', $this->id);
        })->first();

        return $collab ? $collab->status : null;
    }

    // --- Helpers ---

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
        return trim(($this->prefix_title ?? '').' '.$this->name.' '.($this->suffix_title ?? ''));
    }

    public function getDisplayStatusAttribute(): string
    {
        return str_contains(strtolower($this->student_status ?? ''), 'alumni') ? 'Alumni' : 'Student';
    }

    // --- Scopes ---

    /**
     * Four-digit enrollment years in numeric order; anything else counts as 0.
     */
    public function scopeOrderByEnrollmentYear($query, string $direction)
    {
        return $query->orderByRaw("CASE WHEN year_of_enrollment REGEXP '^[0-9]{4}$' THEN CAST(year_of_enrollment AS UNSIGNED) ELSE 0 END {$direction}");
    }

    // --- Contact details on the profile ---

    public function hasContactDetails(bool $withLinkedin = true): bool
    {
        return (bool) ($this->whatsapp || $this->email || ($withLinkedin && $this->linkedin));
    }

    /**
     * Contacts show when the person allows it; admins always see them.
     */
    public function contactsVisibleTo(?User $viewer): bool
    {
        return (bool) ($this->show_contact_details || $viewer?->isAdmin());
    }
}
