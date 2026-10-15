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
    use Concerns\TracksImportProgress, Concerns\ParsesImportDates;

    public $importId;
    protected $successCount = 0;
    protected $skippedCount = 0;
    protected $errors = [];

    public function __construct($importId = null)
    {
        $this->importId = $importId;
    }

    // --- Rows 1-3 are junk and headers; data starts at row 4 ---
    public function startRow(): int
    {
        return 4;
    }

    public function chunkSize(): int
    {
        return 1;
    }

    // --- Main processor ---

    public function array(array $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    private function processRow(array $row): void
    {
        try {
            $cell = function (int $i) use ($row): ?string {
                $val = $row[$i] ?? null;
                if ($val === null) return null;
                $str = trim((string) $val);
                return ($str === '' || $str === '-') ? null : $str;
            };

            // --- Left side: student data ---
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
            $legacyBizName = $cell(13);
            $major         = $cell(16);

            // --- Email: official, then personal, then synthesized from the NIS ---
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

            // --- Student status ---
            $studentStatus = match(true) {
                str_contains($currentStatus, 'alumni') || str_contains($currentStatus, 'lulus') => 'alumni',
                str_contains($currentStatus, 'drop') || str_contains($currentStatus, 'putus')   => 'inactive',
                str_contains($currentStatus, 'non-aktif') || str_contains($currentStatus, 'cuti') => 'inactive',
                default => 'active',
            };

            // --- Upsert user ---
            $rawProfilePhotoUrl = $cell(49);

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
                'profile_photo_url'  => $rawProfilePhotoUrl,
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

            if ($rawProfilePhotoUrl && !str_contains($rawProfilePhotoUrl, 'cloudinary.com')) {
                \App\Jobs\UploadImageToCloudinaryJob::dispatch($user, 'profile_photo_url', $rawProfilePhotoUrl, 'users', $nis ?? $loginEmail);
            }

            // --- Right side: business or company data ---

            // --- Company (intrapreneur): column 22 ---
            $companyName = $cell(22);
            if ($companyName) {
                $companyName = $this->cleanName($companyName);
                $this->upsertCompany($user, $companyName, $cell, $row);
            }

            // --- Business (entrepreneur): column 34 (detailed) or column 13 (legacy) ---
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

    // --- Company upsert ---

    private function upsertCompany(User $user, string $companyName, callable $cell, array $row): void
    {
        $companyName = strtoupper(trim($companyName));

        $categoryId = null;
        $catName = $cell(26) ?? $cell(27);
        if ($catName) {
            $slug = Str::slug($catName);
            if ($slug) {
                $cat = Category::firstOrCreate(['slug' => $slug], ['name' => trim($catName)]);
                $categoryId = $cat->id;
            }
        }

        $rawLogoUrl = $cell(43);

        $data = array_filter([
            'category_id'          => $categoryId,
            'position'             => $cell(23),
            'job_description'      => $cell(25),
            'year_started_working' => $cell(24),
            'logo_url'             => $rawLogoUrl,
            'is_visible'           => true,
        ], fn($v) => $v !== null);

        $company = Company::where('user_id', $user->id)->where('name', $companyName)->first()
            ?? Company::where('user_id', $user->id)->first();

        if ($company) {
            $company->update(array_merge($data, ['name' => $companyName]));
        } else {
            $company = Company::create(array_merge($data, ['user_id' => $user->id, 'name' => $companyName]));
        }

        if ($rawLogoUrl && !str_contains($rawLogoUrl, 'cloudinary.com')) {
            \App\Jobs\UploadImageToCloudinaryJob::dispatch($company, 'logo_url', $rawLogoUrl, 'companies', $companyName);
        }
    }

    // --- Business upsert ---

    private function upsertBusiness(User $user, string $bizName, callable $cell): void
    {
        $categoryId = null;
        $catName = $cell(30) ?? $cell(31);
        if ($catName) {
            $slug = Str::slug($catName);
            if ($slug) {
                $cat = Category::firstOrCreate(['slug' => $slug], ['name' => trim($catName)]);
                $categoryId = $cat->id;
            }
        }

        $rawLogoUrl = $cell(43);

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
            'academic_heritage' => $cell(10) ? 'Join UC Online ' . $cell(10) : null,
            'logo_url'          => $rawLogoUrl,
            'type'              => 'entrepreneur',
            'approval_status'   => 'approved',
            'is_visible'        => true,
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

        if ($rawLogoUrl && !str_contains($rawLogoUrl, 'cloudinary.com')) {
            \App\Jobs\UploadImageToCloudinaryJob::dispatch($business, 'logo_url', $rawLogoUrl, 'businesses', $bizName);
        }

        $business->members()->syncWithoutDetaching([
            $user->id => ['position' => $cell(38)],
        ]);
    }

    // --- Helpers ---

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
        $val = str_replace(["\r", "\n"], ' ', $val);
        $parts = preg_split('/\s*<br\s*\/?\s*>\s*/i', $val);
        $parts = array_filter(array_map('trim', $parts), fn($p) => $p !== '');
        $first = reset($parts) ?: $val;
        $first = trim(strip_tags($first));
        return Str::limit($first, 250, '');
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

    // --- Events ---

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
            },
        ];
    }
}
