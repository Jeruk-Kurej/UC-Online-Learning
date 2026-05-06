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
        
        $availableCities = Business::visible()->whereNotNull('city')->distinct()->pluck('city')->sort();
        $availableProvinces = Business::visible()->whereNotNull('province')->distinct()->pluck('province')->sort();
        $featuredBusinessCount = Business::where('is_featured', true)->count();

        // If admin, also get count of businesses waiting for approval
        $pendingCount = 0;
        if (auth()->check() && auth()->user()->isAdmin()) {
            $pendingCount = Business::where('is_visible', false)->count();
        }

        return view('businesses.index', compact('businesses', 'categories', 'availableCities', 'availableProvinces', 'viewType', 'featuredBusinessCount', 'pendingCount'));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        // Removed admin check to allow students to create businesses

        $categories = Category::all();
        $users = User::orderBy('name')->get();
        $availableCities = \App\Models\Regency::pluck('name')->sort();
        $availableProvinces = \App\Models\Province::pluck('name')->sort();
        return view('businesses.create', compact('categories', 'users', 'availableCities', 'availableProvinces'));
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
        $users = User::orderBy('name')->get();
        $availableCities = \App\Models\Regency::pluck('name')->sort();
        $availableProvinces = \App\Models\Province::pluck('name')->sort();
        
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
            'availableProvinces'
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

    /**
     * Toggle the featured status of a business (admin only, max 8 featured at once).
     */
    public function toggleFeatured(Business $business)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        if ($business->is_featured) {
            $business->update(['is_featured' => false]);
            return back()->with('success', "\"{$business->name}\" removed from featured.");
        }

        $featuredCount = Business::where('is_featured', true)->count();
        if ($featuredCount >= 8) {
            return back()->withErrors(['featured' => 'Maximum of 8 featured businesses reached. Un-feature one first.']);
        }

        $business->update(['is_featured' => true]);
        return back()->with('success', "\"{$business->name}\" is now featured.");
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

            return back()->with('success', "Import queued! Format: {$format}. Processing ~1500 rows in background...")
                         ->with('importId', $importId);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
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
