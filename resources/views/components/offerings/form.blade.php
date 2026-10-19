<div class="mx-auto max-w-3xl space-y-6">
    <x-ui.page-header :title="$isEdit ? 'Edit ' . strtolower($noun) : 'Add a ' . strtolower($noun)" :subtitle="$isEdit ? $offering->name : $business->name" />

    <form data-offering-form method="POST" action="{{ $isEdit ? route('businesses.' . $type . 's.update', [$business, $offering]) : route('businesses.' . $type . 's.store', $business) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <x-forms.section :title="$noun . ' details'" :grid="false">
            <x-forms.input name="name" :label="$noun . ' name'" :value="$offering?->name" required :placeholder="$isEdit ? null : $example['name']" />

            <x-forms.field name="description" label="Description" required>
                <textarea name="description" id="description" rows="4" required class="field-input @error('description') !border-red-700 @enderror" @unless ($isEdit) placeholder="{{ $example['description'] }}" @endunless>{{ old('description', $offering?->description) }}</textarea>
            </x-forms.field>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-forms.field name="price_type" label="How is it priced?" required>
                    <select name="price_type" id="price_type" required class="field-input">
                        @unless ($isEdit)
                            <option value="">Choose one</option>
                        @endunless
                        <option value="fixed" {{ $selectedPriceType === 'fixed' ? 'selected' : '' }}>Fixed price</option>
                        <option value="negotiable" {{ $selectedPriceType === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                        <option value="customize" {{ $selectedPriceType === 'customize' ? 'selected' : '' }}>Depends on the order</option>
                        <option value="unspecified" {{ $selectedPriceType === 'unspecified' ? 'selected' : '' }}>No price shown</option>
                    </select>
                </x-forms.field>

                <x-forms.field name="price" label="Price (Rp)">
                    <input type="number" name="price" id="price" data-fixed-placeholder="{{ $example['price'] }}" value="{{ old('price', $offering?->price) }}" min="0" step="any" required class="field-input @error('price') !border-red-700 @enderror" @unless ($isEdit) placeholder="{{ $example['price'] }}" @endunless>
                </x-forms.field>
            </div>
        </x-forms.section>

        <x-forms.section :title="$noun . ' photo'" :grid="false">
            <div x-data="photoPicker(@js($offering?->photo_url))" class="space-y-4">
                <div class="flex items-center gap-4">
                    <span class="photo-frame h-32 w-32">
                        <i x-show="!hasPhoto" class="bi bi-image text-3xl text-gray-500" aria-hidden="true"></i>
                        <img x-show="hasPhoto" :src="previewUrl" alt="" class="absolute inset-0 h-full w-full bg-white object-contain" referrerpolicy="no-referrer">
                    </span>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">
                            {{ $photoRequired ? 'Required.' : 'Optional.' }} JPG, PNG or WEBP, up to 10 MB.
                        </p>
                        <button type="button" @click="triggerUpload()" class="btn btn-secondary btn-sm" x-text="hasPhoto ? 'Change photo' : 'Choose a photo'"></button>
                    </div>
                </div>
                <input type="file" name="photo" id="photo" accept="image/*" class="sr-only" @change="handleFileChange($event)">
                @error('photo')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <x-forms.input name="photo_caption" label="Photo caption (optional)" :value="$offering?->photo_caption" placeholder="A short description of the photo" />
        </x-forms.section>

        <x-forms.actions>
            <a href="{{ route('businesses.show', $business) }}" class="btn btn-secondary">Cancel</a>
            @if ($isEdit)
                <button type="button" data-confirm-submit="delete-form" data-confirm-message="Delete this {{ $type }}? This cannot be undone." class="btn btn-danger">Delete {{ $type }}</button>
            @endif
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Save changes' : 'Add ' . $type }}</button>
        </x-forms.actions>
    </form>

    @if ($isEdit)
        <form id="delete-form" action="{{ route('businesses.' . $type . 's.destroy', [$business, $offering]) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
