<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
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
     * Show the form for creating a new product.
     */
    public function create(Business $business)
    {
        $this->authorizeBusinessAccess($business);

        // Prevent creating products if NOT in product mode
        if (!$business->isProductMode()) {
            return redirect()
                ->route('businesses.show', $business)
                ->withErrors(['business_mode' => 'This business is not in Product mode.']);
        }

        return view('products.create', compact('business'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, Business $business)
    {
        $this->authorizeBusinessAccess($business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        $validated['business_id'] = $business->id;
        $validated['type'] = 'product';

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo_url'] = $path;
        }

        $product = Product::create($validated);

        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The product '{$product->name}' has been added.");
    }

    /**
     * Display the specified product.
     */
    public function show(Business $business, Product $product)
    {
        if ($product->business_id !== $business->id || $product->type !== 'product') {
            abort(404);
        }

        return view('products.show', compact('business', 'product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Business $business, Product $product)
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
    public function update(Request $request, Business $business, Product $product)
    {
        $this->authorizeBusinessAccess($business);

        if ($product->business_id !== $business->id || $product->type !== 'product') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ]);

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($product->getRawOriginal('photo_url')) {
                Product::deleteCloudinaryImage($product->getRawOriginal('photo_url'));
            }
            $path = $request->file('photo')->store('products', 'public');
            $validated['photo_url'] = $path;
        }

        $product->update($validated);

        return redirect()
            ->route('businesses.products.show', [$business, $product])
            ->with('success', "Success! The product '{$product->name}' has been updated.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Business $business, Product $product)
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
