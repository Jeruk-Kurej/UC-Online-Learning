<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Get authenticated user as User instance
     */
    private function getAuthUser(): User
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }
        
        return $user;
    }

    /**
     * Check if user can manage business
     */
    private function authorizeBusinessAccess(Business $business): void
    {
        $user = $this->getAuthUser();
        
        if (!$business->canBeManagedBy($user)) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(Business $business)
    {
        $this->authorizeBusinessAccess($business);

        // Prevent creating services if NOT in service mode
        if (!$business->isServiceMode()) {
            return redirect()
                ->route('businesses.show', $business)
                ->withErrors(['business_mode' => 'This business is not in Service mode.']);
        }

        return view('services.create', compact('business'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request, Business $business)
    {
        $this->authorizeBusinessAccess($business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,starting_from',
        ]);

        $validated['business_id'] = $business->id;
        $validated['type'] = 'service';

        $service = Product::create($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been added.");
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Business $business, Product $service)
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
    public function update(Request $request, Business $business, Product $service)
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
        ]);

        $service->update($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been updated.");
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Business $business, Product $service)
    {
        $this->authorizeBusinessAccess($business);

        if ($service->business_id !== $business->id || $service->type !== 'service') {
            abort(404);
        }

        $service->delete();

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The service '{$service->name}' has been removed.");
    }
}
