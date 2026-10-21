<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Services\ImageOptimizerService;
use App\Services\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

/**
 * Shared create, edit and delete flow for the things a business offers.
 * Products and services live in the same table, told apart by `type`.
 */
abstract class OfferingController extends Controller
{
    // --- Type settings ---

    abstract protected function type(): string;

    abstract protected function offersType(Business $business): bool;

    protected function label(): string
    {
        return $this->type();
    }

    protected function folder(): string
    {
        return $this->type().'s';
    }

    protected function photoRequiredOnCreate(): bool
    {
        return false;
    }

    // --- Actions ---

    public function create(Business $business): RedirectResponse|View
    {
        $this->authorizeBusiness($business);

        if (! $this->offersType($business)) {
            return redirect()
                ->route('businesses.show', $business)
                ->withErrors(['business_mode' => 'This business is not in '.ucfirst($this->type()).' mode.']);
        }

        return view('offerings.form', ['business' => $business, 'type' => $this->type(), 'offering' => null, 'photoRequired' => $this->photoRequiredOnCreate()]);
    }

    public function store(Request $request, Business $business): RedirectResponse
    {
        $this->authorizeBusiness($business);

        $validated = $request->validate($this->rules($this->photoRequiredOnCreate()));
        $validated['business_id'] = $business->id;
        $validated['type'] = $this->type();

        if ($request->hasFile('photo')) {
            $validated['photo_url'] = $this->storePhoto($request->file('photo'));
        }

        $offering = Product::create($validated);

        return $this->backToBusiness($business, $offering, 'added');
    }

    public function edit(Business $business): View
    {
        $offering = $this->authorizeOffering($business);

        return view('offerings.form', ['business' => $business, 'type' => $this->type(), 'offering' => $offering, 'photoRequired' => false]);
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $offering = $this->authorizeOffering($business);

        $validated = $request->validate($this->rules(false));

        if ($request->hasFile('photo')) {
            $this->deletePhoto($offering);
            $validated['photo_url'] = $this->storePhoto($request->file('photo'));
        } elseif ($request->boolean('delete_photo') && ! $this->photoRequiredOnCreate()) {
            $this->deletePhoto($offering);
            $validated['photo_url'] = null;
        }

        $offering->fill($validated);
        $offering->save();

        return $this->backToBusiness($business, $offering, 'updated');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $offering = $this->authorizeOffering($business);

        $this->deletePhoto($offering);
        $offering->delete();

        return $this->backToBusiness($business, $offering, 'removed');
    }

    // --- Guards ---

    private function authorizeBusiness(Business $business): void
    {
        if (! $business->canBeManagedBy(request()->user())) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function authorizeOffering(Business $business): Product
    {
        $offering = Product::findOrFail(request()->route($this->type()));

        $this->authorizeBusiness($business);

        if ($offering->business_id !== $business->id || $offering->type !== $this->type()) {
            abort(404);
        }

        return $offering;
    }

    // --- Helpers ---

    private function rules(bool $photoRequired): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_type' => 'required|in:fixed,negotiable,customize,unspecified',
            'price' => 'required_unless:price_type,unspecified,customize|nullable|numeric|min:0',
            'photo' => ($photoRequired ? 'required' : 'nullable').'|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'photo_caption' => 'nullable|string|max:255',
        ];
    }

    private function storePhoto(UploadedFile $photo): string
    {
        app(ImageOptimizerService::class)->optimizeUploadedFile($photo);

        return app(UploadStorage::class)->store($photo, $this->folder());
    }

    private function deletePhoto(Product $offering): void
    {
        if ($offering->getRawOriginal('photo_url')) {
            $offering->deleteFileFromStorage($offering->getRawOriginal('photo_url'));
        }
    }

    private function backToBusiness(Business $business, Product $offering, string $verb): RedirectResponse
    {
        return redirect()
            ->route('businesses.show', $business)
            ->with('success', "Success! The {$this->label()} '{$offering->name}' has been {$verb}.");
    }
}
