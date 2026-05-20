<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Models\Province;

use App\Imports\UCOStudentImport;
use App\Imports\FormResponseImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
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

        $search = trim((string) $request->get('search', ''));
        $sortName = $request->get('sort_name');
        $sortYear = $request->get('sort_year');
        $studentStatus = $request->get('student_status');
        $currentStatus = $request->get('current_status');
        $major = $request->get('major');
        $yearOfEnrollment = $request->get('year_of_enrollment');

        if (!in_array($sortName, ['asc', 'desc'], true)) {
            $sortName = null;
        }

        if (!in_array($sortYear, ['asc', 'desc'], true)) {
            $sortYear = null;
        }

        $allowedStudentStatuses = ['active', 'inactive', 'cuti', 'alumni'];
        if (!in_array($studentStatus, $allowedStudentStatuses, true)) {
            $studentStatus = null;
        }

        // Build query with search, filter, and sort options
        $query = User::withCount('businesses')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('nis', 'LIKE', "%{$search}%");
                });
            })
            ->when($studentStatus, fn ($q) => $q->where('student_status', $studentStatus))
            ->when($currentStatus, fn ($q) => $q->whereRaw('LOWER(current_status) = ?', [strtolower($currentStatus)]))
            ->when($major, fn ($q) => $q->where('major', $major))
            ->when($yearOfEnrollment, fn ($q) => $q->where('year_of_enrollment', $yearOfEnrollment));

        // Apply requested sorting
        if ($sortName) {
            $query->orderBy('name', $sortName);
        }

        if ($sortYear) {
            $query->orderByRaw(
                "CASE WHEN year_of_enrollment REGEXP '^[0-9]{4}$' THEN CAST(year_of_enrollment AS UNSIGNED) ELSE 0 END {$sortYear}"
            );
        }

        // Stable fallback ordering
        if (!$sortName && !$sortYear) {
            $query->latest();
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(20);

        // Filter dropdown sources
        $availableMajors = User::query()
            ->whereNotNull('major')
            ->where('major', '!=', '')
            ->distinct()
            ->orderBy('major')
            ->pluck('major');

        $availableEnrollmentYears = User::query()
            ->whereNotNull('year_of_enrollment')
            ->where('year_of_enrollment', '!=', '')
            ->distinct()
            ->orderByRaw(
                "CASE WHEN year_of_enrollment REGEXP '^[0-9]{4}$' THEN CAST(year_of_enrollment AS UNSIGNED) ELSE 0 END DESC"
            )
            ->pluck('year_of_enrollment');

        // Get accurate counts from database (not from paginated collection)
        $totalUsers = User::count();
        $totalEntrepreneurs = User::whereRaw('LOWER(current_status) = ?', ['entrepreneur'])->count();
        $totalIntrapreneurs = User::whereRaw('LOWER(current_status) = ?', ['intrapreneur'])->count();
        $totalAlumni = User::where('role', 'alumni')->count();
        $featuredUserCount = User::where('is_featured', true)->count();

        if ($request->ajax()) {
            return view('users.partials.list', compact('users'))->render();
        }

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalEntrepreneurs',
            'totalIntrapreneurs',
            'totalAlumni',
            'featuredUserCount',
            'availableMajors',
            'availableEnrollmentYears'
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:user,admin',
            'student_status' => 'required|string',
            
            // Identity & Contact
            'prefix_title' => 'nullable|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|url|max:255',
            
            // Academic & Career
            'nis' => 'nullable|string|max:255',
            'year_of_enrollment' => 'nullable|string|max:50',
            'graduate_year' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:255',
            'current_status' => 'nullable|string|max:255',
            'testimony' => 'nullable|string',
            
            // Files
            'profile_photo_url' => 'nullable|image|max:5120',
            'cv_url' => 'nullable|mimes:pdf|max:10240',
            'activities_doc_url' => 'nullable|mimes:pdf|max:10240',
            
            'is_visible' => 'nullable|boolean',
            'owned_businesses' => 'nullable|array',
            'owned_businesses.*' => 'exists:businesses,id',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_status' => $validated['student_status'],
            'prefix_title' => $validated['prefix_title'],
            'suffix_title' => $validated['suffix_title'],
            'personal_email' => $validated['personal_email'],
            'phone_number' => $validated['phone_number'],
            'mobile_number' => $validated['mobile_number'],
            'whatsapp' => $validated['whatsapp'],
            'linkedin' => $validated['linkedin'],
            'nis' => $validated['nis'],
            'year_of_enrollment' => $validated['year_of_enrollment'],
            'graduate_year' => $validated['graduate_year'],
            'major' => $validated['major'],
            'current_status' => $validated['current_status'],
            'testimony' => $validated['testimony'],
            'is_visible' => $request->has('is_visible'),
            'email_verified_at' => now(),
        ];

        // Handle File Uploads
        if ($request->hasFile('profile_photo_url')) {
            $file = $request->file('profile_photo_url');
            $path = $file->storeAs('profile-photos', Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['profile_photo_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('cv_url')) {
            $file = $request->file('cv_url');
            $path = $file->storeAs('student-cvs', 'cv_' . Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['cv_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('activities_doc_url')) {
            $file = $request->file('activities_doc_url');
            $path = $file->storeAs('student-activities', 'act_' . Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['activities_doc_url'] = '/storage/' . $path;
        }

        // Create the user
        $newUser = User::create($userData);

        // Assign businesses if selected
        if (!empty($request->owned_businesses)) {
            Business::whereIn('id', $request->owned_businesses)
                ->update(['user_id' => $newUser->id]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Success! The profile for '{$newUser->name}' has been created.");
    }

    /**
     * Display the specified user profile catalog.
     */
    public function show(User $user)
    {
        // Publicly accessible catalog - removed admin guard

        // Load owned businesses with relationships
        $user->load(['businesses' => function ($query) {
            $query->where('is_visible', true)->with('category');
        }]);

        // Load businesses they are a member of
        $user->load(['memberOfBusinesses' => function ($query) {
            $query->where('is_visible', true)->with('category');
        }]);

        // We use view users.profile instead of users.show
        return view('users.profile', [
            'user' => $user,
            'ownedBusinesses' => $user->businesses,
            'memberBusinesses' => $user->memberOfBusinesses
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

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can update users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:user,admin',
            'student_status' => 'required|string',
            
            // Identity & Contact
            'prefix_title' => 'nullable|string|max:255',
            'suffix_title' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'mobile_number' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|url|max:255',
            
            // Academic & Career
            'nis' => 'nullable|string|max:255',
            'year_of_enrollment' => 'nullable|string|max:50',
            'graduate_year' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:255',
            'current_status' => 'nullable|string|max:255',
            'testimony' => 'nullable|string',
            
            // Files
            'profile_photo_url' => 'nullable|image|max:5120',
            'cv_url' => 'nullable|mimes:pdf|max:10240',
            'activities_doc_url' => 'nullable|mimes:pdf|max:10240',
            
            'is_visible' => 'nullable|boolean',
            'owned_businesses' => 'nullable|array',
            'owned_businesses.*' => 'exists:businesses,id',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_status' => $validated['student_status'],
            'prefix_title' => $validated['prefix_title'],
            'suffix_title' => $validated['suffix_title'],
            'personal_email' => $validated['personal_email'],
            'phone_number' => $validated['phone_number'],
            'mobile_number' => $validated['mobile_number'],
            'whatsapp' => $validated['whatsapp'],
            'linkedin' => $validated['linkedin'],
            'nis' => $validated['nis'],
            'year_of_enrollment' => $validated['year_of_enrollment'],
            'graduate_year' => $validated['graduate_year'],
            'major' => $validated['major'],
            'current_status' => $validated['current_status'],
            'testimony' => $validated['testimony'],
            'is_visible' => $request->has('is_visible'),
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        // Handle File Uploads
        if ($request->hasFile('profile_photo_url')) {
            $file = $request->file('profile_photo_url');
            $path = $file->storeAs('profile-photos', Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['profile_photo_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('cv_url')) {
            $file = $request->file('cv_url');
            $path = $file->storeAs('student-cvs', 'cv_' . Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['cv_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('activities_doc_url')) {
            $file = $request->file('activities_doc_url');
            $path = $file->storeAs('student-activities', 'act_' . Str::slug($userData['name']) . '_' . time() . '.' . $file->getClientOriginalExtension(), 'public');
            $userData['activities_doc_url'] = '/storage/' . $path;
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
     * Toggle the visibility status of a user.
     */
    public function toggleStatus(User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can toggle user status.');
        }

        $newVisibility = !$user->is_visible;
        $updateData = ['is_visible' => $newVisibility];
        
        // If deactivating, also turn off featured status
        if (!$newVisibility) {
            $updateData['is_featured'] = false;
        }

        $user->update($updateData);

        $status = $user->is_visible ? 'activated' : 'deactivated';
        return back()->with('success', "User '{$user->name}' has been {$status} successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Deactivated as per user request to use toggleStatus instead of delete
        abort(405, 'Delete action is disabled. Use Toggle Status instead.');
    }

    /**
     * Toggle featured status for a user.
     */
    public function toggleFeatured(User $user)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403, 'Only administrators can toggle featured status.');
        }

        // Check if we're adding and already hit the limit
        if (!$user->is_featured && User::where('is_featured', true)->count() >= 4) {
            return back()->withErrors(['featured' => 'Maximum of 4 users can be featured.']);
        }

        $user->update(['is_featured' => !$user->is_featured]);

        $status = $user->is_featured ? 'added to' : 'removed from';
        return back()->with('success', "User '{$user->name}' has been {$status} featured users.");
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
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // Max 10MB
        ]);

        // Increase execution time and memory limit for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        try {
            $importId = (string) Str::uuid();
            $file = $request->file('file');

            // Store file so queue worker can access it
            $path = $file->store('imports', 'local');

            // Peek at the file to auto-detect format
            $importer = $this->detectImporter($file->getRealPath(), $importId, $file->getClientOriginalName());

            // Queue it — runs in background via artisan queue:work
            Excel::queueImport($importer, $path, 'local');

            $format = $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response';
            
            session(['active_user_import_id' => $importId]);

            return back()
                ->with('import_success', "Import started ({$format})! Format auto-detected. Please wait.");
        } catch (\Exception $e) {
            Log::error('User import exception: ' . $e->getMessage());
            return redirect()
                ->route('users.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Peek at the raw file to determine which importer to use.
     * Replicated from BusinessController for consistency.
     */
    private function detectImporter(string $path, string $importId, string $originalName = '')
    {
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                return new UCOStudentImport($importId);
            }
            return new FormResponseImport($importId);
        }

        // For CSV/Raw: read first 2KB and search for markers
        $handle = fopen($path, 'r');
        if (!$handle) return new FormResponseImport($importId);
        $peek = fread($handle, 2048);
        fclose($handle);

        // UCO Student format markers: "NIS" AND "Sub Prodi"
        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            return new UCOStudentImport($importId);
        }
        
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
