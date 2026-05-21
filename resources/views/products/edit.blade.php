<x-app-layout>
    @push('styles')
        <style>
            /* Custom styling if any */
        </style>
    @endpush
    <div class="max-w-5xl mx-auto">
        {{-- Page Header - Elegant Design --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('businesses.products.show', [$business, $product]) }}" 
               class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200 mb-4 sm:mb-0">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
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

                    {{-- Product Photo Management --}}
                    <div class="space-y-4 pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="bi bi-image text-uco-orange-500"></i>
                            Product Photo
                        </h3>

                        {{-- Existing Photo --}}
                        @if($product->photo_url)
                            <div class="w-48 aspect-square rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50">
                                <img src="{{ $product->photo_url }}" 
                                     class="w-full h-full object-cover"
                                     alt="Product Photo">
                            </div>
                        @else
                            <div class="p-8 w-48 text-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                <p class="text-xs text-gray-400">No photo uploaded yet.</p>
                            </div>
                        @endif

                        {{-- Upload New Photo --}}
                        <div class="space-y-2 mt-6">
                            <label class="block text-sm font-bold text-gray-700">Change Photo</label>
                            <x-image-preview 
                                input-id="photo" 
                                preview-id="product-photo-edit"
                                multiple="false"
                                height="h-40"
                                placeholder="Drag or click to choose a new photo"
                                hint="Select an image — Max 10MB"
                            />
                            <input type="file" name="photo" id="photo" accept="image/*" class="sr-only">
                        </div>
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

                    {{-- Submit Buttons - Elegant Design --}}
                    <div class="flex items-center justify-between pt-6 border-t-2 border-soft-gray-100">
                        <a href="{{ route('businesses.show', $business) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-900 rounded-xl transition duration-150">
                            Cancel
                        </a>
                        <div class="flex items-center gap-3">
                            <button type="button" 
                                    onclick="deleteProduct()"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>

                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-soft-gray-900 hover:bg-soft-gray-800 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width.5="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
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
    <script>
    function deleteProduct() {
        if(confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof ucoInitImagePreview === 'function') {
            ucoInitImagePreview('photo', 'product-photo-edit', 1, false);
        }
    });
    </script>
    @endpush
</x-app-layout>