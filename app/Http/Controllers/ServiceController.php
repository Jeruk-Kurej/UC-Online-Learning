<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class ServiceController
 *
 * Handles creation, storage, editing, updating, and deletion of services associated with businesses.
 */
class ServiceController extends Controller
{
    /**
     * Get authenticated user as User instance.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If unauthenticated
     */
    private function getAuthUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        return $user;
    }

    /**
     * Check if user can manage business.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If unauthorized
     */
    private function authorizeBusinessAccess(Business $business): void
    {
        $user = $this->getAuthUser();

        if (! $business->canBeManagedBy($user)) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(Business $business): RedirectResponse|View
    {
        $this->authorizeBusinessAccess($business);

        // Prevent creating services if NOT in service mode
        if (! $business->isServiceMode()) {
            return redirect()
                ->route('businesses.show', $business)
                ->withErrors(['business_mode' => 'This business is not in Service mode.']);
        }

        return view('services.create', compact('business'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request, Business $business): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,starting_from',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        $validated['business_id'] = $business->id;
        $validated['type'] = 'service';

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            /** @var \Illuminate\Http\UploadedFile $photoFile */
            $photoFile = $request->file('photo');
            $path = $photoFile->store('services', 'public');
            $validated['photo_url'] = $path;
        }

        $service = Product::create($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been added.");
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Business $business, Product $service): View
    {
        $this->authorizeBusinessAccess($business);

        if ($service->business_id !== $business->id || $service->type !== 'service') {
            abort(404);
        }

        return view('services.edit', compact('business', 'service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Business $business, Product $service): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        if ($service->business_id !== $business->id || $service->type !== 'service') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,starting_from',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        // Handle Photo Upload (delete old one if it exists)
        if ($request->hasFile('photo')) {
            if ($service->getRawOriginal('photo_url')) {
                Product::deleteCloudinaryImage($service->getRawOriginal('photo_url'));
            }
            /** @var \Illuminate\Http\UploadedFile $photoFile */
            $photoFile = $request->file('photo');
            $path = $photoFile->store('services', 'public');
            $validated['photo_url'] = $path;
        }

        $service->fill($validated);
        $service->save();

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been updated.");
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Business $business, Product $service): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        if ($service->business_id !== $business->id || $service->type !== 'service') {
            abort(404);
        }

        // Delete photo if it exists
        if ($service->getRawOriginal('photo_url')) {
            Product::deleteCloudinaryImage($service->getRawOriginal('photo_url'));
        }

        $service->delete();

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been removed.");
    }
}
