<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserImportTemplate;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Imports\UCOStudentImport;
use App\Models\Business;
use App\Models\Province;
use App\Models\User;
use App\Services\ImageOptimizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Admin management of student and alumni accounts: list, create, edit, featuring, activation and import.
 */
class UserController extends \App\Http\Controllers\Controller
{
    /**
     * Featured user counts (total + by career focus).
     *
     * @return array{total: int, intrapreneurs: int, entrepreneurs: int}
     */
    private function getFeaturedUserStats(): array
    {
        $row = User::query()
            ->where('role', '!=', 'admin')
            ->where('is_featured', true)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN LOWER(current_status) = 'intrapreneur' THEN 1 ELSE 0 END) as intrapreneurs")
            ->selectRaw("SUM(CASE WHEN LOWER(current_status) = 'entrepreneur' THEN 1 ELSE 0 END) as entrepreneurs")
            ->first();

        return [
            'total' => (int) ($row?->total ?? 0),
            'intrapreneurs' => (int) ($row?->intrapreneurs ?? 0),
            'entrepreneurs' => (int) ($row?->entrepreneurs ?? 0),
        ];
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $sortName = $request->get('sort_name');
        $sortYear = $request->get('sort_year');
        $studentStatus = $request->get('student_status');
        $currentStatus = $request->get('current_status');
        $major = $request->get('major');
        $yearOfEnrollment = $request->get('year_of_enrollment');

        if (! in_array($sortName, ['asc', 'desc'], true)) {
            $sortName = null;
        }

        if (! in_array($sortYear, ['asc', 'desc'], true)) {
            $sortYear = null;
        }

        $allowedStudentStatuses = ['active', 'inactive', 'cuti', 'alumni'];
        if (! in_array($studentStatus, $allowedStudentStatuses, true)) {
            $studentStatus = null;
        }

        // Build query with search, filter, and sort options
        $query = User::withCount('businesses')
            ->where('role', '!=', 'admin')
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
        if (! $sortName && ! $sortYear) {
            $query->latest();
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(10);

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
        ['total' => $totalUsers, 'entrepreneurs' => $totalEntrepreneurs, 'intrapreneurs' => $totalIntrapreneurs, 'alumni' => $totalAlumni] = $this->userCounts();
        $featuredStats = $this->getFeaturedUserStats();
        $featuredUserCount = $featuredStats['total'];
        $featuredIntrapreneurCount = $featuredStats['intrapreneurs'];
        $featuredEntrepreneurCount = $featuredStats['entrepreneurs'];

        $hasActiveFilters = $request->filled('search')
            || $request->filled('sort_name')
            || $request->filled('sort_year')
            || $request->filled('student_status')
            || $request->filled('major')
            || $request->filled('year_of_enrollment');

        if ($request->ajax()) {
            return response()
                ->view('users.partials.list', compact('users', 'hasActiveFilters'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalEntrepreneurs',
            'totalIntrapreneurs',
            'totalAlumni',
            'featuredUserCount',
            'featuredIntrapreneurCount',
            'featuredEntrepreneurCount',
            'availableMajors',
            'availableEnrollmentYears',
            'hasActiveFilters'
        ));
    }

    /**
     * Return live stats counts as JSON (used for live counter refresh during import).
     */
    public function stats()
    {
        $featuredStats = $this->getFeaturedUserStats();

        return new JsonResponse($this->userCounts() + [
            'featured' => $featuredStats['total'],
            'featured_intrapreneurs' => $featuredStats['intrapreneurs'],
            'featured_entrepreneurs' => $featuredStats['entrepreneurs'],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Get available businesses for ownership transfer
        $availableBusinesses = Business::with('user', 'category')->get();
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('users.form', [
            'userToEdit' => null,
            'availableBusinesses' => $availableBusinesses,
            'ownedBusinessIds' => [],
            'provinces' => $provinces,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $userData = $request->profileData() + [
            'password' => Hash::make($request->validated('password')),
            'show_contact_details' => $request->boolean('show_contact_details'),
            'is_visible' => $request->boolean('is_visible', true),
            'email_verified_at' => now(),
        ];

        $photo = $request->file('profile_photo_url');
        if ($photo instanceof UploadedFile) {
            $userData['profile_photo_url'] = $this->storeProfilePhoto($photo, $userData['name']);
        }

        $document = $request->file('activities_doc_url');
        if ($document instanceof UploadedFile) {
            $path = $document->storeAs('student-activities', 'act_'.Str::slug($userData['name']).'_'.time().'.'.$document->getClientOriginalExtension(), 'public');
            $userData['activities_doc_url'] = '/storage/'.$path;
        }

        $newUser = User::create($userData);
        $this->assignBusinesses($request, $newUser);

        return redirect()
            ->route('users.index')
            ->with('success', "Success! The profile for '{$newUser->name}' has been created.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Get available businesses for ownership transfer (excluding businesses owned by this user)
        $availableBusinesses = Business::with('user', 'category')
            ->where('user_id', '!=', $user->id)
            ->get();

        // Get businesses currently owned by this user
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return view('users.form', [
            'userToEdit' => $user,
            'availableBusinesses' => $availableBusinesses,
            'ownedBusinessIds' => $user->businesses()->pluck('id')->toArray(),
            'provinces' => $provinces,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $userData = $request->profileData();

        foreach (['is_visible', 'show_contact_details'] as $flag) {
            if ($request->has($flag)) {
                $userData[$flag] = $request->boolean($flag);
            }
        }

        if (! empty($request->validated('password'))) {
            $userData['password'] = Hash::make($request->validated('password'));
        }

        if ($request->boolean('delete_profile_photo_url')) {
            $user->deleteFileFromStorage($user->profile_photo_url);
            $userData['profile_photo_url'] = null;
        }

        if ($request->hasFile('profile_photo_url')) {
            $user->deleteFileFromStorage($user->profile_photo_url);

            $photo = $request->file('profile_photo_url');
            if ($photo instanceof UploadedFile) {
                $userData['profile_photo_url'] = $this->storeProfilePhoto($photo, $userData['name']);
            }
        }

        $userData['activities_doc_url'] = $this->syncActivityDocuments($request, $user, $userData['name']);

        $user->fill($userData);
        $user->save();

        $this->assignBusinesses($request, $user);

        return redirect()
            ->route('users.show', $user)
            ->with('success', "Success! The profile for '{$user->name}' has been updated.");
    }

    /**
     * Toggle the featured status of a user.
     */
    public function toggleFeatured(Request $request, User $user)
    {
        // Cannot feature an inactive user
        if (! $user->is_visible && ! $user->is_featured) {
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse(['success' => false, 'message' => 'Cannot feature an inactive user. Please activate the user first.'], 422);
            }

            return back()->with('error', 'Cannot feature an inactive user. Please activate the user first.');
        }

        $user->is_featured = ! $user->is_featured;
        $user->save();

        $status = $user->is_featured ? 'added to' : 'removed from';

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'is_featured' => $user->is_featured,
                'message' => "User '{$user->name}' has been {$status} featured list.",
            ]);
        }

        return back()->with('success', "User '{$user->name}' has been {$status} featured list.");
    }

    /**
     * Toggle the visibility status of a user.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $newVisibility = ! $user->is_visible;
        $updateData = ['is_visible' => $newVisibility];

        // If deactivating, also turn off featured status
        if (! $newVisibility) {
            $updateData['is_featured'] = false;
        }

        $user->fill($updateData);
        $user->save();

        // Work profiles follow their person. Businesses are left alone when hiding: the visible
        // scope already hides a business once none of its people is visible. Re-activating still
        // restores businesses hidden by earlier deactivations.
        $user->companies()->update(['is_visible' => $newVisibility]);
        if ($newVisibility) {
            $user->businesses()->update(['is_visible' => true]);
        }

        $status = $user->is_visible ? 'activated' : 'deactivated';

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'is_visible' => $user->is_visible,
                'is_featured' => $user->is_featured,
                'message' => "User '{$user->name}' has been {$status} successfully.",
            ]);
        }

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
     * Import users from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // Max 10MB
        ]);

        // Increase execution time and memory limit for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        try {
            $importId = (string) Str::uuid();
            $file = $request->file('file');

            if (! $file instanceof \Illuminate\Http\UploadedFile) {
                throw new \RuntimeException('No valid file was uploaded.');
            }

            // Store file so queue worker can access it
            $path = $file->store('imports', 'local');

            // Peek at the file to auto-detect format
            $importer = \App\Factories\ImporterFactory::make((string) $file->getRealPath(), $importId, $file->getClientOriginalName());

            // Queue it — runs in background via artisan queue:work
            Excel::queueImport($importer, (string) $path, 'local');

            $format = $importer instanceof UCOStudentImport ? 'UCO Student Profile' : 'Form Response';

            session(['active_user_import_id' => $importId]);

            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'importId' => $importId,
                    'format' => $format,
                    'message' => "Import started ({$format})! Format auto-detected.",
                ]);
            }

            return back()
                ->with('import_success', "Import started ({$format})! Format auto-detected. Please wait.");
        } catch (\Exception $e) {
            Log::error('User import exception: '.$e->getMessage());
            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Import failed: '.$e->getMessage(),
                ], 500);
            }

            return redirect()
                ->route('users.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Download Excel template for user import.
     */
    public function downloadTemplate()
    {
        return (new UserImportTemplate)->download();
    }

    // --- Form helpers ---

    /**
     * Counts shown on the list page (everyone except admins).
     *
     * @return array{total: int, entrepreneurs: int, intrapreneurs: int, alumni: int}
     */
    private function userCounts(): array
    {
        return [
            'total' => User::where('role', '!=', 'admin')->count(),
            'entrepreneurs' => User::where('role', '!=', 'admin')->whereRaw('LOWER(current_status) = ?', ['entrepreneur'])->count(),
            'intrapreneurs' => User::where('role', '!=', 'admin')->whereRaw('LOWER(current_status) = ?', ['intrapreneur'])->count(),
            'alumni' => User::where('role', '!=', 'admin')->where('student_status', 'alumni')->count(),
        ];
    }

    /**
     * Compress and store a profile photo on the public disk; returns its /storage URL.
     */
    private function storeProfilePhoto(UploadedFile $file, string $name): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($file);
        $path = $file->storeAs('profile-photos', Str::slug($name).'_'.time().'.'.$file->getClientOriginalExtension(), 'public');

        return '/storage/'.$path;
    }

    /**
     * Activity documents after the edit form: flagged ones deleted, new uploads added.
     */
    private function syncActivityDocuments(Request $request, User $user, string $name): array
    {
        $existingUrls = $user->activities_doc_url ?? [];

        if ($request->has('delete_activities_files')) {
            foreach ((array) $request->input('delete_activities_files') as $urlToDelete) {
                if (($key = array_search($urlToDelete, $existingUrls)) !== false) {
                    $user->deleteFileFromStorage($urlToDelete);
                    unset($existingUrls[$key]);
                }
            }
            $existingUrls = array_values($existingUrls);
        }

        $newUrls = [];
        if ($request->hasFile('activities_docs')) {
            $files = $request->file('activities_docs');
            foreach (is_array($files) ? $files : [$files] as $file) {
                if ($file instanceof UploadedFile) {
                    $path = $file->storeAs('student-activities', 'act_'.Str::slug($name).'_'.time().'_'.Str::random(5).'.'.$file->getClientOriginalExtension(), 'public');
                    $newUrls[] = '/storage/'.$path;
                }
            }
        }

        return array_merge($existingUrls, $newUrls);
    }

    /**
     * Give the businesses picked in the form to this user (it only adds, it never takes businesses away).
     */
    private function assignBusinesses(Request $request, User $user): void
    {
        $ownedBusinesses = (array) $request->input('owned_businesses');
        if (! empty($ownedBusinesses)) {
            Business::whereIn('id', $ownedBusinesses)
                ->update(['user_id' => $user->id]);
        }
    }
}
