<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Category;
use App\Models\Company;
use App\Models\Province;
use App\Models\Regency;
use App\Models\User;
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

        $canManage = auth()->check() && $company->canBeManagedBy(auth()->user());

        abort_unless($company->isPublic() || $canManage, 404);

        return view('work-profiles.show', [
            'company' => $company,
            'student' => $company->user,
            'canManage' => $canManage,
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
            'selectedCityId' => null,
            'owners' => $this->currentUserIsAdmin() ? User::nonAdmins()->orderBy('name')->get() : collect(),
            'cancelUrl' => $this->venturesHomeUrl(),
        ]);
    }

    public function store(CompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $isAdmin = $this->currentUserIsAdmin();

        // --- A student adds their own work profile and it waits for approval; an admin adds it for the student picked as owner, approved ---
        $data = $validated;
        $data['user_id'] = $isAdmin ? $validated['user_id'] : Auth::id();
        $data['is_visible'] = true;
        $data['approval_status'] = $isAdmin ? 'approved' : 'pending';

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

        $selectedProvinceId = $this->selectedProvinceId($company->province);
        $availableCities = $selectedProvinceId
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        return view('work-profiles.edit', [
            'company' => $company,
            'categories' => $categories,
            'provinces' => $provinces,
            'selectedProvinceId' => $selectedProvinceId,
            'availableCities' => $availableCities,
            'selectedCityId' => $this->selectedCityId($availableCities, $company->city),
        ]);
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

        $resubmission = $company->resubmissionBy($request->user());

        $company->update(array_merge($this->resolveRegionNames($data, titleCase: true), $resubmission));

        return redirect()->route('intrapreneurs.show', $company)->with('success', $resubmission
            ? 'Work profile updated and sent to the admin team for another review.'
            : 'Work profile updated successfully!');
    }

    public function destroy(Company $company): RedirectResponse
    {
        abort_unless($company->canBeManagedBy(Auth::user()), 403);

        $company->delete();

        return redirect()->to($this->venturesHomeUrl())->with('success', "\"{$company->name}\" was deleted.");
    }

    // --- Form helpers ---

    private function storeLogo(UploadedFile $file, string $name): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($file);

        return app(UploadStorage::class)->store($file, 'company-logos', Str::slug($name).'_'.time().'.'.$file->getClientOriginalExtension());
    }
}
