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
 * Class ProductController
 *
 * Handles creation, storage, showing, editing, updating, and deletion of products associated with businesses.
 */
class ProductController extends Controller
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
     * Show the form for creating a new product.
     */
    public function create(Business $business): RedirectResponse|View
    {
        $this->authorizeBusinessAccess($business);

        // Prevent creating products if NOT in product mode
        if (! $business->isProductMode()) {
            return redirect()
                ->route('businesses.show', $business)
                ->withErrors(['business_mode' => 'This business is not in Product mode.']);
        }

        return view('products.create', compact('business'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, Business $business): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_type' => 'required|in:fixed,negotiable,customize,unspecified',
            'price' => 'required_unless:price_type,unspecified,customize|nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        $validated['business_id'] = $business->id;
        $validated['type'] = 'product';

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            /** @var \Illuminate\Http\UploadedFile $photoFile */
            $photoFile = $request->file('photo');
            $path = $photoFile->store('products', 'public');
            $validated['photo_url'] = $path;
        }

        $product = Product::create($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The product '{$product->name}' has been added.");
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Business $business, Product $product): View
    {
        $this->authorizeBusinessAccess($business);

        if ($product->business_id !== $business->id || $product->type !== 'product') {
            abort(404);
        }

        return view('products.edit', compact('business', 'product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Business $business, Product $product): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        if ($product->business_id !== $business->id || $product->type !== 'product') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_type' => 'required|in:fixed,negotiable,customize,unspecified',
            'price' => 'required_unless:price_type,unspecified,customize|nullable|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($product->getRawOriginal('photo_url')) {
                Product::deleteCloudinaryImage($product->getRawOriginal('photo_url'));
            }
            /** @var \Illuminate\Http\UploadedFile $photoFile */
            $photoFile = $request->file('photo');
            $path = $photoFile->store('products', 'public');
            $validated['photo_url'] = $path;
        }

        $product->fill($validated);
        $product->save();

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The product '{$product->name}' has been updated.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Business $business, Product $product): RedirectResponse
    {
        $this->authorizeBusinessAccess($business);

        if ($product->business_id !== $business->id || $product->type !== 'product') {
            abort(404);
        }

        // Delete photo if it exists
        if ($product->getRawOriginal('photo_url')) {
            Product::deleteCloudinaryImage($product->getRawOriginal('photo_url'));
        }

        $product->delete();

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The product '{$product->name}' has been removed.");
    }
}
