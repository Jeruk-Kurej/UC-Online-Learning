<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Business;
use App\Models\Company;
use App\Models\Category;
use App\Models\Product;
use App\Models\Skill;
use App\Models\LegalDocument;
use App\Models\Certification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\AiModerationService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FormResponseImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithEvents, ShouldQueue
{
    public $importId;
    public $fileName;
    protected $importType = null; // 'entrepreneur', 'intrapreneur', or null
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;
    private static bool $debugKeysDumped = false; // one-time column key debug

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
        return 1; // 1 row per job: each row can take 15-30s (Cloudinary + Gemini AI)
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
        if (!array_key_exists('selected', $row)) {
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
            // ── DEBUG: Log raw column keys on first row only ──
            if (!self::$debugKeysDumped) {
                self::$debugKeysDumped = true;
                $keysAndSamples = [];
                foreach ($row as $k => $v) {
                    $keysAndSamples[$k] = (string)($v ?? '');
                }
                Log::info('[FormResponseImport] DEBUG - Raw column keys from CSV (Maatwebsite snake_case):', $keysAndSamples);
            }

            // ── 1. Resolve email (required) ──
            $email = $this->col($row, 'email_address');
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skip("Missing/invalid Email Address");
                return null;
            }

            // Dynamically detect import type if it is null (e.g., if the filename was generic)
            if ($this->importType === null) {
                $hasEntrepreneur = !empty($this->col($row, 'business_name'));
                $hasIntrapreneur = !empty($this->col($row, 'company_name_', 'company_name'));
                
                if ($hasEntrepreneur && !$hasIntrapreneur) {
                    $this->importType = 'entrepreneur';
                    Log::info("[FormResponseImport] Dynamically detected Entrepreneur sheet from column content.");
                } elseif ($hasIntrapreneur && !$hasEntrepreneur) {
                    $this->importType = 'intrapreneur';
                    Log::info("[FormResponseImport] Dynamically detected Intrapreneur sheet from column content.");
                }
            }


            $fullName = $this->col($row, 'full_name');
            if (!$fullName) {
                $this->skip("Missing Full Name for {$email}");
                return null;
            }

            // ── 2. Create or update User ──
            $graduateYearRaw = $this->col($row, 'graduate_year');
            // graduate_year column can be: a 4-digit year ("2025") OR text like "Active Student".
            // Only treat a real 4-digit numeric year as graduation → alumni.
            $graduateYear = (is_numeric($graduateYearRaw) && strlen(trim((string)$graduateYearRaw)) === 4)
                            ? trim((string)$graduateYearRaw)
                            : null;

            // Determine student status: 'alumni' if graduate year is a 4-digit year or enrollment status contains 'alumni'
            $enrollmentStatus = strtolower($this->col($row, 'current_status') ?? '');
            $studentStatus = ($graduateYear || str_contains($enrollmentStatus, 'alumni')) ? 'alumni' : 'active';

            // The "Category" column holds the career category: Entrepreneur / Intrapreneur.
            $careerCategoryRaw = $this->col($row, 'category') ?? '';
            $careerCategory = null;
            if (str_contains(strtolower($careerCategoryRaw), 'intrapreneur')) {
                $careerCategory = 'Intrapreneur';
            } elseif (str_contains(strtolower($careerCategoryRaw), 'entrepreneur')) {
                $careerCategory = 'Entrepreneur';
            }

            $isFeatured = $this->parseSelectedFeatured($row);

            // Prevent cross-over wiping of featured states when importing two different files that contain all users
            if ($isFeatured !== null && $this->importType !== null && $careerCategory !== null) {
                $lowerCategory = strtolower($careerCategory);
                if ($this->importType === 'intrapreneur' && $lowerCategory === 'entrepreneur' && $isFeatured === false) {
                    // This is the Intrapreneur import file. This row is an Entrepreneur, and is marked FALSE here.
                    // Do NOT overwrite/wipe their existing featured status because this sheet is not the source of truth for Entrepreneurs.
                    $isFeatured = null;
                } elseif ($this->importType === 'entrepreneur' && $lowerCategory === 'intrapreneur' && $isFeatured === false) {
                    // This is the Entrepreneur import file. This row is an Intrapreneur, and is marked FALSE here.
                    // Do NOT overwrite/wipe their existing featured status because this sheet is not the source of truth for Intrapreneurs.
                    $isFeatured = null;
                }
            }

            // ── DEBUG: Log critical field values ──
            Log::debug('[FormResponseImport] Row debug', [
                'email'                  => $email,
                'category_raw'           => $row['category']      ?? '<<KEY MISSING>>',
                'current_status_raw'     => $row['current_status'] ?? '<<KEY MISSING>>',
                'graduate_year_raw'      => $graduateYearRaw,
                'graduate_year_resolved' => $graduateYear,
                'student_status'         => $studentStatus,
                'career_category'        => $careerCategory,
                'is_featured'            => $isFeatured,
                'selected_raw'           => $row['selected'] ?? '<<KEY MISSING>>',
                'business_name'          => $row['business_name']  ?? '<<KEY MISSING>>',
                'company_name'           => $row['company_name_']  ?? ($row['company_name'] ?? '<<KEY MISSING>>'),
            ]);

            $userData = [
                'submitted_at'      => $this->parseTimestamp($this->col($row, 'timestamp')),
                'prefix_title'      => $this->col($row, 'prefix_title'),
                'name'              => $fullName,
                'suffix_title'      => $this->col($row, 'suffix_title'),
                'personal_email'    => $this->col($row, 'personal_email_address'),
                'phone_number'      => $this->col($row, 'personal_phone_number'),
                'mobile_number'     => $this->col($row, 'personal_mobile_number'),
                'whatsapp'          => $this->col($row, 'personal_whatsapp'),
                'linkedin'          => $this->col($row, 'linkedin'),
                // "Category" column → Entrepreneur | Intrapreneur (career type)
                'current_status'    => $careerCategory,
                'nis'               => $this->col($row, 'nis_student_id'),
                'year_of_enrollment'=> $this->col($row, 'year_of_enrollment'),
                // Only store graduate_year if it's a real 4-digit year, not "Active Student"
                'graduate_year'     => $graduateYear,
                'major'             => $this->col($row, 'major'),
                'testimony'         => $this->col($row, 'testimony'),
                'profile_photo_url' => $this->col($row, 'professional_profile_photo'),
                'activities_doc_url'=> $this->col($row, 'professional_activities_documentation'),
                'expertise_certification_url' => $this->col($row, 'expertise_certification'),
                'student_status'    => $studentStatus,
                'email_verified_at' => now(),
            ];

            if ($isFeatured !== null) {
                $userData['is_featured'] = $isFeatured;
            }

            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                // Don't overwrite password on re-import
                $existingUser->update($userData);
                $user = $existingUser;
            } else {
                $user = User::create(array_merge($userData, [
                    'email'    => $email,
                    'password' => Hash::make('password123'),
                ]));
            }

            // Dispatch background job for profile photo
            $rawProfilePhoto = $this->col($row, 'professional_profile_photo');
            if ($rawProfilePhoto && !str_contains($rawProfilePhoto, 'cloudinary.com')) {
                \App\Jobs\UploadImageToCloudinaryJob::dispatch($user, 'profile_photo_url', $rawProfilePhoto, 'users', $fullName);
            }

            // Auto-approve imported testimonies (Bypass Gemini to avoid hitting rate limits on bulk imports)
            if (!empty($user->testimony) && is_null($user->ai_score)) {
                \Illuminate\Support\Facades\Log::info("[FormResponseImport] Auto-approving testimony for user: {$user->email} (Gemini AI skipped during import)");
                $user->update([
                    'ai_score' => 100,
                    'ai_sentiment' => 'Positive',
                    'is_visible' => true,
                    'ai_rejection_reason' => null,
                ]);
            }

            // ── 3. Handle Skills (M:N) ──
            $skillsRaw = $this->col($row, 'skills');
            if ($skillsRaw) {
                $skillIds = [];
                foreach ($this->splitComma($skillsRaw) as $skillName) {
                    try {
                        $skill = $this->getOrCreateSkill($skillName);
                        $skillIds[] = $skill->id;
                    } catch (\Exception $e) {
                        Log::warning("[FormResponseImport] Failed to get or create skill: " . $e->getMessage());
                    }
                }
                $user->skills()->syncWithoutDetaching($skillIds);
            }

            // ── 4. Determine path: Entrepreneur vs Intrapreneur ──
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
                        Log::warning("[FormResponseImport] Failed to get or create category: " . $e->getMessage());
                    }
                }

                $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

                $businessData = [
                    'category_id'             => $categoryId,
                    'position'                => $this->col($row, 'entrepreneur_position'),
                    'established_date'        => $this->parseDate($this->col($row, 'established_date')),
                    'description'             => $this->col($row, 'description'),
                    'province'                => $this->col($row, 'province'),
                    'city'                    => $this->col($row, 'city_regency', 'city_slash_regency'),
                    'address'                 => $this->col($row, 'full_address'),
                    'phone_number'            => $this->col($row, 'business_phone_number'),
                    'whatsapp'                => $this->col($row, 'business_whatsapp'),
                    'email'                   => $this->col($row, 'business_email_address'),
                    'website'                 => $this->col($row, 'website'),
                    'instagram'               => $this->col($row, 'instagram'),
                    'operational_status'      => $this->col($row, 'operational_status'),
                    'offering_type'           => $this->normalizeOfferingType($this->col($row, 'offering_type')),
                    'unique_value_proposition'=> $this->col($row, 'unique_value_proposition'),
                    'target_market'           => $this->col($row, 'target_market'),
                    'customer_base_size'      => $this->col($row, 'customer_base_size'),
                    'employee_count'          => $this->col($row, 'employee_count'),
                    'revenue_range'           => $this->col($row, 'revenue_range_per_year'),
                    'academic_heritage'       => $this->col($row, 'academic_heritage'),
                    'company_profile_url'     => $this->col($row, 'company_profile'),
                    'logo_url'                => $rawLogoUrl,
                    'business_scale'          => $this->col($row, 'business_scale'),
                    'business_legality'       => $this->col($row, 'business_legality'),
                    'product_legality'        => $this->col($row, 'product_legality'),
                    'type'                    => 'entrepreneur',
                    'approval_status'         => 'approved',
                    'is_visible'              => true,
                ];

                if ($isFeatured !== null) {
                    $businessData['is_featured'] = $isFeatured;
                }

                // Smart dedup: try exact match first, then fallback to user's first business
                $business = Business::where('user_id', $user->id)->where('name', $businessName)->first()
                    ?? Business::where('user_id', $user->id)->where('type', 'entrepreneur')->first();

                if ($business) {
                    $business->update($businessData);
                } else {
                    $business = Business::create(array_merge($businessData, [
                        'user_id' => $user->id,
                        'name'    => $businessName,
                    ]));
                }

                // Dispatch background job for logo upload
                if ($rawLogoUrl && !str_contains($rawLogoUrl, 'cloudinary.com')) {
                    \App\Jobs\UploadImageToCloudinaryJob::dispatch($business, 'logo_url', $rawLogoUrl, 'businesses', $businessName);
                }

                // Sync pivot: this user is a member of this business
                $business->members()->syncWithoutDetaching([
                    $user->id => ['position' => $this->col($row, 'entrepreneur_position')],
                ]);

                // ── Products (up to 3) ──
                $this->importProducts($business, $row);

                // ── Legal Documents (M:N) ──
                $legalRaw = $this->col($row, 'legal_documents');
                if ($legalRaw) {
                    $ids = [];
                    foreach ($this->splitComma($legalRaw) as $docName) {
                        try {
                            $doc = $this->getOrCreateLegalDocument($docName);
                            $ids[] = $doc->id;
                        } catch (\Exception $e) {
                            Log::warning("[FormResponseImport] Failed to get or create legal doc: " . $e->getMessage());
                        }
                    }
                    $business->legalDocuments()->syncWithoutDetaching($ids);
                }

                // ── Certifications (M:N) ──
                $certRaw = $this->col($row, 'business_certification', 'certification');
                if ($certRaw) {
                    $ids = [];
                    foreach ($this->splitComma($certRaw) as $certName) {
                        try {
                            $cert = $this->getOrCreateCertification($certName);
                            $ids[] = $cert->id;
                        } catch (\Exception $e) {
                            Log::warning("[FormResponseImport] Failed to get or create certification: " . $e->getMessage());
                        }
                    }
                    $business->certifications()->syncWithoutDetaching($ids);
                }
            }

            // ── 4b. Intrapreneur → Company ──
            // Note: CSV header "Company Name " (with trailing space) → Maatwebsite key = "company_name_"
            $companyName = $this->col($row, 'company_name_', 'company_name');
            if ($companyName) {
                // Company names always UPPERCASE
                $companyName = strtoupper(trim($companyName));

                $industryCatId = null;
                $indCatName = $this->col($row, 'industry_category');
                if ($indCatName) {
                    try {
                        $indCat = $this->getOrCreateCategory($indCatName);
                        $industryCatId = $indCat->id;
                    } catch (\Exception $e) {
                        Log::warning("[FormResponseImport] Failed to get or create industry category: " . $e->getMessage());
                    }
                }

                $rawLogoUrl = $this->col($row, 'businesscompany_logo', 'business_company_logo');

                $companyData = [
                    'category_id'          => $industryCatId,
                    'position'             => $this->col($row, 'intrapreneur_position'),
                    'level_position'       => $this->col($row, 'level_position'),
                    'job_description'      => $this->col($row, 'job_description'),
                    'year_started_working' => $this->col($row, 'year_started_working'),
                    'achievement'          => $this->col($row, 'achievement'),
                    'company_scale'        => $this->col($row, 'company_scale'),
                    'logo_url'             => $rawLogoUrl,
                    'is_visible'           => true,
                ];

                // Smart dedup for companies too
                $company = Company::where('user_id', $user->id)->where('name', $companyName)->first()
                    ?? Company::where('user_id', $user->id)->first();

                if ($company) {
                    $company->update(array_merge($companyData, ['name' => $companyName]));
                } else {
                    $company = Company::create(array_merge($companyData, [
                        'user_id' => $user->id,
                        'name'    => $companyName,
                    ]));
                }

                // Dispatch background job for company logo
                if ($rawLogoUrl && !str_contains($rawLogoUrl, 'cloudinary.com')) {
                    \App\Jobs\UploadImageToCloudinaryJob::dispatch($company, 'logo_url', $rawLogoUrl, 'companies', $companyName);
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
        // Delete existing products for this business to avoid duplicates on re-import
        $business->products()->delete();

        $productSlots = [
            1 => [
                'name'  => $this->col($row, 'productservice_name_1', 'product_service_name_1'),
                'desc'  => $this->col($row, 'productservice_description_1', 'product_service_description_1'),
                'price' => $this->col($row, 'productservice_price_1', 'product_service_price_1'),
                'photo' => $this->col($row, 'productservice_photo_1', 'product_service_photo_1'),
            ],
            2 => [
                'name'  => $this->col($row, 'productservice_name_2', 'product_service_name_2'),
                'desc'  => $this->col($row, 'productservice_description_2', 'product_service_description_2'),
                'price' => $this->col($row, 'productservice_price_2', 'product_service_price_2'),
                'photo' => $this->col($row, 'productservice_photo_2', 'product_service_photo_2'),
            ],
            3 => [
                'name'  => $this->col($row, 'productservice_name_3', 'product_service_name_3'),
                'desc'  => $this->col($row, 'productservice_description_3', 'product_service_description_3'),
                'price' => $this->col($row, 'productservice_price_3', 'product_service_price_3'),
                'photo' => $this->col($row, 'productservice_photo_3', 'product_service_photo_3'),
            ],
        ];

        $productType = ($business->offering_type === 'service') ? 'service' : 'product';

        foreach ($productSlots as $order => $slot) {
            if (empty($slot['name'])) continue;

            $rawPhotoUrl = $slot['photo'];

            $product = Product::create([
                'business_id' => $business->id,
                'type'        => $productType,
                'name'        => $slot['name'],
                'description' => $slot['desc'],
                'price'       => $slot['price'],
                'photo_url'   => $rawPhotoUrl,
                'sort_order'  => $order,
            ]);

            if ($rawPhotoUrl && !str_contains($rawPhotoUrl, 'cloudinary.com')) {
                \App\Jobs\UploadImageToCloudinaryJob::dispatch($product, 'photo_url', $rawPhotoUrl, 'products', $slot['name']);
            }
        }
    }

    // ─── Helpers ───

    private function normalizeOfferingType(?string $val): string
    {
        if (!$val) return 'product';
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

    private function parseDate(?string $val): ?string
    {
        if (!$val) return null;
        
        // If it's just a year (4 digits), convert to YYYY-01-01 to avoid Carbon guessing current day/month
        if (preg_match('/^\d{4}$/', trim($val))) {
            return trim($val) . '-01-01';
        }

        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseTimestamp(?string $val): ?string
    {
        if (!$val) return null;
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
        \Illuminate\Support\Facades\Log::warning("Skipped row: " . $msg);
        $this->updateProgress('skipped');

        if ($this->importId) {
            $prefix = "import_{$this->importId}";
            $progress = \Illuminate\Support\Facades\Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
            if (!is_array($progress)) {
                $progress = ['status' => 'processing', 'errors' => []];
            }
            if (!isset($progress['errors']) || !is_array($progress['errors'])) {
                $progress['errors'] = [];
            }
            $progress['errors'][] = $msg;
            \Illuminate\Support\Facades\Cache::put($prefix, $progress, now()->addMinutes(60));
        }
    }

    private function updateProgress(string $status): void
    {
        if (!$this->importId) return;

        $prefix = "import_{$this->importId}";
        Cache::increment("{$prefix}_current");

        if ($status === 'success') {
            Cache::increment("{$prefix}_success");
        } else {
            Cache::increment("{$prefix}_skipped");
        }
    }

    /**
     * Download image from URL (with cookie auth for employee.uc.ac.id) and upload to Cloudinary.
     */
    private function uploadToCloudinary(?string $url, string $folder, ?string $identifier): ?string
    {
        $url = $this->cleanUrl($url);
        if (!$url) return null;

        if (str_contains($url, 'cloudinary.com') || str_contains($url, 'res.cloudinary.com')) {
            return $url;
        }

        // Cache lookup to prevent duplicate downloads/uploads
        $cacheKey = 'cloudinary_upload_' . md5($url);
        if (Cache::has($cacheKey)) {
            $cachedUrl = Cache::get($cacheKey);
            if ($cachedUrl) {
                return $cachedUrl;
            }
        }

        $tmpFile = null;
        try {
            Log::debug("[FormResponseImport] Attempting download: " . $url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, (string)$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            
            $curlHeaders = [
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer: https://employee.uc.ac.id/index.php/login',
            ];
            
            $isUniversityPortal = str_contains($url, 'employee.uc.ac.id') || str_contains($url, 'employee.ciputra.ac.id');
            if ($isUniversityPortal) {
                $cookie = config('services.uc.cookie_raw', '');
                if ($cookie) $curlHeaders[] = "Cookie: " . trim($cookie);
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($contents === false || $httpCode !== 200 || !str_contains(strtolower($contentType ?? ''), 'image')) {
                Log::warning("[FormResponseImport] Download failed or invalid content type for {$url}. HTTP status: {$httpCode}, Content-Type: {$contentType}");
                return $url;
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'uco_img_');
            file_put_contents($tmpFile, $contents);
            
            // Compress to WebP & Resize to max 1200px before uploading
            $tmpFile = $this->compressToWebp($tmpFile);
            
            $sanitizedId = Str::slug($identifier ?? 'unknown');
            $urlHash = substr(md5($url), 0, 8);
            $publicId = "uco/{$folder}/{$sanitizedId}_{$urlHash}";

            $uploadResult = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($tmpFile, [
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image'
            ]);

            if ($tmpFile && file_exists($tmpFile)) unlink($tmpFile);

            $secureUrl = $uploadResult['secure_url'] ?? $url;
            if ($secureUrl !== $url) {
                Log::info("[FormResponseImport] Uploaded to Cloudinary: " . $secureUrl);
                Cache::put($cacheKey, $secureUrl, now()->addDays(30));
            } else {
                Log::warning("[FormResponseImport] Cloudinary upload returned original URL for: " . $url);
            }
            return $secureUrl;

        } catch (\Throwable $e) {
            if ($tmpFile && file_exists($tmpFile)) unlink($tmpFile);
            Log::error("[FormResponseImport] Image error for {$url}: " . $e->getMessage());
            return $url;
        }
    }

    /**
     * Compress image to WebP and resize to a maximum of 1200px.
     */
    private function compressToWebp(string $filePath, int $quality = 80): string
    {
        ini_set('memory_limit', '512M');
        if (!extension_loaded('gd')) {
            return $filePath;
        }

        try {
            $imageInfo = @getimagesize($filePath);
            if (!$imageInfo) {
                return $filePath;
            }

            $mime = $imageInfo['mime'];
            
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = @imagecreatefromjpeg($filePath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($filePath);
                    break;
                case 'image/gif':
                    $image = @imagecreatefromgif($filePath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($filePath);
                    break;
                default:
                    $data = file_get_contents($filePath);
                    $image = @imagecreatefromstring($data);
                    break;
            }

            if (!$image) {
                return $filePath;
            }

            // Convert palette images to truecolor to prevent "Palette image not supported by webp" error
            if (!imageistruecolor($image)) {
                $trueColorImage = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagealphablending($trueColorImage, false);
                imagesavealpha($trueColorImage, true);
                imagecopy($trueColorImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $trueColorImage;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxDim = 1200;
            if ($width > $maxDim || $height > $maxDim) {
                if ($width > $height) {
                    $newWidth = $maxDim;
                    $newHeight = (int)($height * ($maxDim / $width));
                } else {
                    $newHeight = $maxDim;
                    $newWidth = (int)($width * ($maxDim / $height));
                }
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resizedImage;
            }

            $outputPath = $filePath . '.webp';
            if (imagewebp($image, $outputPath, $quality)) {
                imagedestroy($image);
                unlink($filePath);
                return $outputPath;
            }
            
            imagedestroy($image);
        } catch (\Throwable $e) {
            Log::warning("[FormResponseImport] Image compression to webp failed: " . $e->getMessage());
        }

        return $filePath;
    }

    /**
     * Clean and convert URLs (including Google Drive).
     */
    private function cleanUrl(?string $url): ?string
    {
        if (!$url) return null;
        $url = strip_tags($url);
        $url = preg_replace('/[^\x20-\x7E]/', '', $url);
        $url = trim($url);

        if (preg_match('/(https?:\/\/.*)$/i', $url, $matches)) {
            $url = $matches[1];
        }

        // Google Drive link conversion
        if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
            if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]+)/', $url, $matches)) {
                // Use export=download&confirm=t to bypass virus scan warning HTML page so cURL downloads the actual image bytes
                $url = "https://drive.google.com/uc?export=download&confirm=t&id=" . $matches[1];
            }
        }

        return (filter_var($url, FILTER_VALIDATE_URL)) ? $url : null;
    }

    // ─── Events ───

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                if (!$this->importId) return;
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
                if (!$this->importId) return;
                $prefix = "import_{$this->importId}";
                $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
                $progress['status'] = 'completed';
                Cache::put($prefix, $progress, now()->addMinutes(60));
                Log::info("[FormResponseImport] Completed");
            },
        ];
    }

    private function getOrCreateSkill(string $skillName): Skill
    {
        $slug = Str::slug($skillName);
        if (!$slug) {
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
                'name' => trim($skillName)
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
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
        if (!$slug) {
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
                'name' => trim($categoryName)
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
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
        } catch (\Illuminate\Database\QueryException $e) {
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
        } catch (\Illuminate\Database\QueryException $e) {
            $cert = Certification::where('name', $name)->first();
            if ($cert) {
                return $cert;
            }
            throw $e;
        }
    }

    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skippedCount,
            'errors'  => $this->errors,
        ];
    }
}
