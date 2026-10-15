<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Province;
use App\Models\Regency;
use App\Services\ImageOptimizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Work profiles (intrapreneurs): public page and owner create/edit.
 */
class CompanyController extends Controller
{
    /**
     * Display the specified intrapreneur (Company).
     */
    public function show(Company $company): View
    {
        $company->load(['user', 'category']);

        // Security check: Check if visible or authorized
        $isOwner = auth()->id() === $company->user_id;
        $isCollabPartner = auth()->check() && auth()->user()->collabStatusWith($company->user) === 'accepted';
        $isAdmin = auth()->user()?->isAdmin();

        $isUserVisible = $company->user?->is_visible ?? true;
        $isCompanyVisible = $company->is_visible && $isUserVisible;

        if (!$isCompanyVisible && !$isOwner && !$isCollabPartner && !$isAdmin) {
            abort(404);
        }

        return view('businesses.show_intrapreneur', [
            'company' => $company,
            'student' => $company->user,
            'canManage' => auth()->check() && (auth()->user()->id === $company->user_id || auth()->user()->isAdmin()),
        ]);
    }

    /**
     * Show form to create a company work profile (Intrapreneur).
     */
    public function create(): View
    {
        $categories = Category::all();
        $provinces = Province::orderBy('name')->get();
        return view('businesses.create_intrapreneur', [
            'company' => null,
            'categories' => $categories,
            'provinces' => $provinces,
            'selectedProvinceId' => null,
            'availableCities' => collect(),
        ]);
    }

    /**
     * Store a newly created company work profile (Intrapreneur).
     */
    public function store(Request $request): RedirectResponse
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
                app(ImageOptimizerService::class)->optimizeUploadedFile($file);
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
    public function edit(Company $company): View
    {
        if (Auth::id() !== $company->user_id && !$this->currentUserIsAdmin()) {
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
    public function update(Company $company, Request $request): RedirectResponse
    {
        if (Auth::id() !== $company->user_id && !$this->currentUserIsAdmin()) {
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
            $company->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $data['logo_url'] = null;
        }

        // Handle file uploads (Logo)
        if ($request->hasFile('logo_url')) {
            $company->deleteFileFromStorage($company->getRawOriginal('logo_url'));
            $file = $request->file('logo_url');
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                app(ImageOptimizerService::class)->optimizeUploadedFile($file);
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
