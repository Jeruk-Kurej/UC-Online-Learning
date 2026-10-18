<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;

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
class UCOStudentImport implements ShouldQueue, ToArray, WithChunkReading, WithEvents, WithStartRow
{
    use Concerns\ParsesImportDates, Concerns\TracksImportProgress, Concerns\UpsertsVentures;

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
            $loginEmail = $this->loginEmail($row);
            if (! $loginEmail) {
                $this->skip('Row has no email and no NIS');

                return;
            }

            $studentName = $this->cell($row, 3);
            if (! $studentName) {
                $this->skip("No student name for {$loginEmail}");

                return;
            }

            $user = $this->importUser($row, $loginEmail, $studentName);

            // --- Company (intrapreneur): column 22 ---
            if ($companyName = $this->cell($row, 22)) {
                $this->importCompany($user, $this->cleanName($companyName), $row);
            }

            // --- Business (entrepreneur): column 34 (detailed) or column 13 (legacy) ---
            if ($businessName = $this->cell($row, 34) ?? $this->cell($row, 13)) {
                $this->importBusiness($user, $this->cleanName($businessName), $row);
            }

            $this->successCount++;
            $this->updateProgress('success');
        } catch (\Exception $e) {
            $this->skip("Exception: {$this->safeMessage($e)}");
            Log::error("[UCOStudentImport] Error: {$this->safeMessage($e)}", ['exception' => $e::class]);
        }
    }

    // --- Left side: the student ---

    /**
     * Official email, then personal email, then one made from the NIS.
     */
    private function loginEmail(array $row): ?string
    {
        $officialEmail = $this->cell($row, 11);
        $personalEmail = $this->cell($row, 5);
        $nis = $this->cell($row, 0);

        if ($officialEmail && filter_var($officialEmail, FILTER_VALIDATE_EMAIL)) {
            return strtolower($officialEmail);
        }
        if ($personalEmail && filter_var($personalEmail, FILTER_VALIDATE_EMAIL)) {
            return strtolower($personalEmail);
        }

        return $nis ? strtolower($nis).'@student.ciputra.ac.id' : null;
    }

    private function studentStatus(array $row): string
    {
        $currentStatus = strtolower($this->cell($row, 12) ?? '');

        return match (true) {
            str_contains($currentStatus, 'alumni') || str_contains($currentStatus, 'lulus') => 'alumni',
            str_contains($currentStatus, 'drop') || str_contains($currentStatus, 'putus') => 'inactive',
            str_contains($currentStatus, 'non-aktif') || str_contains($currentStatus, 'cuti') => 'inactive',
            default => 'active',
        };
    }

    private function importUser(array $row, string $loginEmail, string $studentName): User
    {
        $nis = $this->cell($row, 0);
        $rawProfilePhotoUrl = $this->cell($row, 49);

        $userData = array_filter([
            'name' => $studentName,
            'nis' => $nis,
            'phone_number' => $this->cleanPhone($this->cell($row, 6)),
            'mobile_number' => $this->cleanPhone($this->cell($row, 7)),
            'whatsapp' => $this->cleanPhone($this->cell($row, 8)),
            'personal_email' => $this->cell($row, 5),
            'major' => $this->cell($row, 16),
            'student_status' => $this->studentStatus($row),
            'year_of_enrollment' => $this->cell($row, 10),
            'profile_photo_url' => $rawProfilePhotoUrl,
            'email_verified_at' => now(),
            'is_visible' => true,
        ], fn ($v) => $v !== null);

        $user = User::where('email', $loginEmail)->first();
        if ($user) {
            $user->update($userData);
        } else {
            $user = User::create(array_merge($userData, [
                'email' => $loginEmail,
                'password' => Hash::make('password123'),
            ]));
        }

        $this->queueImageUpload($user, 'profile_photo_url', $rawProfilePhotoUrl, 'users', $nis ?? $loginEmail);

        return $user;
    }

    // --- Right side: work profile (intrapreneur) ---

    private function importCompany(User $user, string $companyName, array $row): void
    {
        $companyName = strtoupper(trim($companyName));
        $rawLogoUrl = $this->cell($row, 43);

        $company = $this->upsertCompany($user, $companyName, array_filter([
            'category_id' => $this->categoryId($this->cell($row, 26) ?? $this->cell($row, 27)),
            'position' => $this->cell($row, 23),
            'job_description' => $this->cell($row, 25),
            'year_started_working' => $this->cell($row, 24),
            'logo_url' => $rawLogoUrl,
            'is_visible' => true,
        ], fn ($v) => $v !== null));

        $this->queueImageUpload($company, 'logo_url', $rawLogoUrl, 'companies', $companyName);
    }

    // --- Right side: business (entrepreneur) ---

    private function importBusiness(User $user, string $businessName, array $row): void
    {
        $rawLogoUrl = $this->cell($row, 43);
        $studentYear = $this->cell($row, 10);

        $business = $this->upsertBusiness($user, $businessName, array_filter([
            'category_id' => $this->categoryId($this->cell($row, 30) ?? $this->cell($row, 31)),
            'position' => $this->cell($row, 38),
            'established_date' => $this->parseDate($this->cell($row, 37)),
            'description' => $this->cell($row, 42),
            'address' => $this->cell($row, 40),
            'website' => $this->cell($row, 39),
            'employee_count' => $this->cell($row, 41),
            'revenue_range' => $this->cell($row, 32),
            'business_legality' => $this->cell($row, 35),
            'product_legality' => $this->cell($row, 36),
            'academic_heritage' => $studentYear ? 'Join UC Online '.$studentYear : null,
            'logo_url' => $rawLogoUrl,
            'type' => 'entrepreneur',
            'approval_status' => 'approved',
            'is_visible' => true,
        ], fn ($v) => $v !== null));

        $this->queueImageUpload($business, 'logo_url', $rawLogoUrl, 'businesses', $businessName);

        $business->members()->syncWithoutDetaching([
            $user->id => ['position' => $this->cell($row, 38)],
        ]);
    }

    // --- Helpers ---

    /**
     * Trimmed cell value; empty cells and "-" read as null.
     */
    private function cell(array $row, int $index): ?string
    {
        $val = $row[$index] ?? null;
        if ($val === null) {
            return null;
        }
        $str = trim((string) $val);

        return ($str === '' || $str === '-') ? null : $str;
    }

    /**
     * Category with the slug of this name, created when missing.
     */
    private function categoryId(?string $name): ?int
    {
        $slug = Str::slug($name ?? '');
        if (! $name || ! $slug) {
            return null;
        }

        return Category::firstOrCreate(['slug' => $slug], ['name' => trim($name)])->id;
    }

    private function cleanPhone(?string $val): ?string
    {
        if (! $val) {
            return null;
        }
        $val = preg_replace('/[^0-9+]/', '', $val);

        return strlen($val) >= 6 ? $val : null;
    }

    /**
     * Clean a name field: strip <br> tags, take first entry if multiple, truncate to 250 chars.
     */
    private function cleanName(?string $val): ?string
    {
        if (! $val) {
            return null;
        }
        $val = str_replace(["\r", "\n"], ' ', $val);
        $parts = preg_split('/\s*<br\s*\/?\s*>\s*/i', $val);
        $parts = array_filter(array_map('trim', $parts), fn ($p) => $p !== '');
        $first = reset($parts) ?: $val;
        $first = trim(strip_tags($first));

        return Str::limit($first, 250, '');
    }

    // --- Progress log lines ---

    protected function headerRows(): int
    {
        return $this->startRow() - 1;
    }

    protected function startedLogLine(int $total): string
    {
        return "[UCOStudentImport] Started: ~{$total} rows";
    }

    protected function completedLogLine(): string
    {
        return "[UCOStudentImport] Done: {$this->successCount} ok, {$this->skippedCount} skipped";
    }

    protected function skippedLogLine(string $reason): string
    {
        return "[UCOStudentImport] Skipped: {$reason}";
    }
}
