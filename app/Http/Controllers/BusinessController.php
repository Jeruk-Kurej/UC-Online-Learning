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
use App\Services\ImageOptimizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Public directory, detail pages and owner create/edit for entrepreneur businesses.
 */
class BusinessController extends Controller
{
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
            $query = Business::visible()->with(['user', 'members', 'category', 'products'])->entrepreneur();
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
        if ($this->currentUserIsAdmin()) {
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
     * Show the form for creating a new business.
     */
    public function create(): View
    {
        return view('businesses.form', [
            'business' => null,
            'categories' => Category::all(),
            'users' => User::where('role', '!=', 'admin')->orderBy('name')->get(),
            'provinces' => Province::orderBy('name')->get(),
            'selectedProvinceId' => null,
            'availableCities' => collect(),
            'memberIds' => [],
            'cancelUrl' => $this->formCancelUrl(),
        ]);
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
        $isCollabPartner = auth()->check() && auth()->user()->collabStatusWith($business->user) === 'accepted';
        $isAdmin = auth()->user()?->isAdmin();

        $isBusinessVisible = $business->is_visible && $business->approval_status === 'approved' && $business->hasVisiblePeople();

        if (!$isBusinessVisible && !$isOwner && !$isCoOwner && !$isCollabPartner && !$isAdmin) {
            abort(404);
        }

        return view('businesses.show', [
            'business' => $business,
            'canManageBusiness' => auth()->check() && $business->canBeManagedBy(auth()->user()),
            'collabStatus' => auth()->check() && $business->user_id && auth()->id() !== $business->user_id ? auth()->user()->collabStatusWith($business->public_owner) : null,
            'showRevenue' => Gate::allows('update', $business),
            'offersProducts' => in_array($business->offering_mode, ['product', 'both']),
            'offersServices' => in_array($business->offering_mode, ['service', 'both']),
            'products' => $business->products->where('type', 'product'),
            'services' => $business->products->where('type', 'service'),
        ]);
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
            return app(CompanyController::class)->show($company);
        }

        abort(404);
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

        return view('businesses.form', [
            'business' => $business,
            'categories' => $categories,
            'users' => $users,
            'provinces' => $provinces,
            'selectedProvinceId' => $selectedProvinceId,
            'availableCities' => $availableCities,
            'memberIds' => $business->members()->pluck('users.id')->toArray(),
            'cancelUrl' => $this->formCancelUrl(),
        ]);
    }

    /**
     * Where Back / Cancel on the business form leads: the admin list for admins, "my businesses" for everyone else.
     */
    private function formCancelUrl(): string
    {
        return auth()->check() && auth()->user()->isAdmin() ? route('businesses.index') : route('businesses.my');
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
        if (! $this->currentUserIsAdmin()) {
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
        if ($this->currentUserIsAdmin() && $request->has('is_featured')) {
            $data['is_featured'] = $request->boolean('is_featured');
        }

        // Handle logo deletion
        if ($request->boolean('delete_logo')) {
            $business->deleteFileFromStorage($business->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            $business->deleteFileFromStorage($business->getRawOriginal('logo_url'));

            /** @var \Illuminate\Http\UploadedFile $logoFile */
            $logoFile = $request->file('logo');
            app(ImageOptimizerService::class)->optimizeUploadedFile($logoFile);
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
        if (! $this->currentUserIsAdmin()) {
            $data['is_visible'] = false;
        } else {
            $data['is_visible'] = true;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo')) {
            /** @var \Illuminate\Http\UploadedFile $logoFile */
            $logoFile = $request->file('logo');
            app(ImageOptimizerService::class)->optimizeUploadedFile($logoFile);
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

        $message = $this->currentUserIsAdmin()
            ? 'Business created successfully!'
            : 'Business submitted for approval! An admin will review it soon.';

        return redirect()->route('businesses.my')->with('success', $message);
    }
}
