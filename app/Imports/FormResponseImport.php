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
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;

    public function __construct($importId = null)
    {
        $this->importId = $importId;
    }

    public function chunkSize(): int
    {
        return 50;
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
     * Process each CSV row → creates User + (Business OR Company) + Products + pivots.
     */
    public function model(array $row)
    {
        try {
            // ── 1. Resolve email (required) ──
            $email = $this->col($row, 'email_address');
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skip("Missing/invalid Email Address");
                return null;
            }

            $fullName = $this->col($row, 'full_name');
            if (!$fullName) {
                $this->skip("Missing Full Name for {$email}");
                return null;
            }

            // ── 2. Create or update User ──
            $graduateYear = $this->col($row, 'graduate_year');
            $studentStatus = $graduateYear ? 'alumni' : 'active';

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
                'current_status'    => $this->col($row, 'current_status'),
                'nis'               => $this->col($row, 'nis_student_id'),
                'year_of_enrollment'=> $this->col($row, 'year_of_enrollment'),
                'graduate_year'     => $graduateYear,
                'major'             => $this->col($row, 'major'),
                'testimony'         => $this->col($row, 'testimony'),
                'cv_url'            => $this->col($row, 'curriculum_vitae'),
                'profile_photo_url' => $this->uploadToCloudinary($this->col($row, 'professional_profile_photo'), 'users', $fullName),
                'activities_doc_url'=> $this->col($row, 'professional_activities_documentation'),
                'student_status'    => $studentStatus,
                'email_verified_at' => now(),
            ];

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

            // Analyze testimony using AI
            if (!empty($user->testimony)) {
                $aiService = app(AiModerationService::class);
                $rating = 5; // Default assumption for imported ones if no rating
                $result = $aiService->analyze($user->testimony, $rating, $user->name);

                $user->update([
                    'ai_score' => $result['sentiment_score'],
                    'ai_sentiment' => $result['sentiment'],
                    'is_visible' => $result['is_approved'],
                    'ai_rejection_reason' => $result['rejection_reason'],
                ]);
            }

            // ── 3. Handle Skills (M:N) ──
            $skillsRaw = $this->col($row, 'skills');
            if ($skillsRaw) {
                $skillIds = [];
                foreach ($this->splitComma($skillsRaw) as $skillName) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillName],
                        ['slug' => Str::slug($skillName)]
                    );
                    $skillIds[] = $skill->id;
                }
                $user->skills()->syncWithoutDetaching($skillIds);
            }

            // ── 4. Determine path: Entrepreneur vs Intrapreneur ──
            $status = strtolower($this->col($row, 'current_status') ?? '');

            // ── 4a. Entrepreneur → Business ──
            $businessName = $this->col($row, 'business_name');
            if ($businessName) {
                $categoryId = null;
                $catName = $this->col($row, 'category');
                if ($catName) {
                    $cat = Category::firstOrCreate(
                        ['name' => $catName],
                        ['slug' => Str::slug($catName)]
                    );
                    $categoryId = $cat->id;
                }

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
                    'offering_type'           => $this->col($row, 'offering_type'),
                    'unique_value_proposition'=> $this->col($row, 'unique_value_proposition'),
                    'target_market'           => $this->col($row, 'target_market'),
                    'customer_base_size'      => $this->col($row, 'customer_base_size'),
                    'employee_count'          => $this->col($row, 'employee_count'),
                    'revenue_range'           => $this->col($row, 'revenue_range_per_year'),
                    'academic_heritage'       => $this->col($row, 'academic_heritage'),
                    'company_profile_url'     => $this->col($row, 'company_profile'),
                    'logo_url'                => $this->uploadToCloudinary($this->col($row, 'businesscompany_logo', 'business_company_logo'), 'businesses', $businessName),
                    'business_scale'          => $this->col($row, 'business_scale'),
                    'business_legality'       => $this->col($row, 'business_legality'),
                    'product_legality'        => $this->col($row, 'product_legality'),
                    'type'                    => 'entrepreneur',
                ];

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
                        $doc = LegalDocument::firstOrCreate(['name' => $docName]);
                        $ids[] = $doc->id;
                    }
                    $business->legalDocuments()->syncWithoutDetaching($ids);
                }

                // ── Certifications (M:N) ──
                $certRaw = $this->col($row, 'certification');
                if ($certRaw) {
                    $ids = [];
                    foreach ($this->splitComma($certRaw) as $certName) {
                        $cert = Certification::firstOrCreate(['name' => $certName]);
                        $ids[] = $cert->id;
                    }
                    $business->certifications()->syncWithoutDetaching($ids);
                }
            }

            // ── 4b. Intrapreneur → Company ──
            $companyName = $this->col($row, 'company_name');
            if ($companyName) {
                // Company names always UPPERCASE
                $companyName = strtoupper(trim($companyName));

                $industryCatId = null;
                $indCatName = $this->col($row, 'industry_category');
                if ($indCatName) {
                    $indCat = Category::firstOrCreate(
                        ['name' => $indCatName],
                        ['slug' => Str::slug($indCatName)]
                    );
                    $industryCatId = $indCat->id;
                }

                $companyData = [
                    'category_id'          => $industryCatId,
                    'position'             => $this->col($row, 'intrapreneur_position'),
                    'job_description'      => $this->col($row, 'job_description'),
                    'year_started_working' => $this->col($row, 'year_started_working'),
                    'achievement'          => $this->col($row, 'achievement'),
                    'company_scale'        => $this->col($row, 'company_scale'),
                    'logo_url'             => $this->col($row, 'businesscompany_logo', 'business_company_logo'),
                ];

                // Smart dedup for companies too
                $company = Company::where('user_id', $user->id)->where('name', $companyName)->first()
                    ?? Company::where('user_id', $user->id)->first();

                if ($company) {
                    $company->update(array_merge($companyData, ['name' => $companyName]));
                } else {
                    Company::create(array_merge($companyData, [
                        'user_id' => $user->id,
                        'name'    => $companyName,
                    ]));
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
                'photo' => null, // No Photo (3) in CSV
            ],
        ];

        foreach ($productSlots as $order => $slot) {
            if (empty($slot['name'])) continue;

            Product::create([
                'business_id' => $business->id,
                'name'        => $slot['name'],
                'description' => $slot['desc'],
                'price'       => $slot['price'],
                'photo_url'   => $this->uploadToCloudinary($slot['photo'], 'products', $slot['name']),
                'sort_order'  => $order,
            ]);
        }
    }

    // ─── Helpers ───

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

        $tmpFile = null;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, (string)$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
            $curlHeaders = [
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Referer: https://employee.uc.ac.id/index.php/login',
            ];
            
            $isUniversityPortal = str_contains($url, 'employee.uc.ac.id') || str_contains($url, 'employee.ciputra.ac.id');
            if ($isUniversityPortal) {
                $cookie = env('UC_COOKIE_RAW', '');
                if ($cookie) $curlHeaders[] = "Cookie: " . trim($cookie);
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($contents === false || $httpCode !== 200 || !str_contains(strtolower($contentType ?? ''), 'image')) {
                return $url;
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'uco_img_');
            file_put_contents($tmpFile, $contents);
            
            $sanitizedId = Str::slug($identifier ?? 'unknown');
            $publicId = "uco/{$folder}/{$sanitizedId}/" . Str::random(10);

            $uploadResult = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($tmpFile, [
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image'
            ]);

            if ($tmpFile && file_exists($tmpFile)) unlink($tmpFile);

            return $uploadResult['secure_url'] ?? $url;

        } catch (\Throwable $e) {
            if ($tmpFile && file_exists($tmpFile)) unlink($tmpFile);
            Log::error("[FormResponseImport] Image error for {$url}: " . $e->getMessage());
            return $url;
        }
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

    public function getResults(): array
    {
        return [
            'success' => $this->successCount,
            'skipped' => $this->skippedCount,
            'errors'  => $this->errors,
        ];
    }
}
