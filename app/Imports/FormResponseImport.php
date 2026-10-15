<?php

namespace App\Imports;

use App\Models\Business;
use App\Models\Category;
use App\Models\Certification;
use App\Models\LegalDocument;
use App\Models\Product;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Google Form export (entrepreneur, intrapreneur or mixed sheet): one row becomes a user, their business with
 * products, legal documents and certifications, and/or their work profile.
 */
class FormResponseImport implements ShouldQueue, SkipsEmptyRows, ToModel, WithChunkReading, WithEvents, WithHeadingRow
{
    use Concerns\ParsesImportDates, Concerns\TracksImportProgress, Concerns\UpsertsVentures;

    public $importId;

    public $fileName;

    protected $importType = null;

    protected $errors = [];

    protected $successCount = 0;

    protected $skippedCount = 0;

    private static bool $debugKeysDumped = false;

    public function __construct($importId = null, $fileName = null)
    {
        $this->importId = $importId;
        $this->fileName = $fileName;

        if ($fileName) {
            $lowerName = strtolower($fileName);
            if (str_contains($lowerName, 'intrapreneur')) {
                $this->importType = 'intrapreneur';
            } elseif (str_contains($lowerName, 'entrepreneur')) {
                $this->importType = 'entrepreneur';
            }
        }
    }

    public function chunkSize(): int
    {
        return 1;
    }

    /**
     * Process each CSV row → creates User + (Business OR Company) + Products + pivots.
     */
    public function model(array $row)
    {
        try {
            $this->logColumnKeysOnce($row);

            $email = $this->col($row, 'email_address');
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skip('Missing/invalid Email Address');

                return null;
            }

            $this->detectImportType($row);

            $fullName = $this->col($row, 'full_name');
            if (! $fullName) {
                $this->skip("Missing Full Name for {$email}");

                return null;
            }

            $careerCategory = $this->careerCategory($row);
            $isFeatured = $this->featuredFlag($row, $careerCategory);

            $user = $this->importUser($row, $email, $fullName, $careerCategory, $isFeatured);

            $this->syncByName($user->skills(), $this->col($row, 'skills'), fn (string $name) => $this->firstOrCreateBySlug(Skill::class, $name, 'skill'), 'skill');

            if ($businessName = $this->col($row, 'business_name')) {
                $this->importBusiness($user, $businessName, $row, $isFeatured);
            }

            // --- The CSV header "Company Name " (trailing space) arrives as company_name_ ---
            if ($companyName = $this->col($row, 'company_name_', 'company_name')) {
                $this->importCompany($user, strtoupper(trim($companyName)), $row);
            }

            $this->successCount++;
            $this->updateProgress('success');

            return $user;
        } catch (\Exception $e) {
            $this->skip("Error: {$e->getMessage()}");
            Log::error("FormResponseImport error: {$e->getMessage()}", ['row' => $row]);

            return null;
        }
    }

    // --- Row reading ---

    /**
     * First non-empty value among the given (Maatwebsite snake_case) column keys, trimmed.
     */
    private function col(array $row, string ...$keys): ?string
    {
        foreach ($keys as $key) {
            $val = $row[$key] ?? null;
            if ($val !== null && $val !== '') {
                return trim((string) $val);
            }
        }

        return null;
    }

    private function logColumnKeysOnce(array $row): void
    {
        if (self::$debugKeysDumped) {
            return;
        }
        self::$debugKeysDumped = true;

        $keysAndSamples = [];
        foreach ($row as $k => $v) {
            $keysAndSamples[$k] = (string) ($v ?? '');
        }
        Log::info('[FormResponseImport] DEBUG - Raw column keys from CSV (Maatwebsite snake_case):', $keysAndSamples);
    }

    /**
     * A file name without "entrepreneur" / "intrapreneur" gets its type from the first row that has only one kind.
     */
    private function detectImportType(array $row): void
    {
        if ($this->importType !== null) {
            return;
        }

        $hasEntrepreneur = ! empty($this->col($row, 'business_name'));
        $hasIntrapreneur = ! empty($this->col($row, 'company_name_', 'company_name'));

        if ($hasEntrepreneur && ! $hasIntrapreneur) {
            $this->importType = 'entrepreneur';
            Log::info('[FormResponseImport] Dynamically detected Entrepreneur sheet from column content.');
        } elseif ($hasIntrapreneur && ! $hasEntrepreneur) {
            $this->importType = 'intrapreneur';
            Log::info('[FormResponseImport] Dynamically detected Intrapreneur sheet from column content.');
        }
    }

    private function careerCategory(array $row): ?string
    {
        $raw = strtolower($this->col($row, 'category') ?? '');

        return match (true) {
            str_contains($raw, 'intrapreneur') => 'Intrapreneur',
            str_contains($raw, 'entrepreneur') => 'Entrepreneur',
            default => null,
        };
    }

    /**
     * Featured flag from the "Selected" column (null when the column is absent, so re-imports keep the state). Each
     * sheet only decides for its own career type: an unselected row of the other type leaves the flag alone.
     */
    private function featuredFlag(array $row, ?string $careerCategory): ?bool
    {
        $isFeatured = $this->parseSelectedFeatured($row);

        if ($isFeatured === false && $this->importType !== null && $careerCategory !== null) {
            $otherType = $this->importType === 'intrapreneur' ? 'entrepreneur' : 'intrapreneur';
            if (strtolower($careerCategory) === $otherType) {
                return null;
            }
        }

        return $isFeatured;
    }

    /**
     * Excel/CSV may give the "Selected" column as a boolean, 1/0, or strings like TRUE / yes / n.
     */
    private function parseSelectedFeatured(array $row): ?bool
    {
        if (! array_key_exists('selected', $row)) {
            return null;
        }

        $raw = $row['selected'];

        if ($raw === null || $raw === '') {
            return false;
        }

        if (is_bool($raw)) {
            return $raw;
        }

        if (is_int($raw) || is_float($raw)) {
            return (int) $raw === 1;
        }

        return in_array(strtolower(trim((string) $raw)), ['true', '1', 'yes', 'y'], true);
    }

    // --- User ---

    private function importUser(array $row, string $email, string $fullName, ?string $careerCategory, ?bool $isFeatured): User
    {
        $graduateYearRaw = $this->col($row, 'graduate_year');
        // --- graduate_year holds a 4-digit year or text such as "Active Student"; only a real year means alumni ---
        $graduateYear = (is_numeric($graduateYearRaw) && strlen(trim((string) $graduateYearRaw)) === 4)
            ? trim((string) $graduateYearRaw)
            : null;

        $enrollmentStatus = strtolower($this->col($row, 'current_status') ?? '');
        $studentStatus = ($graduateYear || str_contains($enrollmentStatus, 'alumni')) ? 'alumni' : 'active';

        // --- Debug: critical field values ---
        Log::debug('[FormResponseImport] Row debug', [
            'email' => $email,
            'category_raw' => $row['category'] ?? '<<KEY MISSING>>',
            'current_status_raw' => $row['current_status'] ?? '<<KEY MISSING>>',
            'graduate_year_raw' => $graduateYearRaw,
            'graduate_year_resolved' => $graduateYear,
            'student_status' => $studentStatus,
            'career_category' => $careerCategory,
            'is_featured' => $isFeatured,
            'selected_raw' => $row['selected'] ?? '<<KEY MISSING>>',
            'business_name' => $row['business_name'] ?? '<<KEY MISSING>>',
            'company_name' => $row['company_name_'] ?? ($row['company_name'] ?? '<<KEY MISSING>>'),
        ]);

        $userData = [
            'submitted_at' => $this->parseTimestamp($this->col($row, 'timestamp')),
            'prefix_title' => $this->col($row, 'prefix_title'),
            'name' => $fullName,
            'suffix_title' => $this->col($row, 'suffix_title'),
            'personal_email' => $this->col($row, 'personal_email_address'),
            'phone_number' => $this->col($row, 'personal_phone_number'),
            'mobile_number' => $this->col($row, 'personal_mobile_number'),
            'whatsapp' => $this->col($row, 'personal_whatsapp'),
            'linkedin' => $this->col($row, 'linkedin'),
            'current_status' => $careerCategory,
            'nis' => $this->col($row, 'nis_student_id'),
            'year_of_enrollment' => $this->col($row, 'year_of_enrollment'),
            'graduate_year' => $graduateYear,
            'major' => $this->col($row, 'major'),
            'testimony' => $this->col($row, 'testimony'),
            'profile_photo_url' => $this->col($row, 'professional_profile_photo'),
            'activities_doc_url' => $this->col($row, 'professional_activities_documentation'),
            'expertise_certification_url' => $this->col($row, 'expertise_certification'),
            'student_status' => $studentStatus,
            'email_verified_at' => now(),
        ];

        if ($isFeatured !== null) {
            $userData['is_featured'] = $isFeatured;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update($userData);
        } else {
            $user = User::create(array_merge($userData, [
                'email' => $email,
                'password' => Hash::make('password123'),
            ]));
        }

        $this->queueImageUpload($user, 'profile_photo_url', $this->col($row, 'professional_profile_photo'), 'users', $fullName);

        // --- Imported testimonies are auto-approved (skips Gemini to avoid rate limits) ---
        if (! empty($user->testimony) && is_null($user->ai_score)) {
            Log::info("[FormResponseImport] Auto-approving testimony for user: {$user->email} (Gemini AI skipped during import)");
            $user->update([
                'ai_score' => 100,
                'ai_sentiment' => 'Positive',
                'is_visible' => true,
                'ai_rejection_reason' => null,
            ]);
        }

        return $user;
    }

    // --- Business (entrepreneur) ---

    private function importBusiness(User $user, string $businessName, array $row, ?bool $isFeatured): void
    {
        $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

        $businessData = [
            'category_id' => $this->categoryId($this->col($row, 'industry_category'), 'category'),
            'position' => $this->col($row, 'entrepreneur_position'),
            'established_date' => $this->parseDate($this->col($row, 'established_date')),
            'description' => $this->col($row, 'description'),
            'province' => $this->col($row, 'province'),
            'city' => $this->col($row, 'city_regency', 'city_slash_regency'),
            'address' => $this->col($row, 'full_address'),
            'phone_number' => $this->col($row, 'business_phone_number'),
            'whatsapp' => $this->col($row, 'business_whatsapp'),
            'email' => $this->col($row, 'business_email_address'),
            'website' => $this->col($row, 'website'),
            'instagram' => $this->col($row, 'instagram'),
            'operational_status' => $this->col($row, 'operational_status'),
            'offering_type' => $this->normalizeOfferingType($this->col($row, 'offering_type')),
            'unique_value_proposition' => $this->col($row, 'unique_value_proposition'),
            'target_market' => $this->col($row, 'target_market'),
            'customer_base_size' => $this->col($row, 'customer_base_size'),
            'employee_count' => $this->col($row, 'employee_count'),
            'revenue_range' => $this->col($row, 'revenue_range_per_year'),
            'academic_heritage' => $this->col($row, 'academic_heritage'),
            'company_profile_url' => $this->col($row, 'company_profile'),
            'logo_url' => $rawLogoUrl,
            'business_scale' => $this->col($row, 'business_scale'),
            'business_legality' => $this->col($row, 'business_legality'),
            'product_legality' => $this->col($row, 'product_legality'),
            'type' => 'entrepreneur',
            'approval_status' => 'approved',
            'is_visible' => true,
        ];

        if ($isFeatured !== null) {
            $businessData['is_featured'] = $isFeatured;
        }

        $business = $this->upsertBusiness($user, $businessName, $businessData);

        $this->queueImageUpload($business, 'logo_url', $rawLogoUrl, 'businesses', $businessName);

        $business->members()->syncWithoutDetaching([
            $user->id => ['position' => $this->col($row, 'entrepreneur_position')],
        ]);

        $this->importProducts($business, $row);

        $this->syncByName($business->legalDocuments(), $this->col($row, 'legal_documents'), fn (string $name) => $this->firstOrCreateByName(LegalDocument::class, $name), 'legal doc');
        $this->syncByName($business->certifications(), $this->col($row, 'business_certification', 'certification'), fn (string $name) => $this->firstOrCreateByName(Certification::class, $name), 'certification');
    }

    /**
     * Up to three products from the flat "Product/Service … (1-3)" columns; a re-import replaces the business's products.
     */
    private function importProducts(Business $business, array $row): void
    {
        $business->products()->delete();

        $productType = ($business->offering_type === 'service') ? 'service' : 'product';

        foreach ([1, 2, 3] as $order) {
            $name = $this->col($row, "productservice_name_{$order}", "product_service_name_{$order}");
            if (empty($name)) {
                continue;
            }

            $rawPhotoUrl = $this->col($row, "productservice_photo_{$order}", "product_service_photo_{$order}");

            $product = Product::create([
                'business_id' => $business->id,
                'type' => $productType,
                'name' => $name,
                'description' => $this->col($row, "productservice_description_{$order}", "product_service_description_{$order}"),
                'price' => $this->col($row, "productservice_price_{$order}", "product_service_price_{$order}"),
                'photo_url' => $rawPhotoUrl,
                'sort_order' => $order,
            ]);

            $this->queueImageUpload($product, 'photo_url', $rawPhotoUrl, 'products', $name);
        }
    }

    // --- Work profile (intrapreneur) ---

    private function importCompany(User $user, string $companyName, array $row): void
    {
        $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

        $company = $this->upsertCompany($user, $companyName, [
            'category_id' => $this->categoryId($this->col($row, 'industry_category'), 'industry category'),
            'position' => $this->col($row, 'intrapreneur_position'),
            'level_position' => $this->col($row, 'level_position'),
            'job_description' => $this->col($row, 'job_description'),
            'year_started_working' => $this->col($row, 'year_started_working'),
            'achievement' => $this->col($row, 'achievement'),
            'company_scale' => $this->col($row, 'company_scale'),
            'logo_url' => $rawLogoUrl,
            'is_visible' => true,
        ]);

        $this->queueImageUpload($company, 'logo_url', $rawLogoUrl, 'companies', $companyName);
    }

    // --- Lookups: categories, skills, legal documents, certifications ---

    private function categoryId(?string $name, string $what): ?int
    {
        if (! $name) {
            return null;
        }

        try {
            return $this->firstOrCreateBySlug(Category::class, $name, 'category')->id;
        } catch (\Exception $e) {
            Log::warning("[FormResponseImport] Failed to get or create {$what}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Adds the comma- or semicolon-separated names in $raw to a many-to-many relation, creating the missing ones.
     */
    private function syncByName(BelongsToMany $relation, ?string $raw, callable $findOrCreate, string $what): void
    {
        if (! $raw) {
            return;
        }

        $ids = [];
        foreach (array_filter(array_map('trim', preg_split('/[,;]+/', $raw))) as $name) {
            try {
                $ids[] = $findOrCreate($name)->id;
            } catch (\Exception $e) {
                Log::warning("[FormResponseImport] Failed to get or create {$what}: ".$e->getMessage());
            }
        }
        $relation->syncWithoutDetaching($ids);
    }

    /**
     * Record with this slug or name, created when missing (a parallel import that created it first is picked up).
     *
     * @param  class-string<Model>  $model
     */
    private function firstOrCreateBySlug(string $model, string $name, string $what): Model
    {
        $slug = Str::slug($name);
        if (! $slug) {
            throw new \Exception("Invalid {$what} name: {$name}");
        }

        $find = fn () => $model::where('slug', $slug)->first() ?? $model::where('name', trim($name))->first();

        if ($existing = $find()) {
            return $existing;
        }

        try {
            return $model::create(['slug' => $slug, 'name' => trim($name)]);
        } catch (QueryException $e) {
            return $find() ?? throw $e;
        }
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function firstOrCreateByName(string $model, string $name): Model
    {
        $name = trim($name);
        $find = fn () => $model::where('name', $name)->first();

        if ($existing = $find()) {
            return $existing;
        }

        try {
            return $model::create(['name' => $name]);
        } catch (QueryException $e) {
            return $find() ?? throw $e;
        }
    }

    // --- Value parsing ---

    private function normalizeOfferingType(?string $val): string
    {
        if (! $val) {
            return 'product';
        }
        $lower = strtolower(trim($val));
        if (str_contains($lower, 'both') || str_contains($lower, '&') || str_contains($lower, 'and')) {
            return 'both';
        }
        if (str_contains($lower, 'service')) {
            return 'service';
        }

        return 'product';
    }

    private function parseTimestamp(?string $val): ?string
    {
        if (! $val) {
            return null;
        }
        try {
            return Carbon::parse($val)->toDateTimeString();
        } catch (\Exception $e) {
            return null;
        }
    }

    // --- Progress log lines ---

    protected function headerRows(): int
    {
        return 1;
    }

    protected function startedLogLine(int $total): string
    {
        return "[FormResponseImport] Started: {$total} rows";
    }

    protected function completedLogLine(): string
    {
        return '[FormResponseImport] Completed';
    }

    protected function skippedLogLine(string $reason): string
    {
        return 'Skipped row: '.$reason;
    }
}
