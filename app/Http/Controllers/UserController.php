<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Models\Province;
use App\Imports\FormResponseImport;
use App\Imports\UCOStudentImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class UserController extends Controller
{
    /**
     * Get authenticated user as User instance
     */
    private function getAuthUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }
        
        return $user;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // ✅ CHANGED: Use Gate instead of authorize for better error handling
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can view user list.');
        }

        $search = $request->get('search');
        
        // Build query with search filter
        $query = User::withCount('businesses');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(20);

        // Get accurate counts from database (not from paginated collection)
        $totalUsers = User::count();
        $totalEntrepreneurs = User::whereRaw('LOWER(current_status) = ?', ['entrepreneur'])->count();
        $totalIntrapreneurs = User::whereRaw('LOWER(current_status) = ?', ['intrapreneur'])->count();
        $totalAlumni = User::where('role', 'alumni')->count();
        $featuredUserCount = User::where('is_featured', true)->count();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalEntrepreneurs',
            'totalIntrapreneurs',
            'totalAlumni',
            'featuredUserCount'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can create users.');
        }

        // Get available businesses for ownership transfer
        $availableBusinesses = Business::with('user', 'category')->get();
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('users.create', compact('availableBusinesses', 'provinces'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can create users.');
        }

        $validated = $request->validate([
            // Auth / Required
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:user,admin',
            'student_status' => 'required|in:active,inactive,cuti,alumni',
            'is_visible' => 'nullable|boolean',

            // CSV: Identity
            'prefix_title' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|string|email|max:255',

            // CSV: Contact
            'phone_number' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',

            // CSV: Academic
            'current_status' => 'nullable|string|max:255',
            'nis' => 'nullable|string|max:255',
            'year_of_enrollment' => 'nullable|string|max:255',
            'graduate_year' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',

            // CSV: Extra
            'testimony' => 'nullable|string',
            'cv_url' => 'nullable|file|mimes:pdf|max:10240',
            'profile_photo_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'activities_doc_url' => 'nullable|file|mimes:pdf|max:10240',

            // Business Assignments
            'owned_businesses' => 'nullable|array',
            'owned_businesses.*' => 'exists:businesses,id',
        ]);

        $cvPath = $request->hasFile('cv_url') ? $request->file('cv_url')->store('users/cvs', 'public') : null;
        $profilePhotoPath = $request->hasFile('profile_photo_url') ? $request->file('profile_photo_url')->store('users/photos', 'public') : null;
        $activitiesDocPath = $request->hasFile('activities_doc_url') ? $request->file('activities_doc_url')->store('users/activities', 'public') : null;

        // Prepare user data
        $userData = [
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_status' => $validated['student_status'],
            'is_visible' => true,
            'email_verified_at' => now(),

            'prefix_title' => $validated['prefix_title'] ?? null,
            'name' => $validated['name'],
            'suffix_title' => $validated['suffix_title'] ?? null,
            'personal_email' => $validated['personal_email'] ?? null,

            'phone_number' => $validated['phone_number'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,

            'current_status' => $validated['current_status'] ?? null,
            'nis' => $validated['nis'] ?? null,
            'year_of_enrollment' => $validated['year_of_enrollment'] ?? null,
            'graduate_year' => $validated['graduate_year'] ?? null,
            'major' => $validated['major'] ?? null,

            'testimony' => $validated['testimony'] ?? null,
            'cv_url' => $cvPath,
            'profile_photo_url' => $profilePhotoPath,
            'activities_doc_url' => $activitiesDocPath,
        ];

        // Create the user
        $newUser = User::create($userData);

        // Transfer business ownership if selected
        if ($request->has('owned_businesses') && !empty($request->owned_businesses)) {
            Business::whereIn('id', $request->owned_businesses)
                ->update(['user_id' => $newUser->id]);
            
            $businessCount = count($request->owned_businesses);
            session()->flash('success', "Success! The user '{$newUser->name}' has been created, and {$businessCount} business(es) have been transferred.");
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Success! The user '{$newUser->name}' has been created successfully.");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['businesses.products', 'memberOfBusinesses.products', 'skills']);

        return view('users.show', [
            'user' => $user,
            'userToShow' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can edit users.');
        }

        // Get available businesses for ownership transfer (excluding businesses owned by this user)
        $availableBusinesses = Business::with('user', 'category')
            ->where('user_id', '!=', $user->id)
            ->get();

        // Get businesses currently owned by this user
        $ownedBusinesses = $user->businesses()->pluck('id')->toArray();
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('users.edit', [
            'userToEdit' => $user,
            'availableBusinesses' => $availableBusinesses,
            'ownedBusinesses' => $ownedBusinesses,
            'provinces' => $provinces,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can update users.');
        }

        $validated = $request->validate([
            // Auth / Required
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:user,admin',
            'student_status' => 'required|in:active,inactive,cuti,alumni',
            'is_visible' => 'nullable|boolean',

            // CSV: Identity
            'prefix_title' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|string|email|max:255',

            // CSV: Contact
            'phone_number' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',

            // CSV: Academic
            'current_status' => 'nullable|string|max:255',
            'nis' => 'nullable|string|max:255',
            'year_of_enrollment' => 'nullable|string|max:255',
            'graduate_year' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',

            // CSV: Extra
            'testimony' => 'nullable|string',
            'cv_url' => 'nullable|file|mimes:pdf|max:10240',
            'profile_photo_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'activities_doc_url' => 'nullable|file|mimes:pdf|max:10240',

            // Business Assignments
            'owned_businesses' => 'nullable|array',
            'owned_businesses.*' => 'exists:businesses,id',
        ]);

        $cvPath = $request->hasFile('cv_url') ? $request->file('cv_url')->store('users/cvs', 'public') : $user->cv_url;
        $profilePhotoPath = $request->hasFile('profile_photo_url') ? $request->file('profile_photo_url')->store('users/photos', 'public') : $user->profile_photo_url;
        $activitiesDocPath = $request->hasFile('activities_doc_url') ? $request->file('activities_doc_url')->store('users/activities', 'public') : $user->activities_doc_url;

        // Prepare user data
        $userData = [
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_status' => $validated['student_status'],
            'is_visible' => true,

            'prefix_title' => $validated['prefix_title'] ?? null,
            'name' => $validated['name'],
            'suffix_title' => $validated['suffix_title'] ?? null,
            'personal_email' => $validated['personal_email'] ?? null,

            'phone_number' => $validated['phone_number'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,

            'current_status' => $validated['current_status'] ?? null,
            'nis' => $validated['nis'] ?? null,
            'year_of_enrollment' => $validated['year_of_enrollment'] ?? null,
            'graduate_year' => $validated['graduate_year'] ?? null,
            'major' => $validated['major'] ?? null,

            'testimony' => $validated['testimony'] ?? null,
            'cv_url' => $cvPath,
            'profile_photo_url' => $profilePhotoPath,
            'activities_doc_url' => $activitiesDocPath,
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Update the user
        $user->update($userData);

        // Update business ownership if selected
        if ($request->has('owned_businesses')) {
            // Remove this user from businesses they no longer own
            Business::where('user_id', $user->id)
                ->whereNotIn('id', $request->owned_businesses ?? [])
                ->update(['user_id' => null]);
            
            // Transfer selected businesses to this user
            if (!empty($request->owned_businesses)) {
                Business::whereIn('id', $request->owned_businesses)
                    ->update(['user_id' => $user->id]);
            }
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Success! The profile for '{$user->name}' has been updated.");
    }

    /**
     * Toggle the featured status of a user (admin only, max 4 featured at once).
     */
    public function toggleFeatured(User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can feature users.');
        }

        if ($user->is_featured) {
            $user->update(['is_featured' => false]);
            return back()->with('success', "\"{$user->name}\" removed from featured.");
        }

        $featuredCount = User::where('is_featured', true)->count();
        if ($featuredCount >= 4) {
            return back()->withErrors(['featured' => 'Maximum of 4 featured users reached. Un-feature one first.']);
        }

        $user->update(['is_featured' => true]);
        return back()->with('success', "\"{$user->name}\" is now featured.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $currentUser = $this->getAuthUser();

        if (!$currentUser->isAdmin()) {
            abort(403, 'Only administrators can delete users.');
        }

        // Prevent deleting yourself
        if ($user->id === $currentUser->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "The user '{$user->name}' has been deleted successfully.");
    }

    /**
     * Import users from Excel file.
     */
    public function import(Request $request)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can import users.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        // Increase execution time and memory limit for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        try {
            $importId = (string) Str::uuid();
            $file = $request->file('file');
            
            // Store file to local temp disk so queue worker can access it
            $path = $file->store('imports', 'local');
            
            // Detect format and get the appropriate importer
            $importer = $this->detectImporter($file->getRealPath(), $importId, $file->getClientOriginalName());
            
            // Queue it — runs in background via `php artisan queue:work`
            Excel::queueImport($importer, $path, 'local');
            
            $format = $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response';
            
            // Store importId in session so frontend can poll progress
            session(['active_user_import_id' => $importId]);

            return back()
                ->with('import_success', "Import queued! Format: {$format}. Processing in background...")
                ->with('importId', $importId);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMsg = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                $errorMessages[] = $errorMsg;
                Log::error("User import validation error: " . $errorMsg);
            }
            
            return redirect()
                ->route('users.index')
                ->with('error', 'Import validation failed')
                ->with('import_errors', array_slice($errorMessages, 0, 5));
        } catch (\Exception $e) {
            Log::error('User import exception: ' . $e->getMessage());
            return redirect()
                ->route('users.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Detect the importer type based on file content.
     */
    private function detectImporter(string $path, string $importId, string $originalName = '')
    {
        // For xlsx/xls we can't search raw text easily — check filename heuristic
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info("User Import: XLSX filename heuristic → UCOStudentImport");
                return new UCOStudentImport($importId);
            }
            Log::info("User Import: XLSX default → FormResponseImport");
            return new FormResponseImport($importId);
        }

        // For CSV/Raw: read first 2KB and search for markers
        $handle = fopen($path, 'r');
        $peek = fread($handle, 2048);
        fclose($handle);

        // UCO Student format markers: "NIS" AND "Sub Prodi"
        // Form Response markers: "Timestamp" OR "Email Address" (row 1)
        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            Log::info("User Import: detected UCO Student Profile format via content markers");
            return new UCOStudentImport($importId);
        }

        Log::info("User Import: detected Form Response format (default for CSV)");
        return new FormResponseImport($importId);
    }

    /**
     * Download Excel template for user import.
     */
    public function downloadTemplate()
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can download import template.');
        }

        $headers = [
            // Core Fields
            'name',
            'email',
            'username',
            'password',
            'role',
            'is_active',
            
            // Student Info
            'nis',
            'nisn',
            'prodi',
            'sub_prodi',
            'student_year',
            'major',
            'is_graduate',
            'cgpa',
            'edu_level',
            
            // Personal Data
            'gender',
            'birth_date',
            'birth_city',
            'religion',
            'citizenship',
            'citizenship_no',
            
            // Contact Info - Primary
            'address',
            'address_city',
            'province',
            'country',
            'zip_code',
            'phone_number',
            'mobile_number',
            
            // Contact Info - Secondary
            'address2',
            'address_city2',
            'province2',
            'country2',
            'zip_code2',
            'phone_number2',
            'mobile_number2',
            
            // Social Media
            'whatsapp',
            'bbm',
            'line',
            'facebook',
            'twitter',
            'instagram',
            
            // Academic History
            'academic_advisor',
            'previous_school_name',
            'school_city',
            'previous_edu_level',
            'start_year',
            'end_year',
            'score',
            
            // Certificates
            'certificate_no_1',
            'certificate_date_1',
            'certificate_no_2',
            'certificate_date_2',
            
            // Father Data - Basic
            'father_name',
            'father_birth_city',
            'father_birthday',
            'father_citizenship',
            'father_citizenship_no',
            'father_passport_no',
            'father_npwp_no',
            'father_religion',
            'father_bpjs_no',
            
            // Father Data - Contact
            'father_address',
            'father_address_city',
            'father_phone',
            'father_mobile',
            'father_email',
            'father_bbm',
            
            // Father Data - Education & Work
            'father_education',
            'father_education_major',
            'father_profession',
            'father_business_name',
            'father_business_address',
            'father_business_phone',
            'father_business_line',
            'father_business_title',
            'father_business_revenue',
            'father_special_need',
            
            // Mother Data - Basic
            'mother_name',
            'mother_birth_city',
            'mother_birthday',
            'mother_citizenship',
            'mother_citizenship_no',
            'mother_passport_no',
            'mother_npwp_no',
            'mother_religion',
            'mother_bpjs_no',
            
            // Mother Data - Contact
            'mother_address',
            'mother_address_city',
            'mother_phone',
            'mother_mobile',
            'mother_email',
            'mother_bbm',
            
            // Mother Data - Education & Work
            'mother_education',
            'mother_education_major',
            'mother_profession',
            'mother_business_name',
            'mother_business_address',
            'mother_business_phone',
            'mother_business_line',
            'mother_business_title',
            'mother_business_revenue',
            'mother_special_need',
            
            // Graduation Data
            'final_project_indonesia',
            'final_project_english',
            'cum_credits',
            'predicate',
            'judicium_date',
            'document_no',
            'document_date',
            'graduate_period',
            'class_semester',
            'form_no',
            'official_email',
            'current_status',
            'start_date',
            'end_date',
            'business_name',
            'business_line',
            'business_title',
        ];

        $sampleData = [
            // Core Fields
            'John Doe',                    // name
            'john.doe@example.com',        // email
            'johndoe',                     // username
            'password123',                 // password
            'student',                     // role
            '1',                          // is_active
            
            // Student Info
            '12345678',                    // nis
            '1234567890',                  // nisn
            'Computer Science',            // prodi
            'Software Engineering',        // sub_prodi
            '2023',                        // student_year
            'Computer Science',            // major
            '0',                          // is_graduate
            '3.85',                        // cgpa
            'Bachelor',                    // edu_level
            
            // Personal Data
            'Male',                        // gender
            '2000-01-01',                  // birth_date
            'Jakarta',                     // birth_city
            'Islam',                       // religion
            'Indonesian',                  // citizenship
            '3201010101000001',           // citizenship_no
            
            // Contact Info - Primary
            'Jl. Example No. 123',        // address
            'Jakarta',                     // address_city
            'DKI Jakarta',                // province
            'Indonesia',                   // country
            '12345',                       // zip_code
            '021-1234567',                // phone_number
            '0812-3456-7890',             // mobile_number
            
            // Contact Info - Secondary
            '',                           // address2
            '',                           // address_city2
            '',                           // province2
            '',                           // country2
            '',                           // zip_code2
            '',                           // phone_number2
            '',                           // mobile_number2
            
            // Social Media
            '0812-3456-7890',             // whatsapp
            '',                           // bbm
            '',                           // line
            '',                           // facebook
            '',                           // twitter
            '',                           // instagram
            
            // Academic History
            'Dr. Jane Smith',             // academic_advisor
            'SMA Example',                // previous_school_name
            'Jakarta',                     // school_city
            'High School',                // previous_edu_level
            '2018',                        // start_year
            '2021',                        // end_year
            '85.5',                        // score
            
            // Certificates
            '',                           // certificate_no_1
            '',                           // certificate_date_1
            '',                           // certificate_no_2
            '',                           // certificate_date_2
            
            // Father Data - Basic
            'John Doe Sr.',               // father_name
            'Jakarta',                     // father_birth_city
            '1970-01-01',                 // father_birthday
            'Indonesian',                  // father_citizenship
            '3201010170000001',           // father_citizenship_no
            '',                           // father_passport_no
            '',                           // father_npwp_no
            'Islam',                       // father_religion
            '',                           // father_bpjs_no
            
            // Father Data - Contact
            'Jl. Example No. 123',        // father_address
            'Jakarta',                     // father_address_city
            '021-1111111',                // father_phone
            '0811-1111-1111',             // father_mobile
            'father@example.com',         // father_email
            '',                           // father_bbm
            
            // Father Data - Education & Work
            'Bachelor',                    // father_education
            'Business',                    // father_education_major
            'Entrepreneur',                // father_profession
            'ABC Company',                 // father_business_name
            'Jl. Business St.',           // father_business_address
            '021-9999999',                // father_business_phone
            'Trading',                     // father_business_line
            'CEO',                         // father_business_title
            '> 1B',                        // father_business_revenue
            '',                           // father_special_need
            
            // Mother Data - Basic
            'Jane Doe',                    // mother_name
            'Jakarta',                     // mother_birth_city
            '1972-01-01',                 // mother_birthday
            'Indonesian',                  // mother_citizenship
            '3201010172000002',           // mother_citizenship_no
            '',                           // mother_passport_no
            '',                           // mother_npwp_no
            'Islam',                       // mother_religion
            '',                           // mother_bpjs_no
            
            // Mother Data - Contact
            'Jl. Example No. 123',        // mother_address
            'Jakarta',                     // mother_address_city
            '021-2222222',                // mother_phone
            '0822-2222-2222',             // mother_mobile
            'mother@example.com',         // mother_email
            '',                           // mother_bbm
            
            // Mother Data - Education & Work
            'Bachelor',                    // mother_education
            'Education',                   // mother_education_major
            'Teacher',                     // mother_profession
            'XYZ School',                  // mother_business_name
            'Jl. School St.',             // mother_business_address
            '021-8888888',                // mother_business_phone
            'Education',                   // mother_business_line
            'Principal',                   // mother_business_title
            '500M - 1B',                   // mother_business_revenue
            '',                           // mother_special_need
            
            // Graduation Data
            '',                           // final_project_indonesia
            '',                           // final_project_english
            '',                           // cum_credits
            '',                           // predicate
            '',                           // judicium_date
            '',                           // document_no
            '',                           // document_date
            '',                           // graduate_period
            'Semester 1',                  // class_semester
            '',                           // form_no
            'john.doe@student.university.edu', // official_email
            'Active Student',              // current_status
            '2023-09-01',                 // start_date
            '2027-06-30',                 // end_date
            '',                           // business_name
            '',                           // business_line
            '',                           // business_title
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Add sample data
        $col = 'A';
        foreach ($sampleData as $value) {
            $sheet->setCellValue($col . '2', $value);
            $col++;
        }

        // Auto-size columns (using column index instead of range)
        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'users_import_template_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
