<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use App\Http\Requests\UpdateBusinessRequest;
use App\Imports\FormResponseImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BusinessController extends Controller
{
    /**
     * Get authenticated user as User instance.
     */
    private function getAuthUser(): User
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        return $user;
    }

    /**
     * Display a listing of the businesses.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $viewType = $request->get('view', 'entrepreneur');

        if ($viewType === 'intrapreneur') {
            $query = \App\Models\Company::visible()->with(['user', 'category']);
        } else {
            // If admin and explicitly asking for pending, show only invisible
            if (auth()->check() && auth()->user()->isAdmin() && $request->status === 'pending') {
                $query = Business::where('is_visible', false)->with(['user', 'category', 'products'])->entrepreneur();
            } 
            // If admin but not filtering for pending, show all (so they can see what needs approval in the main list)
            elseif (auth()->check() && auth()->user()->isAdmin()) {
                $query = Business::with(['user', 'category', 'products'])->entrepreneur();
            }
            // Default for students/public: only visible
            else {
                $query = Business::visible()->with(['user', 'category', 'products'])->entrepreneur();
            }
        }

        if ($search) {
            $query->where(function($q) use ($search, $viewType) {
                $q->where('name', 'LIKE', "%{$search}%");
                if ($viewType === 'entrepreneur') {
                    $q->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('city', 'LIKE', "%{$search}%")
                      ->orWhere('province', 'LIKE', "%{$search}%");
                } else {
                    $q->orWhere('job_description', 'LIKE', "%{$search}%");
                }
            });
        }
        
        $category = $request->get('category');
        if ($category) {
            $query->where('category_id', $category);
        }

        if ($viewType === 'entrepreneur') {
            if ($request->city) {
                $query->where('city', 'LIKE', "%{$request->city}%");
            }
            if ($request->province) {
                $query->where('province', 'LIKE', "%{$request->province}%");
            }
        }
        
        $businesses = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();
        
        $availableProvinces = Business::visible()->whereNotNull('province')->distinct()->pluck('province')->sort();
        
        // Dynamic city list based on selected province
        $cityQuery = Business::visible()->whereNotNull('city');
        if ($request->province) {
            $cityQuery->where('province', $request->province);
        }
        $availableCities = $cityQuery->distinct()->pluck('city')->sort();

        // Mapping for Alpine.js dependent dropdown
        $provinceCityMap = Business::visible()
            ->whereNotNull('province')
            ->whereNotNull('city')
            ->select('province', 'city')
            ->distinct()
            ->get()
            ->groupBy('province')
            ->map(fn($items) => $items->pluck('city')->sort()->values())
            ->toArray();

        $featuredBusinessCount = Business::where('is_featured', true)->count();

        // If admin, also get count of businesses waiting for approval
        $pendingCount = 0;
        if (auth()->check() && auth()->user()->isAdmin()) {
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

    public function adminIndex(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $status = $request->get('status');
        $search = $request->get('search');
        $featured = $request->get('featured');

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
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $businesses = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()
                ->view('businesses.admin.partials.list', compact('businesses'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.admin.index', compact(
            'businesses', 
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
    public function updateStatus(Request $request, Business $business)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,need_revision,pending',
            'rejection_reason' => 'required_if:status,rejected,need_revision'
        ]);

        $business->update([
            'approval_status' => $request->status,
            'rejection_reason' => in_array($request->status, ['rejected', 'need_revision']) ? $request->rejection_reason : null,
            'is_visible' => $request->status === 'approved'
        ]);

        return back()->with('success', "Business status updated to " . ucfirst(str_replace('_', ' ', $request->status)));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        // Removed admin check to allow students to create businesses

        $categories = Category::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $availableCities = \App\Models\Regency::pluck('name')->sort();
        $provinces = \App\Models\Province::orderBy('name')->get();
        return view('businesses.create', compact('categories', 'users', 'availableCities', 'provinces'));
    }

    /**
     * Get regencies for a given province.
     */
    public function getRegencies(Request $request)
    {
        $provinceId = $request->get('province_id');
        if (!$provinceId) {
            return response()->json([]);
        }

        $regencies = \App\Models\Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($regencies);
    }

    /**
     * Display the specified business.
     */
    public function show(Business $business)
    {
        $business->load(['user', 'category', 'products', 'legalDocuments', 'certifications', 'members']);
        return view('businesses.show', compact('business'));
    }

    /**
     * Resolve /showcase/{slug} to either a Business or a Company.
     */
    public function resolveShowcase($slug)
    {
        $business = Business::where('slug', $slug)->first();
        if ($business) {
            return $this->show($business);
        }

        $company = \App\Models\Company::where('slug', $slug)->first();
        if ($company) {
            return $this->showIntrapreneur($company);
        }

        abort(404);
    }

    /**
     * Display the specified intrapreneur (Company).
     */
    public function showIntrapreneur(\App\Models\Company $company)
    {
        $company->load(['user', 'category']);
        return view('businesses.show_intrapreneur', compact('company'));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(Business $business)
    {
        $this->authorize('update', $business);
        
        // Load existing relationships
        $business->load(['user', 'category', 'products', 'members', 'legalDocuments', 'certifications']);
        
        $categories = Category::all();
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $provinces = \App\Models\Province::orderBy('name')->get();
        
        $selectedProvinceId = \App\Models\Province::where('name', $business->province)->first()?->id;
        $availableCities = $selectedProvinceId 
            ? \App\Models\Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        // Prepare variables for the view
        $existingServices = []; // Placeholder as services are not yet separated in DB
        $legalDocs = $business->legalDocuments; // Use the many-to-many relationship

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
    public function update(UpdateBusinessRequest $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validated();
        
        // Map form fields to database columns
        $data = $validated;

        // Security: only admins can change visibility or featured status or primary owner
        if (!auth()->user()->isAdmin()) {
            unset($data['is_visible'], $data['is_featured'], $data['user_id']);
        }
        // category_id is now sent directly from the form
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
        
        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_url'] = $path; // Fixed: using logo_url as per model
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $data['province'] = \App\Models\Province::find($data['province'])?->name ?? $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $data['city'] = \App\Models\Regency::find($data['city'])?->name ?? $data['city'];
        }

        $business->update($data);
        
        // Sync members if provided (form uses owner_ids)
        if ($request->has('owner_ids')) {
            $business->members()->sync($request->owner_ids);
        }

        return redirect()->route('businesses.show', $business)->with('success', 'Business updated successfully!');
    }

    /**
     * Display user's businesses.
     */
    public function my()
    {
        $user = Auth::user();
        $myBusinesses = Business::with(['category'])->where('user_id', $user->id)->latest()->get();
        return view('businesses.my', compact('myBusinesses'));
    }

    /**
     * Store a newly created business (from manual form).
     */
    public function store(\App\Http\Requests\StoreBusinessRequest $request)
    {
        $validated = $request->validated();
        
        // Map form fields to database columns
        $data = $validated;
        // category_id is now sent directly from the form
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
        if (!auth()->user()->isAdmin()) {
            $data['is_visible'] = false;
        } else {
            $data['is_visible'] = true;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_url'] = $path;
        }

        // Resolve Province and City names if IDs are sent
        if (isset($data['province']) && is_numeric($data['province'])) {
            $data['province'] = \App\Models\Province::find($data['province'])?->name ?? $data['province'];
        }
        if (isset($data['city']) && is_numeric($data['city'])) {
            $data['city'] = \App\Models\Regency::find($data['city'])?->name ?? $data['city'];
        }

        $business = Business::create($data);

        // Sync members if provided
        if ($request->has('owner_ids')) {
            $business->members()->sync($request->owner_ids);
        }

        $message = auth()->user()->isAdmin() 
            ? 'Business created successfully!' 
            : 'Business submitted for approval! An admin will review it soon.';

        return redirect()->route('businesses.my')->with('success', $message);
    }

    /**
     * Approve a business (admin only).
     */
    public function approve(Business $business)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $business->update(['is_visible' => true]);

        return back()->with('success', "Business \"{$business->name}\" has been approved and is now visible.");
    }

    public function toggleFeatured(Business $business)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        if ($business->is_featured) {
            $business->update(['is_featured' => false]);
            $msg = "\"{$business->name}\" removed from featured.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);
        }


        $business->update(['is_featured' => true]);
        $msg = "\"{$business->name}\" is now featured.";
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }


    /**
     * Import businesses from CSV/Excel using auto-detected importer.
     */
    public function destroy(Business $business)
    {
        if (!$this->getAuthUser()->isAdmin()) {
            abort(403);
        }

        $business->delete();

        return redirect()->route('businesses.index')
            ->with('success', 'Business deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $importId = (string) Str::uuid();
            $file = $request->file('file');

            // Store file to local temp disk so queue worker can access it
            $path = $file->store('imports', 'local');

            // Peek at the file to auto-detect format using the local temp upload file
            $importer = $this->detectImporter($file->getRealPath(), $importId, $file->getClientOriginalName());

            // Queue it — runs in background via `php artisan queue:work`
            Excel::queueImport($importer, $path, 'local');

            $format = $importer instanceof \App\Imports\UCOStudentImport ? 'UCO Student Profile' : 'Form Response';

            // Store importId in session so frontend can poll progress
            session(['active_import' => $importId]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'importId' => $importId,
                    'format' => $format,
                    'message' => "Import queued! Format: {$format}."
                ]);
            }

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                         ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function createAchievement(\App\Models\Company $company)
    {
        if (Auth::id() !== $company->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return view('businesses.add_achievement', compact('company'));
    }

    public function addAchievement(\App\Models\Company $company, Request $request)
    {
        if (Auth::id() !== $company->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'achievement' => 'required|string|max:500',
        ]);

        $newAchievement = trim($request->achievement);
        
        $existing = trim($company->achievement);
        if (empty($existing)) {
            $updated = "- " . $newAchievement;
        } else {
            $updated = $existing . "\n- " . $newAchievement;
        }

        $company->update(['achievement' => $updated]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Achievement added successfully!',
                'achievements' => $company->achievements_list
            ]);
        }

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Achievement added successfully!');
    }

    public function deleteAchievement(\App\Models\Company $company, Request $request)
    {
        if (Auth::id() !== $company->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $achievements = $company->achievements_list;
        $index = (int) $request->index;

        if (isset($achievements[$index])) {
            unset($achievements[$index]);
            $updated = empty($achievements) ? null : implode("\n", array_map(fn($item) => "- " . $item, $achievements));
            $company->update(['achievement' => $updated]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Achievement deleted successfully!',
                'achievements' => $company->achievements_list ?? []
            ]);
        }

        return back()->with('success', 'Achievement deleted successfully!');
    }

    /**
     * Peek at the raw file to determine which importer to use.
     */
    private function detectImporter(string $path, string $importId, string $originalName = '')
    {
        // For xlsx/xls we can't search raw text easily — check filename heuristic
        $ext = strtolower(pathinfo($originalName ?: $path, PATHINFO_EXTENSION));
        if (in_array($ext, ['xlsx', 'xls'])) {
            if (stripos($originalName, 'UCO') !== false || stripos($originalName, 'Student') !== false) {
                Log::info("Import: XLSX filename heuristic → UCOStudentImport");
                return new \App\Imports\UCOStudentImport($importId);
            }
            Log::info("Import: XLSX default → FormResponseImport");
            return new \App\Imports\FormResponseImport($importId);
        }

        // For CSV/Raw: read first 2KB and search for markers
        $handle = fopen($path, 'r');
        $peek = fread($handle, 2048);
        fclose($handle);

        // UCO Student format markers: "NIS" AND "Sub Prodi"
        // Form Response markers: "Timestamp" OR "Email Address" (row 1)
        if (stripos($peek, 'NIS') !== false && stripos($peek, 'Sub Prodi') !== false) {
            Log::info("Import: detected UCO Student Profile format via content markers");
            return new \App\Imports\UCOStudentImport($importId);
        }

        Log::info("Import: falling back to Form Response format");
        return new \App\Imports\FormResponseImport($importId);
    }
}
