<x-app-layout>
    @push('styles')
        <style>
            /* Custom styling if any */
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
                <h1 class="text-3xl font-bold text-soft-gray-900 tracking-tight">Add New Product</h1>
                <p class="text-sm text-soft-gray-600 mt-1">{{ $business->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('businesses.products.store', $business) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- Product Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('name') border-red-500 @enderror"
                               placeholder="e.g., Nasi Goreng Spesial">
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
                                  placeholder="Describe your product...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Photo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product Photo <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <x-image-preview 
                            input-id="photo" 
                            preview-id="product-photo"
                            multiple="false"
                            height="h-48"
                            placeholder="Click or drag photo here"
                            hint="Select one image — Max 10MB"
                        />
                        <input type="file" name="photo" id="photo" accept="image/*" class="sr-only">
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
                                   value="{{ old('price') }}"
                                   min="0"
                                   step="any"
                                   required
                                   class="block w-full pl-10 rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('price') border-red-500 @enderror"
                                   placeholder="15000">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Buttons - Elegant Design --}}
                    <div class="flex items-center justify-between pt-6 border-t-2 border-soft-gray-100">
                        <a href="{{ route('businesses.show', $business) }}" class="btn-uco btn-uco-neutral">
                            Cancel
                        </a>
                        <button type="submit" class="btn-uco btn-uco-primary">
                            <i class="bi bi-check-lg"></i>
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof ucoInitImagePreview === 'function') {
                ucoInitImagePreview('photo', 'product-photo', 1, false);
            }
        });
    </script>
    @endpush
</x-app-layout>