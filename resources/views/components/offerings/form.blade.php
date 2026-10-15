<div class="mx-auto max-w-5xl">
    {{-- --- Page header --- --}}
    <div class="{{ $look['header'] }}">
        <a href="{{ route('businesses.show', $business) }}" class="{{ $look['back'] }}">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="{{ $look['title'] }}">{{ $isEdit ? 'Edit' : 'Add New' }} {{ $noun }}</h1>
            <p class="{{ $look['subtitle'] }}">{{ $isEdit ? $offering->name : $business->name }}</p>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-xl">
        <div class="p-6">
            <form data-offering-form method="POST" action="{{ $isEdit ? route('businesses.' . $type . 's.update', [$business, $offering]) : route('businesses.' . $type . 's.store', $business) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <x-forms.field look="offering" name="name" :label="$noun . ' Name'" required>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $offering?->name) }}"
                           required
                           class="@error('name') border-red-500 @enderror block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm"
                           @unless ($isEdit) placeholder="{{ $look['namePlaceholder'] }}" @endunless>
                </x-forms.field>

                <x-forms.field look="offering" name="description" label="Description" required>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              required
                              class="@error('description') border-red-500 @enderror block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm"
                              @unless ($isEdit) placeholder="{{ $look['descriptionPlaceholder'] }}" @endunless>{{ old('description', $offering?->description) }}</textarea>
                </x-forms.field>

                <x-forms.field look="offering" name="price_type" label="Price Type" required>
                    <select name="price_type"
                            id="price_type"
                            required
                            class="block w-full">
                        @unless ($isEdit)
                            <option value="">-- Select Price Type --</option>
                        @endunless
                        <option value="fixed" {{ $selectedPriceType === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                        <option value="negotiable" {{ $selectedPriceType === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                        <option value="customize" {{ $selectedPriceType === 'customize' ? 'selected' : '' }}>Customize by Order</option>
                        <option value="unspecified" {{ $selectedPriceType === 'unspecified' ? 'selected' : '' }}>Unspecified Price</option>
                    </select>
                </x-forms.field>

                {{-- --- Price --- --}}
                <div>
                    <label for="price" class="mb-2 block text-sm font-medium text-gray-700">
                        Price (Rp) <span class="text-red-500" id="price-required-star">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number"
                               name="price"
                               id="price"
                               data-fixed-placeholder="{{ $look['pricePlaceholder'] }}"
                               value="{{ old('price', $offering?->price) }}"
                               min="0"
                               step="any"
                               required
                               class="@error('price') border-red-500 @enderror block w-full rounded-xl border-gray-200 pl-10 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm"
                               @unless ($isEdit) placeholder="{{ $look['pricePlaceholder'] }}" @endunless>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- --- Photo --- --}}
                <div x-data="photoPicker(@js($offering?->photo_url))" @class(['space-y-4', 'pt-6 border-t border-gray-100' => $isEdit])>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ $noun }} Photo
                        @if ($photoRequired)
                            <span class="text-red-500">*</span>
                        @else
                            <span class="font-normal text-gray-400">(Optional)</span>
                        @endif
                    </label>

                    <div @class([
                        'relative w-48 h-48 rounded-sm overflow-hidden border border-gray-200 group cursor-pointer',
                        'hover:border-orange-300 transition-colors' => !$isEdit,
                    ])
                         @click="triggerUpload()">

                        <template x-if="hasPhoto">
                            <div class="{{ $look['preview'] }}">
                                <img :src="previewUrl" class="relative z-10 max-h-full max-w-full rounded-sm object-contain" alt="{{ $isEdit ? '' : 'Preview ' }}{{ $noun }} Photo" referrerpolicy="no-referrer">
                            </div>
                        </template>

                        <template x-if="!hasPhoto">
                            <div class="flex h-full w-full flex-col items-center justify-center p-6 text-center text-gray-400">
                                <i class="bi bi-cloud-arrow-up mb-2 text-3xl text-gray-300"></i>
                                <p class="text-[11px] font-bold uppercase tracking-wider">Upload Photo</p>
                                <p class="mt-1 text-[9px] text-gray-400">Click to choose image</p>
                            </div>
                        </template>

                        @if ($isEdit)
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/60 p-4 text-center text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <i class="bi bi-camera mb-1.5 text-2xl"></i>
                                <span class="text-xs font-extrabold uppercase tracking-wider" x-text="hasPhoto ? 'Change Photo' : 'Upload Photo'"></span>
                                <span class="mt-1 text-[9px] text-white/70">Max 10MB</span>
                            </div>
                        @else
                            <template x-if="hasPhoto">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/60 p-4 text-center text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <i class="bi bi-camera mb-1.5 text-2xl"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wider">Change Photo</span>
                                    <span class="mt-1 text-[9px] text-white/70">Max 10MB</span>
                                </div>
                            </template>
                        @endif
                    </div>

                    <input type="file"
                           name="photo"
                           id="photo"
                           accept="image/*"
                           class="sr-only"
                           @change="handleFileChange($event)">

                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- --- Photo caption --- --}}
                <div>
                    <label for="photo_caption" class="mb-2 block text-sm font-medium text-gray-700">
                        Photo Caption <span class="font-normal text-gray-400">(Optional)</span>
                    </label>
                    <input type="text"
                           name="photo_caption"
                           id="photo_caption"
                           value="{{ old('photo_caption', $offering?->photo_caption) }}"
                           class="@error('photo_caption') border-red-500 @enderror block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm"
                           placeholder="Short description of the photo...">
                    @error('photo_caption')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- --- Actions --- --}}
                <div class="{{ $look['actions'] }}">
                    <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-neutral">
                        Cancel
                    </a>
                    @if ($isEdit)
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    data-confirm-submit="delete-form"
                                    data-confirm-message="Are you sure you want to delete this {{ $type }}? This action cannot be undone."
                                    class="btn-uco btn-uco-danger">
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>

                            <button type="submit"
                                    class="btn-uco btn-uco-primary">
                                <i class="bi bi-check-lg"></i>
                                Update {{ $noun }}
                            </button>
                        </div>
                    @else
                        <button type="submit" class="btn-uco btn-uco-primary">
                            <i class="bi bi-check-lg"></i>
                            Create {{ $noun }}
                        </button>
                    @endif
                </div>
            </form>

            @if ($isEdit)
                <form id="delete-form" action="{{ route('businesses.' . $type . 's.destroy', [$business, $offering]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
