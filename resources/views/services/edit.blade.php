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
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Edit Service</h1>
                <p class="text-sm text-gray-600">{{ $service->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('businesses.services.update', [$business, $service]) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Service Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $service->name) }}"
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
                                  class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" 
                                   name="price" 
                                   id="price" 
                                   value="{{ old('price', $service->price) }}"
                                   min="0"
                                   step="any"
                                   required
                                   class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
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
                            <option value="fixed" {{ old('price_type', $service->price_type) === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                            <option value="starting_from" {{ old('price_type', $service->price_type) === 'starting_from' ? 'selected' : '' }}>Starting From</option>
                        </select>
                        @error('price_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Service Photo (Optional) --}}
                    <div x-data="{ preview: null }">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Photo <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>

                        {{-- Current Photo Preview --}}
                        @if($service->getRawOriginal('photo_url'))
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1.5">Current photo:</p>
                                <img src="{{ $service->photo_url }}" class="h-40 rounded-xl object-cover border border-gray-200" alt="Current service photo">
                            </div>
                        @endif

                        <input type="file"
                               name="photo"
                               id="photo"
                               accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-uco-orange-50 file:text-uco-orange-700 hover:file:bg-uco-orange-100 cursor-pointer"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <div x-show="preview" class="mt-3">
                            <p class="text-xs text-gray-500 mb-1.5">New photo preview:</p>
                            <img :src="preview" class="h-40 rounded-xl object-cover border border-gray-200" alt="New photo preview">
                        </div>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-neutral">
                            Cancel
                        </a>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick="deleteService()"
                                    class="btn-uco btn-uco-danger">
                                <i class="bi bi-trash"></i>
                                Delete
                            </button>

                            <button type="submit" 
                                    class="btn-uco btn-uco-primary">
                                <i class="bi bi-check-lg"></i>
                                Update Service
                            </button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('businesses.services.destroy', [$business, $service]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    function deleteService() {
        if(confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize TomSelect for price type
        const priceTypeSelect = document.getElementById("price_type");
        if (priceTypeSelect && window.TomSelect) {
            new TomSelect(priceTypeSelect, {
                create: false,
                placeholder: "-- Select Price Type --",
                searchField: ["text"],
            });
        }
    });
    </script>
    @endpush
</x-app-layout>