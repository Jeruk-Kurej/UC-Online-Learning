<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/offering-form.css')
    @endpush
    <div class="max-w-5xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Add New Service</h1>
                <p class="text-sm text-gray-600">{{ $business->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form data-offering-form method="POST" action="{{ route('businesses.services.store', $business) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    {{-- Service Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('name') border-red-500 @enderror"
                               placeholder="e.g., Web Development, Haircut, Delivery Service">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4" 
                                  required
                                  class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('description') border-red-500 @enderror"
                                  placeholder="Describe your service in detail...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price Type --}}
                    <div>
                        <label for="price_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Price Type <span class="text-red-500">*</span>
                        </label>
                        <select name="price_type" 
                                id="price_type" 
                                required
                                class="block w-full">
                            <option value="">-- Select Price Type --</option>
                            <option value="fixed" {{ old('price_type') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                            <option value="negotiable" {{ old('price_type') === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                            <option value="customize" {{ old('price_type') === 'customize' ? 'selected' : '' }}>Customize by Order</option>
                            <option value="unspecified" {{ old('price_type') === 'unspecified' ? 'selected' : '' }}>Unspecified Price</option>
                        </select>
                        @error('price_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (Rp) <span class="text-red-500" id="price-required-star">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" 
                                   name="price" 
                                   id="price" 
                                   data-fixed-placeholder="50000"
                                   value="{{ old('price') }}"
                                   min="0"
                                   step="any"
                                   required
                                   class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('price') border-red-500 @enderror"
                                   placeholder="50000">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Service Photo --}}
                    <div x-data="photoPicker()" class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Service Photo <span class="text-red-500">*</span>
                        </label>
                        
                        {{-- Interactive Upload Box --}}
                        <div class="relative w-48 h-48 rounded-sm overflow-hidden border border-gray-200 hover:border-orange-300 group cursor-pointer transition-colors"
                             @click="triggerUpload()">
                            
                            {{-- Display preview if uploaded --}}
                            <template x-if="hasPhoto">
                                <div class="w-full h-full relative overflow-hidden flex items-center justify-center">
                                    <img :src="previewUrl" alt="Preview Service Photo" class="relative z-10 max-w-full max-h-full object-contain rounded-sm" referrerpolicy="no-referrer">
                                </div>
                            </template>

                            {{-- Display placeholder if no photo --}}
                            <template x-if="!hasPhoto">
                                <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center text-gray-400">
                                    <i class="bi bi-cloud-arrow-up text-3xl mb-2 text-gray-300"></i>
                                    <p class="text-[11px] font-bold uppercase tracking-wider">Upload Photo</p>
                                    <p class="text-[9px] text-gray-400 mt-1">Click to choose image</p>
                                </div>
                            </template>

                            {{-- Hover Overlay --}}
                            <template x-if="hasPhoto">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white text-center p-4">
                                    <i class="bi bi-camera text-2xl mb-1.5"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wider">Change Photo</span>
                                    <span class="text-[9px] text-white/70 mt-1">Max 10MB</span>
                                </div>
                            </template>
                        </div>

                        {{-- Hidden File Input --}}
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

                    {{-- Photo Caption --}}
                    <div>
                        <label for="photo_caption" class="block text-sm font-medium text-gray-700 mb-2">
                            Photo Caption <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input type="text" 
                               name="photo_caption" 
                               id="photo_caption" 
                               value="{{ old('photo_caption') }}"
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('photo_caption') border-red-500 @enderror"
                               placeholder="Short description of the photo...">
                        @error('photo_caption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-neutral">
                            Cancel
                        </a>
                        <button type="submit" class="btn-uco btn-uco-primary">
                            <i class="bi bi-check-lg"></i>
                            Create Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>