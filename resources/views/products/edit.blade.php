<x-app-layout>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
        <style>
            .ts-wrapper {
                width: 100% !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .ts-wrapper .ts-control {
                border: 1px solid #e2e8f0 !important;
                border-radius: 0.75rem !important;
                padding: 10px 16px !important; 
                min-height: 42px !important;
                width: 100% !important;
                box-sizing: border-box !important;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                background: white !important;
                display: flex !important;
                align-items: center !important;
            }

            .ts-wrapper.focus .ts-control {
                border-color: #111827 !important; /* Soft Gray 900 */
                box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.05) !important;
                ring: none !important;
            }

            .ts-dropdown {
                background-color: white !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 1rem !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                margin-top: 6px !important;
                padding: 6px !important;
                z-index: 1000 !important;
            }

            .ts-dropdown .option {
                padding: 8px 12px !important;
                font-size: 13px !important;
                color: #475569 !important;
                border-radius: 0.75rem !important;
                margin-bottom: 2px !important;
                transition: all 0.15s ease !important;
            }

            .ts-dropdown .option.active {
                background-color: #fff7ed !important;
                color: #f97316 !important;
                font-weight: 600 !important;
            }

            .ts-wrapper .ts-control>input {
                font-size: 14px !important;
            }
        </style>
    @endpush
    <div class="max-w-5xl mx-auto">
        {{-- Page Header - Elegant Design --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-secondary mb-4 sm:mb-0">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-soft-gray-900 tracking-tight">Edit Product</h1>
                <p class="text-sm text-soft-gray-600 mt-1">{{ $product->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('businesses.products.update', [$business, $product]) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Product Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $product->name) }}"
                               required
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('name') border-red-500 @enderror">
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
                                  class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price Type --}}
                    @php
                        $selectedPriceType = old('price_type', $product->price_type);
                        if (($product->price === null || trim($product->price) === '') && !in_array($selectedPriceType, ['unspecified', 'customize'])) {
                            $selectedPriceType = 'unspecified';
                        }
                    @endphp
                    <div>
                        <label for="price_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Price Type <span class="text-red-500">*</span>
                        </label>
                        <select name="price_type" 
                                id="price_type" 
                                required
                                class="block w-full">
                            <option value="fixed" {{ $selectedPriceType === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                            <option value="negotiable" {{ $selectedPriceType === 'negotiable' ? 'selected' : '' }}>Negotiable</option>
                            <option value="customize" {{ $selectedPriceType === 'customize' ? 'selected' : '' }}>Customize by Order</option>
                            <option value="unspecified" {{ $selectedPriceType === 'unspecified' ? 'selected' : '' }}>Unspecified Price</option>
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
                                   value="{{ old('price', $product->price) }}"
                                   min="0"
                                   step="any"
                                   required
                                   class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Photo Management --}}
                    <div x-data="{ 
                        previewUrl: '{{ $product->photo_url ?? '' }}',
                        hasPhoto: {{ $product->photo_url ? 'true' : 'false' }},
                        handleFileChange(event) {
                            const file = event.target.files[0];
                            if (file) {
                                this.previewUrl = URL.createObjectURL(file);
                                this.hasPhoto = true;
                            }
                        },
                        triggerUpload() {
                            document.getElementById('photo').click();
                        }
                    }" class="space-y-4 pt-6 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700">
                            Product Photo <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>

                        {{-- Interactive Hover-to-Change Photo Container --}}
                        <div class="relative w-48 h-48 rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 group cursor-pointer"
                             @click="triggerUpload()">
                            
                            {{-- Display current photo or preview --}}
                            <template x-if="hasPhoto">
                                <div class="absolute inset-0 w-full h-full bg-slate-950/5 flex items-center justify-center">
                                    <img :src="previewUrl" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none transition-transform duration-500 group-hover:scale-105" aria-hidden="true" referrerpolicy="no-referrer">
                                    <img :src="previewUrl" class="relative z-10 max-w-full max-h-full object-contain" alt="Product Photo" referrerpolicy="no-referrer">
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
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white text-center p-4">
                                <i class="bi bi-camera text-2xl mb-1.5"></i>
                                <span class="text-xs font-extrabold uppercase tracking-wider" x-text="hasPhoto ? 'Change Photo' : 'Upload Photo'"></span>
                                <span class="text-[9px] text-white/70 mt-1">Max 10MB</span>
                            </div>
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
                               value="{{ old('photo_caption', $product->photo_caption) }}"
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('photo_caption') border-red-500 @enderror"
                               placeholder="Short description of the photo...">
                        @error('photo_caption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Buttons - Elegant Design --}}
                    <div class="flex items-center justify-between pt-6 border-t-2 border-soft-gray-100">
                        <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-neutral">
                            Cancel
                        </a>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick="deleteProduct()"
                                    class="btn-uco btn-uco-danger">
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>

                            <button type="submit" 
                                    class="btn-uco btn-uco-primary">
                                <i class="bi bi-check-lg"></i>
                                Update Product
                            </button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('businesses.products.destroy', [$business, $product]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    function deleteProduct() {
        if(confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const priceTypeSelect = document.getElementById("price_type");
        const priceInput = document.getElementById("price");
        const star = document.getElementById("price-required-star");

        const handlePriceTypeChange = (value) => {
            if (value === 'unspecified' || value === 'customize') {
                priceInput.value = '';
                priceInput.disabled = true;
                priceInput.required = false;
                priceInput.placeholder = value === 'unspecified' ? 'Price not specified' : 'Customized by order';
                if (star) star.style.display = 'none';
            } else {
                priceInput.disabled = false;
                priceInput.required = true;
                priceInput.placeholder = '15000';
                if (star) star.style.display = 'inline';
            }
        };

        if (priceTypeSelect && window.TomSelect) {
            const ts = new TomSelect(priceTypeSelect, {
                create: false,
                placeholder: "-- Select Price Type --",
                searchField: ["text"],
            });

            ts.on('change', (val) => {
                handlePriceTypeChange(val);
            });

            // Run initially
            handlePriceTypeChange(priceTypeSelect.value);
        } else if (priceTypeSelect) {
            priceTypeSelect.addEventListener('change', (e) => {
                handlePriceTypeChange(e.target.value);
            });
            handlePriceTypeChange(priceTypeSelect.value);
        }
    });
    </script>
    @endpush
</x-app-layout>