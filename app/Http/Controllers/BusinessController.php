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
use App\Services\UploadStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Public directory, detail pages and owner create/edit for entrepreneur businesses.
 */
class BusinessController extends Controller
{
    use Concerns\ResolvesRegionNames;

    public function index(Request $request): View|Response
    {
        $viewType = $request->get('view', 'entrepreneur');

        $businesses = $this->directoryQuery($request, $viewType)->newestFirst()->paginate(6)->withQueryString();

        if ($request->ajax()) {
            return response()
                ->view('businesses.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        return view('businesses.index', [
            'businesses' => $businesses,
            'categories' => Category::all(),
            'viewType' => $viewType,
            'availableProvinces' => $this->directoryLocations($viewType)->whereNotNull('province')->distinct()->pluck('province')->sort(),
            'directoryState' => $this->directoryState($request, $viewType),
        ]);
    }

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
                    'name' => Str::title(strtolower($regency->name)),
                ];
            });

        return new JsonResponse($regencies);
    }

    public function show(Business $business): View
    {
        $business->load(['user', 'category', 'products', 'legalDocuments', 'certifications', 'members']);

        $isOwner = auth()->id() === $business->user_id;
        $isCoOwner = auth()->check() && $business->members->contains(auth()->id());
        $isCollabPartner = auth()->check() && auth()->user()->collabStatusWith($business->user) === 'accepted';
        $isAdmin = auth()->user()?->isAdmin();

        $isBusinessVisible = $business->is_visible && $business->approval_status === 'approved' && $business->hasVisiblePeople();

        if (! $isBusinessVisible && ! $isOwner && ! $isCoOwner && ! $isCollabPartner && ! $isAdmin) {
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

    public function update(UpdateBusinessRequest $request, Business $business): RedirectResponse
    {
        $data = $request->businessData();

        // --- Only admins may change visibility, featured status or the primary owner ---
        if (! $this->currentUserIsAdmin()) {
            unset($data['is_visible'], $data['is_featured'], $data['user_id']);
        }

        if ($this->currentUserIsAdmin() && $request->has('is_featured')) {
            $data['is_featured'] = $request->boolean('is_featured');
        }

        if ($request->boolean('delete_logo')) {
            $business->deleteFileFromStorage($business->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        if ($request->hasFile('logo')) {
            $business->deleteFileFromStorage($business->getRawOriginal('logo_url'));
            $data['logo_url'] = $this->storeLogo($request->file('logo'));
        }

        $business->fill($this->resolveRegionNames($data));
        $business->save();

        $this->syncMembers($request, $business);

        return redirect()->route('businesses.show', $business)->with('success', 'Business updated successfully!');
    }

    public function my(): View
    {
        $user = Auth::user();
        $myBusinesses = Business::with(['category'])->where('user_id', $user->id)->latest()->get();
        $myCompanies = Company::with(['category'])->where('user_id', $user->id)->latest()->get();

        return view('businesses.my', compact('myBusinesses', 'myCompanies'));
    }

    public function store(StoreBusinessRequest $request): RedirectResponse
    {
        $data = $request->businessData();
        $data['user_id'] = Auth::id();
        $data['type'] = 'entrepreneur';

        // --- Businesses created by students stay hidden until an admin approves them ---
        if (! $this->currentUserIsAdmin()) {
            $data['is_visible'] = false;
        } else {
            $data['is_visible'] = true;
        }

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $this->storeLogo($request->file('logo'));
        }

        $business = Business::create($this->resolveRegionNames($data));

        $this->syncMembers($request, $business);

        $message = $this->currentUserIsAdmin()
            ? 'Business created successfully!'
            : 'Business submitted for approval! An admin will review it soon.';

        return redirect()->route('businesses.my')->with('success', $message);
    }

    // --- Form helpers ---

    private function storeLogo(UploadedFile $file): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($file);

        return app(UploadStorage::class)->store($file, 'logos');
    }

    private function syncMembers(Request $request, Business $business): void
    {
        if ($request->has('owner_ids')) {
            $business->members()->sync((array) $request->input('owner_ids'));
        }
    }

    // --- Directory list: visible ventures of the chosen type, narrowed by the search box and the filters ---

    private function directoryQuery(Request $request, string $viewType): Builder
    {
        $query = $viewType === 'intrapreneur'
            ? Company::visible()->with(['user', 'category'])
            : Business::visible()->with(['user', 'members', 'category', 'products'])->entrepreneur();

        if ($search = $request->get('search')) {
            $query->where(fn (Builder $matches) => $this->searchDirectory($matches, $search, $viewType));
        }
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }
        if ($city = $request->get('city')) {
            $query->where('city', 'LIKE', "%{$city}%");
        }
        if ($province = $request->get('province')) {
            $query->where('province', 'LIKE', "%{$province}%");
        }

        return $query;
    }

    /**
     * The name or the person's name, then the text columns of the venture type.
     */
    private function searchDirectory(Builder $query, $search, string $viewType): void
    {
        $query->where('name', 'LIKE', "%{$search}%")
            ->orWhereHas('user', fn (Builder $users) => $users->where('name', 'LIKE', "%{$search}%"));

        $columns = $viewType === 'entrepreneur'
            ? ['description', 'city', 'province']
            : ['job_description', 'position', 'achievement', 'city', 'province'];

        foreach ($columns as $column) {
            $query->orWhere($column, 'LIKE', "%{$search}%");
        }
    }

    // --- Directory filters ---

    private function directoryLocations(string $viewType): Builder
    {
        return $viewType === 'intrapreneur' ? Company::visible() : Business::visible();
    }

    /**
     * Initial state of the directory's Alpine component (tabs, province → city dropdown).
     */
    private function directoryState(Request $request, string $viewType): array
    {
        $cityQuery = $this->directoryLocations($viewType)->whereNotNull('city');
        if ($request->get('province')) {
            $cityQuery->where('province', $request->get('province'));
        }

        return [
            'viewType' => $viewType,
            'isPending' => $request->get('status') === 'pending',
            'provinceCityMap' => $this->directoryLocations($viewType)
                ->whereNotNull('province')
                ->whereNotNull('city')
                ->select('province', 'city')
                ->distinct()
                ->get()
                ->groupBy('province')
                ->map(fn ($items) => $items->pluck('city')->sort()->values())
                ->toArray(),
            'selectedProvince' => (string) $request->get('province'),
            'selectedCity' => (string) $request->get('city'),
            'availableCities' => $cityQuery->distinct()->pluck('city')->sort()->values(),
        ];
    }
}
