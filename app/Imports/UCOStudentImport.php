<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Business;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Importer for "UCO Student Profile - With Data.csv"
 *
 * CSV layout (3-row header junk, real data from row 4):
 *   Row 1: empty
 *   Row 2: source filenames
 *   Row 3: column headers (duplicate column names across left+right merged sheets)
 *   Row 4+: data
 *
 * Because the CSV is a MERGE of two sheets side-by-side, column names duplicate.
 * We use positional (0-indexed) column access to avoid ambiguity.
 *
 * LEFT side (student list, cols 0–16):
 *   0  NIS
 *   1  Prodi
 *   2  Sub Prodi
 *   3  Name                  ← student name
 *   4  Edu Level
 *   5  Email                 ← personal email
 *   6  Phone 1
 *   7  Mobile 1
 *   8  Whatsapp
 *   9  Instagram
 *   10 Student Year
 *   11 Official Email        ← login email
 *   12 Current Status
 *   13 Business Name         ← legacy business name (left side)
 *   14 Business Line
 *   15 Business title
 *   16 Major
 *
 * RIGHT side (business/company data, cols 17+):
 *   17 NIM
 *   18 Name                  ← business person name (often different)
 *   19 Phone
 *   20 Mobile
 *   21 Email                 ← business person email
 *   22 Nama Perusahaan... (intrapreneur company name)
 *   23 Posisi saat ini (jika intraprenuer)
 *   24 Tanggal Mulai Aktif Bekerja
 *   25 Deskripsi Perusahaan Tempat Bekerja
 *   26 Kategori perusahaan (intrapreneur)
 *   27 Jenis usaha (intrapreneur)
 *   28 Apakah bisnis lanjutan dari project SEH?
 *   29 Apakah menjalankan >1 bisnis?
 *   30 Kategori perusahaan Anda/Orang tua (entrepreneur category)
 *   31 Jenis bisnis/ventura
 *   32 Omzet per tahun
 *   33 Tantangan
 *   34 Nama bisnis/ventura    ← entrepreneur business name
 *   35 Legalitas usaha
 *   36 Legalitas produk
 *   37 Tgl berdiri
 *   38 Posisi saat ini (entrepreneur)
 *   39 Website/social media
 *   40 Alamat usaha
 *   41 Jumlah Karyawan
 *   42 Deskripsi bisnis
 *   43 Logo Usaha/perusahaan
 *   44 Foto Produk
 *   45 Foto Diri/Kelompok
 *   46 Apakah aktivitas akan dilanjutkan?
 *   47 Apakah aktivitas lanjutan dari SEH?
 *   48 Website/Social Media terkait aktivitas
 *   49 Foto Pribadi
 */
class UCOStudentImport implements ToArray, WithStartRow, WithChunkReading, WithEvents, ShouldQueue
{
    public $importId;
    protected $successCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct($importId = null)
    {
        $this->importId = $importId;
    }

    // Skip rows 1-3 (junk + headers), start reading data from row 4
    public function startRow(): int
    {
        return 4;
    }

    public function chunkSize(): int
    {
        return 1; // 1 row per job to avoid queue timeout
    }

    // ─── Main processor ────────────────────────────────────────────────────────

    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    private function processRow(array $row): void
    {
        try {
            // Helper: get cell by index, return null if empty/dash
            $cell = function (int $i) use ($row): ?string {
                $val = $row[$i] ?? null;
                if ($val === null) return null;
                $str = trim((string) $val);
                return ($str === '' || $str === '-') ? null : $str;
            };

            // ── LEFT SIDE: student data ──
            $nis           = $cell(0);
            $studentName   = $cell(3);
            $personalEmail = $cell(5);
            $phone1        = $cell(6);
            $mobile1       = $cell(7);
            $whatsapp      = $cell(8);
            $instagram     = $cell(9);
            $studentYear   = $cell(10);
            $officialEmail = $cell(11);
            $currentStatus = strtolower($cell(12) ?? '');
            $legacyBizName = $cell(13); // old business name column
            $major         = $cell(16);

            // ── Email: prefer official, fall back to personal, synthesize from NIS ──
            $loginEmail = null;
            if ($officialEmail && filter_var($officialEmail, FILTER_VALIDATE_EMAIL)) {
                $loginEmail = strtolower($officialEmail);
            } elseif ($personalEmail && filter_var($personalEmail, FILTER_VALIDATE_EMAIL)) {
                $loginEmail = strtolower($personalEmail);
            } elseif ($nis) {
                $loginEmail = strtolower($nis) . '@student.ciputra.ac.id';
            }

            if (!$loginEmail) {
                $this->skip("Row has no email and no NIS: " . implode('|', array_slice($row, 0, 5)));
                return;
            }

            if (!$studentName) {
                $this->skip("No student name for {$loginEmail}");
                return;
            }

            // ── Student status ──
            $studentStatus = match(true) {
                str_contains($currentStatus, 'alumni') || str_contains($currentStatus, 'lulus') => 'alumni',
                str_contains($currentStatus, 'drop') || str_contains($currentStatus, 'putus')   => 'inactive',
                str_contains($currentStatus, 'non-aktif') || str_contains($currentStatus, 'cuti') => 'inactive',
                default => 'active',
            };

            // ── Upsert User ──
            // Upload profile photo to Cloudinary if it's an employee URL
            $profilePhotoUrl = $this->uploadToCloudinary($cell(49), 'users', $nis ?? $loginEmail);

            $userData = array_filter([
                'name'               => $studentName,
                'nis'                => $nis,
                'phone_number'       => $this->cleanPhone($phone1),
                'mobile_number'      => $this->cleanPhone($mobile1),
                'whatsapp'           => $this->cleanPhone($whatsapp),
                'personal_email'     => $personalEmail,
                'major'              => $major,
                'student_status'     => $studentStatus,
                'year_of_enrollment' => $studentYear,
                'profile_photo_url'  => $profilePhotoUrl,
                'email_verified_at'  => now(),
                'is_visible'         => true,
            ], fn($v) => $v !== null);

            $user = User::where('email', $loginEmail)->first();
            if ($user) {
                $user->update($userData);
            } else {
                $user = User::create(array_merge($userData, [
                    'email'    => $loginEmail,
                    'password' => Hash::make('password123'),
                ]));
            }

            // ── RIGHT SIDE: business/company data ──

            // Company (Intrapreneur) — col 22
            $companyName = $cell(22);
            if ($companyName) {
                $companyName = $this->cleanName($companyName);
                $this->upsertCompany($user, $companyName, $cell, $row);
            }

            // Business (Entrepreneur) — col 34 (detailed), or col 13 (legacy)
            $entrepreneurBizName = $cell(34) ?? $legacyBizName;
            if ($entrepreneurBizName) {
                $entrepreneurBizName = $this->cleanName($entrepreneurBizName);
                $this->upsertBusiness($user, $entrepreneurBizName, $cell);
            }

            $this->successCount++;
            $this->updateProgress('success');

        } catch (\Exception $e) {
            $this->skip("Exception: {$e->getMessage()}");
            Log::error("[UCOStudentImport] Error: {$e->getMessage()}", [
                'row_preview' => array_slice($row, 0, 10),
            ]);
        }
    }

    // ─── Company upsert ────────────────────────────────────────────────────────

    private function upsertCompany(User $user, string $companyName, callable $cell, array $row): void
    {
        $companyName = strtoupper(trim($companyName));

        $categoryId = null;
        $catName = $cell(26) ?? $cell(27); // kategori or jenis usaha
        if ($catName) {
            $slug = Str::slug($catName);
            if ($slug) {
                $cat = Category::firstOrCreate(['slug' => $slug], ['name' => trim($catName)]);
                $categoryId = $cat->id;
            }
        }

        $logoUrl = $this->uploadToCloudinary($cell(43), 'companies', $companyName);

        $data = array_filter([
            'category_id'          => $categoryId,
            'position'             => $cell(23),
            'job_description'      => $cell(25),
            'year_started_working' => $cell(24),
            'logo_url'             => $logoUrl,
        ], fn($v) => $v !== null);

        $company = Company::where('user_id', $user->id)->where('name', $companyName)->first()
            ?? Company::where('user_id', $user->id)->first();

        if ($company) {
            $company->update(array_merge($data, ['name' => $companyName]));
        } else {
            Company::create(array_merge($data, ['user_id' => $user->id, 'name' => $companyName]));
        }
    }

    // ─── Business upsert ───────────────────────────────────────────────────────

    private function upsertBusiness(User $user, string $bizName, callable $cell): void
    {
        $categoryId = null;
        $catName = $cell(30) ?? $cell(31); // Kategori perusahaan or Jenis bisnis/ventura
        if ($catName) {
            $slug = Str::slug($catName);
            if ($slug) {
                $cat = Category::firstOrCreate(['slug' => $slug], ['name' => trim($catName)]);
                $categoryId = $cat->id;
            }
        }

        $logoUrl = $this->uploadToCloudinary($cell(43), 'businesses', $bizName);

        $data = array_filter([
            'category_id'       => $categoryId,
            'position'          => $cell(38),
            'established_date'  => $this->parseDate($cell(37)),
            'description'       => $cell(42),
            'address'           => $cell(40),
            'website'           => $cell(39),
            'employee_count'    => $cell(41),
            'revenue_range'     => $cell(32),
            'business_legality' => $cell(35),
            'product_legality'  => $cell(36),
            'academic_heritage' => $cell(10) ? 'Batch ' . $cell(10) : null,
            'logo_url'          => $logoUrl,
            'type'              => 'entrepreneur',
        ], fn($v) => $v !== null);

        $business = Business::where('user_id', $user->id)->where('name', $bizName)->first()
            ?? Business::where('user_id', $user->id)->where('type', 'entrepreneur')->first();

        if ($business) {
            $business->update($data);
        } else {
            $business = Business::create(array_merge($data, [
                'user_id' => $user->id,
                'name'    => $bizName,
            ]));
        }

        // Pivot: link this user as member
        $business->members()->syncWithoutDetaching([
            $user->id => ['position' => $cell(38)],
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function cleanPhone(?string $val): ?string
    {
        if (!$val) return null;
        $val = preg_replace('/[^0-9+]/', '', $val);
        return strlen($val) >= 6 ? $val : null;
    }

    /**
     * Clean a name field: strip <br> tags, take first entry if multiple, truncate to 250 chars.
     */
    private function cleanName(?string $val): ?string
    {
        if (!$val) return null;
        // Split on <br> variants and take first non-empty entry
        $val = str_replace(["\r", "\n"], ' ', $val);
        $parts = preg_split('/\s*<br\s*\/?\s*>\s*/i', $val);
        $parts = array_filter(array_map('trim', $parts), fn($p) => $p !== '');
        $first = reset($parts) ?: $val;
        // Strip any remaining tags and truncate
        $first = trim(strip_tags($first));
        return Str::limit($first, 250, '');
    }

    /**
     * Download image from URL (with cookie auth for employee.uc.ac.id) and upload to Cloudinary.
     * Returns the Cloudinary URL, or the original URL if upload fails.
     */
    private function uploadToCloudinary(?string $url, string $folder, ?string $identifier): ?string
    {
        $url = $this->cleanUrl($url);
        if (!$url) return null;

        // Already a Cloudinary URL — pass through
        if (str_contains($url, 'cloudinary.com') || str_contains($url, 'res.cloudinary.com')) {
            return $url;
        }

        // Cache lookup to prevent duplicate downloads/uploads
        $cacheKey = 'cloudinary_upload_' . md5($url);
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $cachedUrl = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cachedUrl) {
                return $cachedUrl;
            }
        }

        $tmpFile = null;
        try {
            Log::debug("[UCOStudentImport] Attempting native curl download: " . $url);

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
                'Accept-Language: en-US,en;q=0.9,id;q=0.8',
                'Referer: https://employee.uc.ac.id/index.php/login',
                'Connection: keep-alive',
            ];
            
            // The session is often tied to either employee.uc.ac.id or employee.ciputra.ac.id
            $isUniversityPortal = str_contains($url, 'employee.uc.ac.id') || str_contains($url, 'employee.ciputra.ac.id');
            
            if ($isUniversityPortal) {
                $cookie = env('UC_COOKIE_RAW', '');
                if ($cookie) {
                    $curlHeaders[] = "Cookie: " . trim($cookie);
                }
            }
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
            
            $contents = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $curlError = curl_error($ch);
            
            // FALLBACK: If we got HTML from employee.uc.ac.id, try swapping to employee.ciputra.ac.id
            // as the cookie is often domain-specific to ciputra.ac.id
            if (str_contains($url, 'employee.uc.ac.id') && str_contains(strtolower($contentType ?? ''), 'text/html')) {
                $fallbackUrl = str_replace('employee.uc.ac.id', 'employee.ciputra.ac.id', $url);
                Log::debug("[UCOStudentImport] Detected HTML redirect on uc.ac.id. Trying fallback to ciputra.ac.id: {$fallbackUrl}");
                
                curl_setopt($ch, CURLOPT_URL, $fallbackUrl);
                $contents = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $curlError = curl_error($ch);
            }
            
            curl_close($ch);

            Log::debug("[UCOStudentImport] Download result: Code={$httpCode}, Type={$contentType}, Size=" . ($contents ? strlen($contents) : 0));

            if ($contents === false || $httpCode !== 200 || strlen($contents) < 50) {
                Log::warning("[UCOStudentImport] Download failed. Code: {$httpCode}, Error: {$curlError}, Size: " . ($contents ? strlen($contents) : 0));
                return $url;
            }

            // Log the start of the content to see if it's HTML or real binary
            $snippet = substr($contents, 0, 200);
            Log::debug("[UCOStudentImport] Content snippet (hex): " . bin2hex($snippet));
            Log::debug("[UCOStudentImport] Content snippet (text): " . preg_replace('/[^\x20-\x7E]/', '.', $snippet));

            // Verify it's a valid image using GD
            $imageInfo = @getimagesizefromstring($contents);
            if (!$imageInfo) {
                Log::warning("[UCOStudentImport] getimagesizefromstring failed. Not a valid image or corrupted. URL: {$url}");
                // We'll still check the contentType, but this is a strong indicator of failure
            } else {
                Log::debug("[UCOStudentImport] Valid image detected: {$imageInfo[0]}x{$imageInfo[1]} ({$imageInfo['mime']})");
            }

            // Check if it's actually an image
            if (!str_contains(strtolower($contentType ?? ''), 'image')) {
                Log::warning("[UCOStudentImport] Downloaded content is not an image. Content-Type: {$contentType}. URL: {$url}");
                return $url;
            }

            // Save to temp file
            $tmpFile = tempnam(sys_get_temp_dir(), 'uco_img_');
            file_put_contents($tmpFile, $contents);
            unset($contents); // Free memory immediately
            gc_collect_cycles();

            // Compress to WebP & Resize to max 1200px before uploading
            $tmpFile = $this->compressToWebp($tmpFile);

            // Prepare Cloudinary public_id
            $sanitizedId = Str::slug($identifier ?? 'unknown');
            $urlHash = substr(md5($url), 0, 8);
            $publicId = "uco/{$folder}/{$sanitizedId}_{$urlHash}";

            Log::debug("[UCOStudentImport] Uploading to Cloudinary: " . $publicId);

            // Use the Cloudinary SDK directly to bypass Storage facade issues
            // This assumes the 'cloudinary-labs/cloudinary-laravel' package is installed
            // which provides the \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary facade.
            $uploadResult = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload($tmpFile, [
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image'
            ]);

            if ($tmpFile && file_exists($tmpFile)) {
                unlink($tmpFile);
            }

            if (isset($uploadResult['secure_url'])) {
                Log::info("[UCOStudentImport] Uploaded to Cloudinary: " . $uploadResult['secure_url']);
                \Illuminate\Support\Facades\Cache::put($cacheKey, $uploadResult['secure_url'], now()->addDays(30));
                return $uploadResult['secure_url'];
            }

            Log::warning("[UCOStudentImport] Cloudinary upload returned no secure_url for {$publicId}");
            return $url;

        } catch (\Throwable $e) {
            if ($tmpFile && file_exists($tmpFile)) {
                unlink($tmpFile);
            }
            Log::error("[UCOStudentImport] CRITICAL ERROR during Cloudinary process for {$url}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $url;
        }
    }

    /**
     * Compress image to WebP and resize to a maximum of 1200px.
     */
    private function compressToWebp(string $filePath, int $quality = 80): string
    {
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
            Log::warning("[UCOStudentImport] Image compression to webp failed: " . $e->getMessage());
        }

        return $filePath;
    }

    /**
     * Aggressively clean a URL to ensure it is absolute and valid for Guzzle.
     */
    private function cleanUrl(?string $url): ?string
    {
        if (!$url) return null;

        // 1. Strip HTML tags
        $url = strip_tags($url);

        // 2. Remove all non-printable ASCII characters (including BOM, zero-width spaces, etc.)
        $url = preg_replace('/[^\x20-\x7E]/', '', $url);

        // 3. Trim whitespace and weird characters
        $url = trim($url, " \t\n\r\0\x0B\xEF\xBB\xBF");

        // 4. Nuclear fix: find the first instance of 'http' and throw away everything before it.
        // This solves the "relative URI" error caused by leading garbage.
        if (preg_match('/(https?:\/\/.*)$/i', $url, $matches)) {
            $url = $matches[1];
        }

        // 5. Google Drive link conversion
        // Convert "open?id=..." or "/file/d/.../view" into direct stream link "/uc?export=download&confirm=t&id=..."
        if (str_contains($url, 'drive.google.com') || str_contains($url, 'docs.google.com')) {
            if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{25,})/', $url, $matches)) {
                $url = "https://drive.google.com/uc?export=download&confirm=t&id=" . $matches[1];
            }
        }

        // 6. Final check
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
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

    private function skip(string $msg): void
    {
        $this->skippedCount++;
        $this->errors[] = $msg;
        Log::warning("[UCOStudentImport] Skipped: {$msg}");
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
        Cache::increment($status === 'success' ? "{$prefix}_success" : "{$prefix}_skipped");
    }

    // ─── Events ───────────────────────────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                if (!$this->importId) return;
                $totalRows = $event->getReader()->getTotalRows();
                $total = max(0, max($totalRows) - 3);
                $prefix = "import_{$this->importId}";
                Cache::put($prefix, ['status' => 'processing', 'errors' => []], now()->addMinutes(60));
                Cache::forever("{$prefix}_total", $total);
                Cache::forever("{$prefix}_current", 0);
                Cache::forever("{$prefix}_success", 0);
                Cache::forever("{$prefix}_skipped", 0);
                Log::info("[UCOStudentImport] Started: ~{$total} rows");
            },
            AfterImport::class => function () {
                if (!$this->importId) return;
                $prefix = "import_{$this->importId}";
                $progress = Cache::get($prefix, ['status' => 'processing', 'errors' => []]);
                $progress['status'] = 'completed';
                Cache::put($prefix, $progress, now()->addMinutes(60));
                Log::info("[UCOStudentImport] Done: {$this->successCount} ok, {$this->skippedCount} skipped");

                // Images are now uploaded inline during import — no post-import migration needed
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
