<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\Province;
use App\Models\Regency;
use App\Services\ImageOptimizerService;
use App\Services\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Work profiles (intrapreneurs): public page and owner create/edit.
 */
class CompanyController extends Controller
{
    use Concerns\ResolvesRegionNames;

    public function show(Company $company): View
    {
        $company->load(['user', 'category']);

        $isOwner = auth()->id() === $company->user_id;
        $isCollabPartner = auth()->check() && auth()->user()->collabStatusWith($company->user) === 'accepted';
        $isAdmin = auth()->user()?->isAdmin();

        $isUserVisible = $company->user?->is_visible ?? true;
        $isCompanyVisible = $company->is_visible && $isUserVisible;

        if (! $isCompanyVisible && ! $isOwner && ! $isCollabPartner && ! $isAdmin) {
            abort(404);
        }

        return view('work-profiles.show', [
            'company' => $company,
            'student' => $company->user,
            'canManage' => auth()->check() && $company->canBeManagedBy(auth()->user()),
        ]);
    }

    public function create(): View
    {
        $categories = Category::all();
        $provinces = Province::orderBy('name')->get();

        return view('work-profiles.create', [
            'company' => null,
            'categories' => $categories,
            'provinces' => $provinces,
            'selectedProvinceId' => null,
            'availableCities' => collect(),
        ]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['is_visible'] = true;
        $data['approval_status'] = Auth::user()->isAdmin() ? 'approved' : 'pending';

        $logo = $request->file('logo_url');
        if ($logo instanceof UploadedFile) {
            $data['logo_url'] = $this->storeLogo($logo, $validated['name']);
        }

        $company = Company::create($this->resolveRegionNames($data, titleCase: true));

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Work profile registered successfully!');
    }

    public function edit(Company $company): View
    {
        abort_unless($company->canBeManagedBy(Auth::user()), 403);

        $categories = Category::all();
        $provinces = Province::orderBy('name')->get();

        $selectedProvinceId = Province::where('name', $company->province)->first()?->id;
        $availableCities = $selectedProvinceId
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        return view('work-profiles.edit', compact('company', 'categories', 'provinces', 'selectedProvinceId', 'availableCities'));
    }

    public function update(Company $company, CompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = $validated;

        if ($request->boolean('delete_logo')) {
            $company->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        if ($request->hasFile('logo_url')) {
            $company->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $logo = $request->file('logo_url');
            if ($logo instanceof UploadedFile) {
                $data['logo_url'] = $this->storeLogo($logo, $validated['name']);
            }
        }

        if ($validated['name'] !== $company->name) {
            $data['slug'] = $company->freshSlugFor($validated['name']);
        }

        $company->update($this->resolveRegionNames($data, titleCase: true));

        return redirect()->route('intrapreneurs.show', $company)->with('success', 'Work profile updated successfully!');
    }

    // --- Form helpers ---

    private function storeLogo(UploadedFile $file, string $name): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($file);

        return app(UploadStorage::class)->store($file, 'company-logos', Str::slug($name).'_'.time().'.'.$file->getClientOriginalExtension());
    }
}
