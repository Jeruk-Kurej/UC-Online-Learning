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
use Illuminate\Support\Collection as SupportCollection;
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
            Category::attachTo($businesses->getCollection());

            return response()
                ->view('businesses.partials.list', compact('businesses', 'viewType'))
                ->header('Vary', 'X-Requested-With');
        }

        $categories = Category::all();
        Category::attachTo($businesses->getCollection(), $categories);
        $locations = $this->directoryLocations($viewType);

        return view('businesses.index', [
            'businesses' => $businesses,
            'categories' => $categories,
            'viewType' => $viewType,
            'availableProvinces' => $this->uniqueNames($locations->pluck('province'))->sort(),
            'directoryState' => $this->directoryState($request, $viewType, $locations),
        ]);
    }

    public function create(): View
    {
        return view('businesses.form', [
            'business' => null,
            'categories' => Category::all(),
            'users' => User::nonAdmins()->orderBy('name')->get(),
            'provinces' => Province::orderBy('name')->get(),
            'selectedProvinceId' => null,
            'availableCities' => collect(),
            'selectedCityId' => null,
            'memberIds' => [],
            'cancelUrl' => $this->venturesHomeUrl(),
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

        $canManage = auth()->check() && $business->canBeManagedBy(auth()->user());

        abort_unless($business->isPublic() || $canManage, 404);

        return view('businesses.show', [
            'business' => $business,
            'canManageBusiness' => $canManage,
            'showReviewNotice' => $canManage && ! $business->isPublic(),
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
        $users = User::nonAdmins()->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        $selectedProvinceId = $this->selectedProvinceId($business->province);
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
            'selectedCityId' => $this->selectedCityId($availableCities, $business->city),
            'memberIds' => $business->members()->pluck('users.id')->toArray(),
            'cancelUrl' => $this->venturesHomeUrl(),
        ]);
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

        $resubmission = $business->resubmissionBy($request->user());

        $business->fill(array_merge($this->resolveRegionNames($data), $resubmission));
        $business->save();

        $this->syncMembers($request, $business);

        return redirect()->route('businesses.show', $business)->with('success', $resubmission
            ? 'Business updated and sent to the admin team for another review.'
            : 'Business updated successfully!');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $this->authorize('delete', $business);

        $business->deleteWithOfferings();

        return redirect()->to($this->venturesHomeUrl())->with('success', "\"{$business->name}\" was deleted.");
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
        $isAdmin = $this->currentUserIsAdmin();

        // --- A student registers their own business and it waits for approval; an admin registers it for the student picked as owner, approved ---
        $data = $request->businessData();
        $data['user_id'] = $isAdmin ? $data['user_id'] : Auth::id();
        $data['type'] = 'entrepreneur';
        $data['is_visible'] = $isAdmin;
        $data['approval_status'] = $isAdmin ? 'approved' : 'pending';

        if ($request->hasFile('logo')) {
            $data['logo_url'] = $this->storeLogo($request->file('logo'));
        }

        $business = Business::create($this->resolveRegionNames($data));

        $this->syncMembers($request, $business);

        if ($isAdmin) {
            return redirect()->route('businesses.show', $business)->with('success', 'Business created successfully!');
        }

        return redirect()->route('businesses.my')->with('success', 'Business submitted for approval! An admin will review it soon.');
    }

    // --- Form helpers ---

    private function storeLogo(UploadedFile $file): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($file);

        return app(UploadStorage::class)->store($file, 'logos');
    }

    /**
     * Only admins choose who runs a business with its owner; an owner cannot add or remove people through the form.
     */
    private function syncMembers(Request $request, Business $business): void
    {
        if ($this->currentUserIsAdmin() && $request->has('owner_ids')) {
            $business->members()->sync((array) $request->input('owner_ids'));
        }
    }

    // --- Directory list: visible ventures of the chosen type, narrowed by the search box and the filters ---

    private function directoryQuery(Request $request, string $viewType): Builder
    {
        $query = $viewType === 'intrapreneur'
            ? Company::visible()->with(['user'])
            : Business::visible()->with(['user', 'members', 'products'])->entrepreneur();

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

    // --- Directory filters: one query for every distinct (province, city) of the visible ventures; names compare as MySQL did (case and trailing spaces ignored, the first spelling kept) ---

    private function directoryLocations(string $viewType): SupportCollection
    {
        $query = $viewType === 'intrapreneur' ? Company::visible() : Business::visible();

        return $query->where(fn (Builder $located) => $located->whereNotNull('province')->orWhereNotNull('city'))
            ->select('province', 'city')
            ->distinct()
            ->toBase()
            ->get();
    }

    private function uniqueNames(SupportCollection $names): SupportCollection
    {
        return $names->filter(fn ($name) => $name !== null)->unique(fn (string $name) => self::sameName($name));
    }

    private static function sameName(?string $name): string
    {
        return mb_strtolower(rtrim((string) $name));
    }

    /**
     * Initial state of the directory's Alpine component (tabs, province → city dropdown).
     */
    private function directoryState(Request $request, string $viewType, SupportCollection $locations): array
    {
        $selectedProvince = $request->get('province');
        $cityRows = $selectedProvince
            ? $locations->filter(fn ($row) => self::sameName($row->province) === self::sameName($selectedProvince))
            : $locations;

        return [
            'viewType' => $viewType,
            'isPending' => $request->get('status') === 'pending',
            'provinceCityMap' => $locations
                ->filter(fn ($row) => $row->province !== null && $row->city !== null)
                ->groupBy('province')
                ->map(fn ($items) => $items->pluck('city')->sort()->values())
                ->toArray(),
            'selectedProvince' => (string) $selectedProvince,
            'selectedCity' => (string) $request->get('city'),
            'availableCities' => $this->uniqueNames($cityRows->pluck('city'))->sort()->values(),
        ];
    }
}
