<?php

namespace App\Imports;

use App\Jobs\UploadImageToCloudinaryJob;
use App\Models\Business;
use App\Models\Category;
use App\Models\Certification;
use App\Models\Company;
use App\Models\LegalDocument;
use App\Models\Product;
use App\Models\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class FormResponseImport implements ShouldQueue, SkipsEmptyRows, ToModel, WithChunkReading, WithEvents, WithHeadingRow
{
    use Concerns\TracksImportProgress, Concerns\ParsesImportDates;

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
     * Map CSV heading row to internal keys.
     * Maatwebsite lowercases + snake_cases headings, so we map from that.
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

    /**
     * Parse Google Form "Selected" column (featured flag).
     * Excel/CSV may provide boolean true/false, 1/0, or strings TRUE/FALSE.
     * Returns null when the column is absent so re-imports do not wipe featured state.
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

        $normalized = strtolower(trim((string) $raw));

        if (in_array($normalized, ['true', '1', 'yes', 'y'], true)) {
            return true;
        }

        if (in_array($normalized, ['false', '0', 'no', 'n'], true)) {
            return false;
        }

        return false;
    }

    /**
     * Process each CSV row → creates User + (Business OR Company) + Products + pivots.
     */
    public function model(array $row)
    {
        try {
            // --- Debug: raw column keys of the first row ---
            if (! self::$debugKeysDumped) {
                self::$debugKeysDumped = true;
                $keysAndSamples = [];
                foreach ($row as $k => $v) {
                    $keysAndSamples[$k] = (string) ($v ?? '');
                }
                Log::info('[FormResponseImport] DEBUG - Raw column keys from CSV (Maatwebsite snake_case):', $keysAndSamples);
            }

            // --- 1. Email (required) ---
            $email = $this->col($row, 'email_address');
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skip('Missing/invalid Email Address');

                return null;
            }

            if ($this->importType === null) {
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

            $fullName = $this->col($row, 'full_name');
            if (! $fullName) {
                $this->skip("Missing Full Name for {$email}");

                return null;
            }

            // --- 2. User ---
            $graduateYearRaw = $this->col($row, 'graduate_year');
            // --- graduate_year holds a 4-digit year or text such as "Active Student"; only a real year means alumni ---
            $graduateYear = (is_numeric($graduateYearRaw) && strlen(trim((string) $graduateYearRaw)) === 4)
                            ? trim((string) $graduateYearRaw)
                            : null;

            $enrollmentStatus = strtolower($this->col($row, 'current_status') ?? '');
            $studentStatus = ($graduateYear || str_contains($enrollmentStatus, 'alumni')) ? 'alumni' : 'active';

            $careerCategoryRaw = $this->col($row, 'category') ?? '';
            $careerCategory = null;
            if (str_contains(strtolower($careerCategoryRaw), 'intrapreneur')) {
                $careerCategory = 'Intrapreneur';
            } elseif (str_contains(strtolower($careerCategoryRaw), 'entrepreneur')) {
                $careerCategory = 'Entrepreneur';
            }

            $isFeatured = $this->parseSelectedFeatured($row);

            // --- Featured flags: each sheet only decides for its own career type ---
            if ($isFeatured !== null && $this->importType !== null && $careerCategory !== null) {
                $lowerCategory = strtolower($careerCategory);
                if ($this->importType === 'intrapreneur' && $lowerCategory === 'entrepreneur' && $isFeatured === false) {
                    $isFeatured = null;
                } elseif ($this->importType === 'entrepreneur' && $lowerCategory === 'intrapreneur' && $isFeatured === false) {
                    $isFeatured = null;
                }
            }

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

            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $existingUser->update($userData);
                $user = $existingUser;
            } else {
                $user = User::create(array_merge($userData, [
                    'email' => $email,
                    'password' => Hash::make('password123'),
                ]));
            }

            $rawProfilePhoto = $this->col($row, 'professional_profile_photo');
            if ($rawProfilePhoto && ! str_contains($rawProfilePhoto, 'cloudinary.com')) {
                UploadImageToCloudinaryJob::dispatch($user, 'profile_photo_url', $rawProfilePhoto, 'users', $fullName);
            }

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

            // --- 3. Skills ---
            $skillsRaw = $this->col($row, 'skills');
            if ($skillsRaw) {
                $skillIds = [];
                foreach ($this->splitComma($skillsRaw) as $skillName) {
                    try {
                        $skill = $this->getOrCreateSkill($skillName);
                        $skillIds[] = $skill->id;
                    } catch (\Exception $e) {
                        Log::warning('[FormResponseImport] Failed to get or create skill: '.$e->getMessage());
                    }
                }
                $user->skills()->syncWithoutDetaching($skillIds);
            }

            // --- 4. Entrepreneur or intrapreneur ---
            $status = strtolower($this->col($row, 'current_status') ?? '');

            $businessName = $this->col($row, 'business_name');
            if ($businessName) {
                $categoryId = null;
                $catName = $this->col($row, 'industry_category');
                if ($catName) {
                    try {
                        $cat = $this->getOrCreateCategory($catName);
                        $categoryId = $cat->id;
                    } catch (\Exception $e) {
                        Log::warning('[FormResponseImport] Failed to get or create category: '.$e->getMessage());
                    }
                }

                $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

                $businessData = [
                    'category_id' => $categoryId,
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

                // --- Dedup: exact name first, then the user's first business ---
                $business = Business::where('user_id', $user->id)->where('name', $businessName)->first()
                    ?? Business::where('user_id', $user->id)->where('type', 'entrepreneur')->first();

                if ($business) {
                    $business->update($businessData);
                } else {
                    $business = Business::create(array_merge($businessData, [
                        'user_id' => $user->id,
                        'name' => $businessName,
                    ]));
                }

                if ($rawLogoUrl && ! str_contains($rawLogoUrl, 'cloudinary.com')) {
                    UploadImageToCloudinaryJob::dispatch($business, 'logo_url', $rawLogoUrl, 'businesses', $businessName);
                }

                $business->members()->syncWithoutDetaching([
                    $user->id => ['position' => $this->col($row, 'entrepreneur_position')],
                ]);

                // --- Products (up to 3) ---
                $this->importProducts($business, $row);

                // --- Legal documents ---
                $legalRaw = $this->col($row, 'legal_documents');
                if ($legalRaw) {
                    $ids = [];
                    foreach ($this->splitComma($legalRaw) as $docName) {
                        try {
                            $doc = $this->getOrCreateLegalDocument($docName);
                            $ids[] = $doc->id;
                        } catch (\Exception $e) {
                            Log::warning('[FormResponseImport] Failed to get or create legal doc: '.$e->getMessage());
                        }
                    }
                    $business->legalDocuments()->syncWithoutDetaching($ids);
                }

                // --- Certifications ---
                $certRaw = $this->col($row, 'business_certification', 'certification');
                if ($certRaw) {
                    $ids = [];
                    foreach ($this->splitComma($certRaw) as $certName) {
                        try {
                            $cert = $this->getOrCreateCertification($certName);
                            $ids[] = $cert->id;
                        } catch (\Exception $e) {
                            Log::warning('[FormResponseImport] Failed to get or create certification: '.$e->getMessage());
                        }
                    }
                    $business->certifications()->syncWithoutDetaching($ids);
                }
            }

            // --- 4b. Intrapreneur → company ---
            // --- The CSV header "Company Name " (trailing space) arrives as company_name_ ---
            $companyName = $this->col($row, 'company_name_', 'company_name');
            if ($companyName) {
                $companyName = strtoupper(trim($companyName));

                $industryCatId = null;
                $indCatName = $this->col($row, 'industry_category');
                if ($indCatName) {
                    try {
                        $indCat = $this->getOrCreateCategory($indCatName);
                        $industryCatId = $indCat->id;
                    } catch (\Exception $e) {
                        Log::warning('[FormResponseImport] Failed to get or create industry category: '.$e->getMessage());
                    }
                }

                $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

                $companyData = [
                    'category_id' => $industryCatId,
                    'position' => $this->col($row, 'intrapreneur_position'),
                    'level_position' => $this->col($row, 'level_position'),
                    'job_description' => $this->col($row, 'job_description'),
                    'year_started_working' => $this->col($row, 'year_started_working'),
                    'achievement' => $this->col($row, 'achievement'),
                    'company_scale' => $this->col($row, 'company_scale'),
                    'logo_url' => $rawLogoUrl,
                    'is_visible' => true,
                ];

                $company = Company::where('user_id', $user->id)->where('name', $companyName)->first()
                    ?? Company::where('user_id', $user->id)->first();

                if ($company) {
                    $company->update(array_merge($companyData, ['name' => $companyName]));
                } else {
                    $company = Company::create(array_merge($companyData, [
                        'user_id' => $user->id,
                        'name' => $companyName,
                    ]));
                }

                if ($rawLogoUrl && ! str_contains($rawLogoUrl, 'cloudinary.com')) {
                    UploadImageToCloudinaryJob::dispatch($company, 'logo_url', $rawLogoUrl, 'companies', $companyName);
                }
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

    /**
     * Import up to 3 products from the flat CSV columns.
     */
    private function importProducts(Business $business, array $row): void
    {
        // --- Re-import replaces the business's products ---
        $business->products()->delete();

        $productSlots = [
            1 => [
                'name' => $this->col($row, 'productservice_name_1', 'product_service_name_1'),
                'desc' => $this->col($row, 'productservice_description_1', 'product_service_description_1'),
                'price' => $this->col($row, 'productservice_price_1', 'product_service_price_1'),
                'photo' => $this->col($row, 'productservice_photo_1', 'product_service_photo_1'),
            ],
            2 => [
                'name' => $this->col($row, 'productservice_name_2', 'product_service_name_2'),
                'desc' => $this->col($row, 'productservice_description_2', 'product_service_description_2'),
                'price' => $this->col($row, 'productservice_price_2', 'product_service_price_2'),
                'photo' => $this->col($row, 'productservice_photo_2', 'product_service_photo_2'),
            ],
            3 => [
                'name' => $this->col($row, 'productservice_name_3', 'product_service_name_3'),
                'desc' => $this->col($row, 'productservice_description_3', 'product_service_description_3'),
                'price' => $this->col($row, 'productservice_price_3', 'product_service_price_3'),
                'photo' => $this->col($row, 'productservice_photo_3', 'product_service_photo_3'),
            ],
        ];

        $productType = ($business->offering_type === 'service') ? 'service' : 'product';

        foreach ($productSlots as $order => $slot) {
            if (empty($slot['name'])) {
                continue;
            }

            $rawPhotoUrl = $slot['photo'];

            $product = Product::create([
                'business_id' => $business->id,
                'type' => $productType,
                'name' => $slot['name'],
                'description' => $slot['desc'],
                'price' => $slot['price'],
                'photo_url' => $rawPhotoUrl,
                'sort_order' => $order,
            ]);

            if ($rawPhotoUrl && ! str_contains($rawPhotoUrl, 'cloudinary.com')) {
                UploadImageToCloudinaryJob::dispatch($product, 'photo_url', $rawPhotoUrl, 'products', $slot['name']);
            }
        }
    }

    // --- Helpers ---

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
        if (str_contains($lower, 'product')) {
            return 'product';
        }

        return 'product';
    }

    private function splitComma(string $raw): array
    {
        return array_filter(array_map('trim', preg_split('/[,;]+/', $raw)));
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

    private function skip(string $msg): void
    {
        $this->skippedCount++;
        $this->errors[] = $msg;
        Log::warning('Skipped row: '.$msg);
        $this->updateProgress('skipped');

        if ($this->importId) {
            $prefix = "import_{$this->importId}";
            $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
            if (! is_array($progress)) {
                $progress = ['status' => 'processing', 'errors' => []];
            }
            if (! isset($progress['errors']) || ! is_array($progress['errors'])) {
                $progress['errors'] = [];
            }
            $progress['errors'][] = $msg;
            Cache::put($prefix, $progress, now()->addMinutes(60));
        }
    }

    // --- Events ---

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                if (! $this->importId) {
                    return;
                }
                $totalRows = $event->getReader()->getTotalRows();
                $total = max(0, max($totalRows) - 1);
                $prefix = "import_{$this->importId}";

                Cache::put($prefix, ['status' => 'processing', 'errors' => []], now()->addMinutes(60));
                Cache::forever("{$prefix}_total", $total);
                Cache::forever("{$prefix}_current", 0);
                Cache::forever("{$prefix}_success", 0);
                Cache::forever("{$prefix}_skipped", 0);

                Log::info("[FormResponseImport] Started: {$total} rows");
            },
            AfterImport::class => function () {
                if (! $this->importId) {
                    return;
                }
                $prefix = "import_{$this->importId}";
                $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
                $progress['status'] = 'completed';
                Cache::put($prefix, $progress, now()->addMinutes(60));
                Log::info('[FormResponseImport] Completed');
            },
        ];
    }

    private function getOrCreateSkill(string $skillName): Skill
    {
        $slug = Str::slug($skillName);
        if (! $slug) {
            throw new \Exception("Invalid skill name: {$skillName}");
        }

        $skill = Skill::where('slug', $slug)->first();
        if ($skill) {
            return $skill;
        }

        $skill = Skill::where('name', trim($skillName))->first();
        if ($skill) {
            return $skill;
        }

        try {
            return Skill::create([
                'slug' => $slug,
                'name' => trim($skillName),
            ]);
        } catch (QueryException $e) {
            $skill = Skill::where('slug', $slug)->first()
                ?? Skill::where('name', trim($skillName))->first();

            if ($skill) {
                return $skill;
            }
            throw $e;
        }
    }

    private function getOrCreateCategory(string $categoryName): Category
    {
        $slug = Str::slug($categoryName);
        if (! $slug) {
            throw new \Exception("Invalid category name: {$categoryName}");
        }

        $cat = Category::where('slug', $slug)->first()
            ?? Category::where('name', trim($categoryName))->first();

        if ($cat) {
            return $cat;
        }

        try {
            return Category::create([
                'slug' => $slug,
                'name' => trim($categoryName),
            ]);
        } catch (QueryException $e) {
            $cat = Category::where('slug', $slug)->first()
                ?? Category::where('name', trim($categoryName))->first();

            if ($cat) {
                return $cat;
            }
            throw $e;
        }
    }

    private function getOrCreateLegalDocument(string $docName): LegalDocument
    {
        $name = trim($docName);
        $doc = LegalDocument::where('name', $name)->first();
        if ($doc) {
            return $doc;
        }

        try {
            return LegalDocument::create(['name' => $name]);
        } catch (QueryException $e) {
            $doc = LegalDocument::where('name', $name)->first();
            if ($doc) {
                return $doc;
            }
            throw $e;
        }
    }

    private function getOrCreateCertification(string $certName): Certification
    {
        $name = trim($certName);
        $cert = Certification::where('name', $name)->first();
        if ($cert) {
            return $cert;
        }

        try {
            return Certification::create(['name' => $name]);
        } catch (QueryException $e) {
            $cert = Certification::where('name', $name)->first();
            if ($cert) {
                return $cert;
            }
            throw $e;
        }
    }
}
