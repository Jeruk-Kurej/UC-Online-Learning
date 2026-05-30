<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusinessRequest;
use App\Http\Requests\UpdateBusinessRequest;
use App\Models\Business;
use App\Models\Category;
use App\Models\Company;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class BusinessController
 *
 * Handles showcase listings, detail views, and CRUD management for Businesses (Entrepreneurship)
 * and Companies (Intrapreneurship), as well as administration statuses, CSV importing, and achievements.
 */
class BusinessController extends Controller
{
    /**
     * Get authenticated user as User instance.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If unauthenticated
     */
    private function getAuthUser(): User
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(401, 'Unauthenticated.');
        }

        return $user;
    }

    /**
     * Check if current authenticated user is an admin.
     */
    private function isUserAdmin(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    /**
     * Display a listing of the businesses.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->get('search');
        $viewType = $request->get('view', 'entrepreneur');

        if ($viewType === 'intrapreneur') {
            $query = Company::visible()->with(['user', 'category']);
        } else {
            $query = Business::visible()->with(['user', 'category', 'products'])->entrepreneur();
        }

        if ($search) {
            $query->where(function ($q) use ($search, $viewType) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'LIKE', "%{$search}%");
                    });

                if ($viewType === 'entrepreneur') {
                    $q->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%")
                        ->orWhere('province', 'LIKE', "%{$search}%");
                } else {
                    $q->orWhere('job_description', 'LIKE', "%{$search}%")
                        ->orWhere('position', 'LIKE', "%{$search}%")
                        ->orWhere('achievement', 'LIKE', "%{$search}%")
                        ->orWhere('city', 'LIKE', "%{$search}%")
                        ->orWhere('province', 'LIKE', "%{$search}%");
                }
            });
        }

        $category = $request->get('category');
        if ($category) {
            $query->where('category_id', $category);
        }

        $city = $request->get('city');
        $province = $request->get('province');
        if ($city) {
            $query->where('city', 'LIKE', "%{$city}%");
        }
        if ($province) {
            $query->where('province', 'LIKE', "%{$province}%");
        }

        $businesses = $query->latest()->paginate(6)->withQueryString();
        $categories = Category::all();

        $locationQuery = $viewType === 'intrapreneur' ? Company::visible() : Business::visible();

        $availableProvinces = $locationQuery->whereNotNull('province')->distinct()->pluck('province')->sort();

        // Dynamic city list based on selected province
        $cityQuery = ($viewType === 'intrapreneur' ? Company::visible() : Business::visible())->whereNotNull('city');
        $selectedProvince = $request->get('province');
        if ($selectedProvince) {
            $cityQuery->where('province', $selectedProvince);
        }
        $availableCities = $cityQuery->distinct()->pluck('city')->sort();

        // Mapping for Alpine.js dependent dropdown
        $provinceCityMap = ($viewType === 'intrapreneur' ? Company::visible() : Business::visible())
            ->whereNotNull('province')
            ->whereNotNull('city')
            ->select('province', 'city')
            ->distinct()
            ->get()
            ->groupBy('province')
            ->map(fn ($items) => $items->pluck('city')->sort()->values())
            ->toArray();

        $featuredBusinessCount = Business::where('is_featured', true)->count();

        // If admin, also get count of businesses waiting for approval
        $pendingCount = 0;
        if ($this->isUserAdmin()) {
            $pendingCount = Business::where('is_visible', false)->count();
        }

        if ($request->ajax()) {
            return response()
                ->view('businesses.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.index', compact(
            'businesses',
            'categories',
            'viewType',
            'availableCities',
            'availableProvinces',
            'provinceCityMap',
            'featuredBusinessCount',
            'pendingCount'
        ));
    }

    /**
     * Display a listing of businesses for administrative management.
     */
    public function adminIndex(Request $request): View|Response
    {
        if (! $this->isUserAdmin()) {
            abort(403);
        }

        $viewType = $request->get('type', 'entrepreneur'); // 'entrepreneur' or 'intrapreneur'
        $status = $request->get('status');
        $search = $request->get('search');
        $featured = $request->get('featured');

        if ($viewType === 'intrapreneur') {
            // ── Intrapreneur (Company) mode ──────────────────────────────────
            $query = Company::with(['user', 'category']);

            $totalBusinesses = Company::count();
            $approvedBusinesses = Company::where('approval_status', 'approved')->count();
            $pendingBusinesses = Company::where('approval_status', 'pending')->count();
            $rejectedBusinesses = Company::whereIn('approval_status', ['rejected', 'need_revision'])->count();
            $featuredBusinessesCount = 0;

            if ($status) {
                $query->where('approval_status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('position', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            $businesses = $query->latest()->paginate(10)->withQueryString();

            if ($request->ajax()) {
                return response()
                    ->view('businesses.admin.partials.list', compact('businesses', 'viewType'))
                    ->header('Vary', 'X-Requested-With');
            }

            return view('businesses.admin.index', compact(
                'businesses',
                'viewType',
                'status',
                'search',
                'featured',
                'totalBusinesses',
                'pendingBusinesses',
                'approvedBusinesses',
                'rejectedBusinesses',
                'featuredBusinessesCount'
            ));
        }

        // ── Entrepreneur (Business) mode ─────────────────────────────────────
        $viewType = 'entrepreneur';
        $query = Business::with(['user', 'category'])->entrepreneur();

        // Calculate statistics
        $totalBusinesses = Business::entrepreneur()->count();
        $pendingBusinesses = Business::entrepreneur()->where('approval_status', 'pending')->count();
        $approvedBusinesses = Business::entrepreneur()->where('approval_status', 'approved')->count();
        $rejectedBusinesses = Business::entrepreneur()->whereIn('approval_status', ['rejected', 'need_revision'])->count();
        $featuredBusinessesCount = Business::entrepreneur()->where('is_featured', true)->count();

        if ($status) {
            $query->where('approval_status', $status);
        }

        if ($featured === 'yes') {
            $query->where('is_featured', true);
        } elseif ($featured === 'no') {
            $query->where('is_featured', false);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $businesses = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()
                ->view('businesses.admin.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.admin.index', compact(
            'businesses',
            'viewType',
            'status',
            'search',
            'featured',
            'totalBusinesses',
            'pendingBusinesses',
            'approvedBusinesses',
            'rejectedBusinesses',
            'featuredBusinessesCount'
        ));
    }

    /**
     * Update business status (admin only).
     */
    public function updateStatus(Request $request, Business $business): RedirectResponse
    {
        if (! $this->isUserAdmin()) {
            abort(403);
        }

        $this->validate($request, [
            'status' => 'required|in:approved,rejected,need_revision,pending',
            'rejection_reason' => 'required_if:status,rejected,need_revision',
        ]);

        $status = $request->input('status');
        $rejectionReason = in_array($status, ['rejected', 'need_revision']) ? $request->input('rejection_reason') : null;

        $business->fill([
            'approval_status' => $status,
            'rejection_reason' => $rejectionReason,
            'is_visible' => $status === 'approved',
        ]);
        $business->save();

        return back()->with('success', 'Business status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    /**
     * Update company status (admin only).
     */
    public function updateCompanyStatus(Request $request, Company $company): RedirectResponse
    {
        if (! $this->isUserAdmin()) {
            abort(403);
        }

        $this->validate($request, [
            'status' => 'required|in:approved,rejected,need_revision,pending',
            'rejection_reason' => 'required_if:status,rejected,need_revision',
        ]);

        $status = $request->input('status');
        $rejectionReason = in_array($status, ['rejected', 'need_revision']) ? $request->input('rejection_reason') : null;

        $company->fill([
            'approval_status' => $status,
            'rejection_reason' => $rejectionReason,
            'is_visible' => $status === 'approved',
        ]);
        $company->save();

        return back()->with('success', 'Company status updated to '.ucfirst(str_replace('_', ' ', (string) $status)));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create(): View
    {
        $categories = Category::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $availableCities = Regency::pluck('name')->sort();
        $provinces = Province::orderBy('name')->get();

        return view('businesses.create', compact('categories', 'users', 'availableCities', 'provinces'));
    }

    /**
     * Get regencies for a given province via AJAX.
     */
    public function getRegencies(Request $request): JsonResponse
    {
        $provinceId = $request->get('province_id');
        if (! $provinceId) {
            return new JsonResponse([]);
        }

        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($regency) {
                return [
                    'id' => $regency->id,
                    'name' => \Illuminate\Support\Str::title(strtolower($regency->name)),
                ];
            });

        return new JsonResponse($regencies);
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business): View
    {
        $business->load(['user', 'category', 'products', 'legalDocuments', 'certifications', 'members']);

        // Security check: Check if visible or authorized
        $isOwner = auth()->id() === $business->user_id;
        $isCoOwner = auth()->check() && $business->members->contains(auth()->id());
        $isAdmin = auth()->user()?->isAdmin();

        $isUserVisible = $business->user?->is_visible ?? true;
        $isBusinessVisible = $business->is_visible && $business->approval_status === 'approved' && $isUserVisible;

        if (!$isBusinessVisible && !$isOwner && !$isCoOwner && !$isAdmin) {
            abort(404);
        }

        return view('businesses.show', compact('business'));
    }

    /**
     * Resolve /showcase/{slug} to either a Business or a Company.
     *
     * @param  string  $slug
     */
    public function resolveShowcase($slug): View|Response
    {
        $business = Business::where('slug', $slug)->first();
        if ($business) {
            return $this->show($business);
        }

        $company = Company::where('slug', $slug)->first();
        if ($company) {
            return $this->showIntrapreneur($company);
        }

        abort(404);
    }

    /**
     * Display the specified intrapreneur (Company).
     */
    public function showIntrapreneur(Company $company): View
    {
        $company->load(['user', 'category']);

        // Security check: Check if visible or authorized
        $isOwner = auth()->id() === $company->user_id;
        $isAdmin = auth()->user()?->isAdmin();

        $isUserVisible = $company->user?->is_visible ?? true;
        $isCompanyVisible = $company->is_visible && $isUserVisible;

        if (!$isCompanyVisible && !$isOwner && !$isAdmin) {
            abort(404);
        }

        return view('businesses.show_intrapreneur', compact('company'));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business): View
    {
        $this->authorize('update', $business);

        $business->load(['user', 'category', 'products', 'members', 'legalDocuments', 'certifications']);

        $categories = Category::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        $selectedProvinceId = Province::where('name', $business->province)->first()?->id;
        $availableCities = $selectedProvinceId
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        $existingServices = [];
        $legalDocs = $business->legalDocuments;

        return view('businesses.edit', compact(
            'business',
            'categories',
            'users',
            'existingServices',
            'legalDocs',
            'availableCities',
            'provinces',
            'selectedProvinceId'
        ));
    }

    /**
     * Update the specified business.
     */
    public function update(UpdateBusinessRequest $request, Business $business): RedirectResponse
    {
        $this->authorize('update', $business);

        $validated = $request->validated();
        $data = $validated;

        // Security: only admins can change visibility or featured status or primary owner
        if (! $this->isUserAdmin()) {
            unset($data['is_visible'], $data['is_featured'], $data['user_id']);
        }

        if (isset($validated['business_type_id'])) {
            $data['category_id'] = $validated['business_type_id'];
            unset($data['business_type_id']);
        }
        if (isset($validated['business_mode'])) {
            $data['offering_type'] = $validated['business_mode'];
            unset($data['business_mode']);
        }
        if (isset($validated['phone'])) {
            $data['phone_number'] = $validated['phone'];
            unset($data['phone']);
        }
        if (isset($validated['whatsapp_number'])) {
            $data['whatsapp'] = $validated['whatsapp_number'];
            unset($data['whatsapp_number']);
        }
        if (isset($validated['instagram_handle'])) {
            $data['instagram'] = $validated['instagram_handle'];
            unset($data['instagram_handle']);
        }
        $data['is_featured'] = $request->boolean('is_featured');

        // Handle logo deletion
        if ($request->boolean('delete_logo')) {
            $this->deleteFileFromStorage($business->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            $this->deleteFileFromStorage($business->getRawOriginal('logo_url'));

            /** @var \Illuminate\Http\UploadedFile $logoFile */
            $logoFile = $request->file('logo');
            $path = $logoFile->store('logos', 'public');
            $data['logo_url'] = $path;
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $data['province'] = Province::find($data['province'])?->name ?? $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $data['city'] = Regency::find($data['city'])?->name ?? $data['city'];
        }

        $business->fill($data);
        $business->save();

        // Sync members if provided
        if ($request->has('owner_ids')) {
            $ownerIds = (array) $request->input('owner_ids');
            $business->members()->sync($ownerIds);
        }

        return redirect()->route('businesses.show', $business)->with('success', 'Business updated successfully!');
    }

    /**
     * Display the authenticated user's businesses.
     */
    public function my(): View
    {
        $user = Auth::user();
        $myBusinesses = Business::with(['category'])->where('user_id', $user->id)->latest()->get();
        $myCompanies = Company::with(['category'])->where('user_id', $user->id)->latest()->get();

        return view('businesses.my', compact('myBusinesses', 'myCompanies'));
    }

    /**
     * Store a newly created business.
     */
    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $data = $validated;

        if (isset($validated['business_mode'])) {
            $data['offering_type'] = $validated['business_mode'];
            unset($data['business_mode']);
        }
        if (isset($validated['phone'])) {
            $data['phone_number'] = $validated['phone'];
            unset($data['phone']);
        }
        if (isset($validated['whatsapp_number'])) {
            $data['whatsapp'] = $validated['whatsapp_number'];
            unset($data['whatsapp_number']);
        }
        if (isset($validated['instagram_handle'])) {
            $data['instagram'] = $validated['instagram_handle'];
            unset($data['instagram_handle']);
        }

        $data['user_id'] = Auth::id();
        $data['type'] = 'entrepreneur';

        // Student-created businesses are invisible until approved by admin
        if (! $this->isUserAdmin()) {
            $data['is_visible'] = false;
        } else {
            $data['is_visible'] = true;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            /** @var \Illuminate\Http\UploadedFile $logoFile */
            $logoFile = $request->file('logo');
            $path = $logoFile->store('logos', 'public');
            $data['logo_url'] = $path;
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $data['province'] = Province::find($data['province'])?->name ?? $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $data['city'] = Regency::find($data['city'])?->name ?? $data['city'];
        }

        $business = Business::create($data);

        // Sync members if provided
        if ($request->has('owner_ids')) {
            $ownerIds = (array) $request->input('owner_ids');
            $business->members()->sync($ownerIds);
        }

        $message = $this->isUserAdmin()
            ? 'Business created successfully!'
            : 'Business submitted for approval! An admin will review it soon.';

        return redirect()->route('businesses.my')->with('success', $message);
    }

    /**
     * Approve a business (admin only).
     */
    public function approve(Business $business): RedirectResponse
    {
        if (! $this->isUserAdmin()) {
            abort(403);
        }

        $business->is_visible = true;
        $business->save();

        return back()->with('success', "Business \"{$business->name}\" has been approved and is now visible.");
    }

    /**
     * Toggle the featured status of a business.
     */
    public function toggleFeatured(Request $request, Business $business): RedirectResponse|JsonResponse
    {
        if (! $this->isUserAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        if ($business->approval_status !== 'approved') {
            $msg = 'Only approved businesses can be featured.';
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['approval' => $msg]);
        }

        if ($business->is_featured) {
            $business->is_featured = false;
            $business->save();
            $msg = "\"{$business->name}\" removed from featured.";
            if ($request->ajax() || $request->wantsJson()) {
                return new JsonResponse(['success' => true, 'message' => $msg]);
            }

            return back()->with('success', $msg);
        }

        $business->is_featured = true;
        $business->save();
        $msg = "\"{$business->name}\" is now featured.";
        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Delete a business.
     */
    public function destroy(Business $business): RedirectResponse
    {
        if (! $this->getAuthUser()->isAdmin()) {
            abort(403);
        }

        $business->delete();

        return redirect()->route('businesses.index')
            ->with('success', 'Business deleted successfully.');
    }

    /**
     * Import businesses from CSV/Excel using auto-detected importer.
     */
    public function import(Request $request): RedirectResponse|JsonResponse
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $importId = (string) Str::uuid();
            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $request->file('file');

            $path = $file->store('imports', 'local');

            $importer = $this->detectImporter($file->getRealPath(), $importId, $file->getClientOriginalName());

            Excel::queueImport($importer, $path, 'local');

            $format = $importer instanceof \App\Imports\UCOStudentImport ? 'UCO Student Profile' : 'Form Response';

            session(['active_import' => $importId]);

            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => true,
                    'importId' => $importId,
                    'format' => $format,
                    'message' => "Import queued! Format: {$format}.",
                ]);
            }

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: '.$e->getMessage());
            if ($request->wantsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Import failed: '.$e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new company achievement.
     */
    public function createAchievement(Company $company): View
    {
        if (Auth::id() !== $company->user_id && ! $this->isUserAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return view('businesses.add_achievement', compact('company'));
    }

    /**
     * Add achievement to a company.
     */
    public function addAchievement(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        if (Auth::id() !== $company->user_id && ! $this->isUserAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate($request, [
            'achievement' => 'required|string|max:500',
        ]);

        $newAchievement = trim((string) $request->input('achievement'));

        $existing = trim($company->achievement);
        if (empty($existing)) {
            $updated = '- '.$newAchievement;
        } else {
            $updated = $existing."\n- ".$newAchievement;
        }

        $company->achievement = $updated;
        $company->save();

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Achievement added successfully!',
                'achievements' => $company->achievements_list,
            ]);
        }

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Achievement added successfully!');
    }

    /**
     * Delete achievement from a company.
     */
    public function deleteAchievement(Company $company, Request $request): RedirectResponse|JsonResponse
    {
        if (Auth::id() !== $company->user_id && ! $this->isUserAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->validate($request, [
            'index' => 'required|integer|min:0',
        ]);

        $achievements = $company->achievements_list;
        $index = (int) $request->input('index');

        if (isset($achievements[$index])) {
            unset($achievements[$index]);
            $updated = empty($achievements) ? null : implode("\n", array_map(fn ($item) => '- '.$item, $achievements));
            $company->achievement = $updated;
            $company->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Achievement deleted successfully!',
                'achievements' => $company->achievements_list ?? [],
            ]);
        }

        return back()->with('success', 'Achievement deleted successfully!');
    }

    /**
     * Peek at the raw file to determine which importer to use.
     */
    private function detectImporter(string $path, string $importId, string $originalName = ''): object
    {
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));

        // Auto-detect importType based on CSV content first (most robust)
        $detectedType = null;
        if ($ext === 'csv' && file_exists($path)) {
            $handle = fopen($path, 'r');
            if ($handle) {
                $headers = fgetcsv($handle);
                if ($headers) {
                    // Normalize headers: lowercase and remove spaces, underscores, and question marks
                    $normHeaders = array_map(function($h) {
                        return strtolower(trim(str_replace([' ', '_', '?'], '', $h)));
                    }, $headers);

                    $selectedIdx = array_search('selected', $normHeaders);
                    $categoryIdx = array_search('category', $normHeaders);

                    if ($selectedIdx !== false && $categoryIdx !== false) {
                        while (($row = fgetcsv($handle)) !== false) {
                            $selectedVal = strtolower(trim($row[$selectedIdx] ?? ''));
                            $categoryVal = strtolower(trim($row[$categoryIdx] ?? ''));

                            // If we find any row where Category is Intrapreneur and Selected is truthy
                            if (str_contains($categoryVal, 'intrapreneur') && in_array($selectedVal, ['true', '1', 'yes', 'selected', 'y'])) {
                                $detectedType = 'intrapreneur';
                                break;
                            }
                        }
                    }
                }
                fclose($handle);
            }
        }

        // Fall back to filename check if content detection didn't resolve it
        if (!$detectedType) {
            $lowerName = strtolower($originalName ?: basename($path));
            if (str_contains($lowerName, 'intrapreneur')) {
                $detectedType = 'intrapreneur';
            } elseif (str_contains($lowerName, 'entrepreneur')) {
                $detectedType = 'entrepreneur';
            } else {
                $detectedType = 'entrepreneur'; // default fallback
            }
        }

        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info('Import: XLSX filename heuristic → UCOStudentImport');
                return new \App\Imports\UCOStudentImport($importId);
            }
            Log::info('Import: XLSX default → FormResponseImport (' . $detectedType . ')');
            $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.xlsx' : 'entrepreneur.xlsx';
            return new \App\Imports\FormResponseImport($importId, $constructedName);
        }

        // For CSV/Raw: read first 2KB and search for markers
        $handle = fopen($path, 'r');
        if (! $handle) {
            Log::info('Import: falling back to Form Response format (' . $detectedType . ')');
            $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.csv' : 'entrepreneur.csv';
            return new \App\Imports\FormResponseImport($importId, $constructedName);
        }
        $peek = fread($handle, 2048);
        fclose($handle);

        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            Log::info('Import: detected UCO Student Profile format via content markers');
            return new \App\Imports\UCOStudentImport($importId);
        }

        Log::info('Import: falling back to Form Response format (' . $detectedType . ')');
        $constructedName = $detectedType === 'intrapreneur' ? 'intrapreneur.csv' : 'entrepreneur.csv';
        return new \App\Imports\FormResponseImport($importId, $constructedName);
    }

    /**
     * Safely delete a file from local public storage or Cloudinary.
     */
    private function deleteFileFromStorage(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
            return;
        }

        // Handle Cloudinary URL
        if (str_contains($pathOrUrl, 'cloudinary.com')) {
            try {
                Business::deleteCloudinaryImage($pathOrUrl);
            } catch (\Throwable $e) {
                // silently swallow
            }

            return;
        }

        // Normalize local storage path
        $relativePath = $pathOrUrl;
        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            $relativePath = parse_url($relativePath, PHP_URL_PATH);
        }

        if (str_starts_with($relativePath, '/storage/')) {
            $relativePath = substr($relativePath, strlen('/storage/'));
        } elseif (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, strlen('storage/'));
        }

        $relativePath = ltrim($relativePath, '/');

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    /**
     * Show form to create a company work profile (Intrapreneur).
     */
    public function createCompany(): View
    {
        $categories = Category::all();
        $provinces = Province::orderBy('name')->get();
        return view('businesses.create_intrapreneur', compact('categories', 'provinces'));
    }

    /**
     * Store a newly created company work profile (Intrapreneur).
     */
    public function storeCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'position' => 'required|string|max:255',
            'level_position' => 'nullable|string|max:255',
            'company_scale' => 'nullable|string|max:255',
            'province' => 'nullable',
            'city' => 'nullable',
            'year_started_working' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'job_description' => 'required|string|min:10',
            'logo_url' => 'nullable|image|max:5120',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['is_visible'] = true;
        $data['approval_status'] = Auth::user()->isAdmin() ? 'approved' : 'pending';

        if ($request->hasFile('logo_url')) {
            $file = $request->file('logo_url');
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $path = $file->storeAs('company-logos', Str::slug($validated['name']).'_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                $data['logo_url'] = '/storage/'.$path;
            }
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $provObj = Province::find($data['province']);
            $data['province'] = $provObj ? Str::title(strtolower($provObj->name)) : $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $regObj = Regency::find($data['city']);
            $data['city'] = $regObj ? Str::title(strtolower($regObj->name)) : $data['city'];
        }

        $company = Company::create($data);

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Work profile registered successfully!');
    }

    /**
     * Show form to edit a company work profile (Intrapreneur).
     */
    public function editCompany(Company $company): View
    {
        if (Auth::id() !== $company->user_id && !$this->isUserAdmin()) {
            abort(403);
        }

        $categories = Category::all();
        $provinces = Province::orderBy('name')->get();

        $selectedProvinceId = Province::where('name', $company->province)->first()?->id;
        $availableCities = $selectedProvinceId
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        return view('businesses.edit_intrapreneur', compact('company', 'categories', 'provinces', 'selectedProvinceId', 'availableCities'));
    }

    /**
     * Update a company work profile (Intrapreneur).
     */
    public function updateCompany(Company $company, Request $request): RedirectResponse
    {
        if (Auth::id() !== $company->user_id && !$this->isUserAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'position' => 'required|string|max:255',
            'level_position' => 'nullable|string|max:255',
            'company_scale' => 'nullable|string|max:255',
            'province' => 'nullable',
            'city' => 'nullable',
            'year_started_working' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
            'job_description' => 'required|string|min:10',
            'logo_url' => 'nullable|image|max:5120',
        ]);

        $data = $validated;

        // Handle logo deletion
        if ($request->boolean('delete_logo')) {
            $this->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo_url')) {
            $this->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $file = $request->file('logo_url');
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $path = $file->storeAs('company-logos', Str::slug($validated['name']).'_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                $data['logo_url'] = '/storage/'.$path;
            }
        }

        if ($validated['name'] !== $company->name) {
            $slug = Str::slug($validated['name']);
            $original = $slug;
            $i = 1;
            while (Company::where('slug', $slug)->where('id', '!=', $company->id)->exists()) {
                $slug = $original . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $provObj = Province::find($data['province']);
            $data['province'] = $provObj ? Str::title(strtolower($provObj->name)) : $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $regObj = Regency::find($data['city']);
            $data['city'] = $regObj ? Str::title(strtolower($regObj->name)) : $data['city'];
        }

        $company->update($data);

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Work profile updated successfully!');
    }
}
