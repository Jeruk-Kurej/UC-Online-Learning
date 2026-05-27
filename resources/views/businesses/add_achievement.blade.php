<x-app-layout>
    @push('styles')
        <style>
            /* Custom styling if any */
        </style>
    @endpush
    <div class="max-w-5xl mx-auto">
        {{-- Page Header - Elegant Design --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('intrapreneurs.show', $company) }}" 
               class="group inline-flex items-center justify-center sm:justify-start gap-2.5 px-4 py-2.5 bg-white hover:bg-gray-900 border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-white rounded-xl font-medium text-sm shadow-sm hover:shadow-md transition-all duration-200 mb-4 sm:mb-0">
                <i class="bi bi-arrow-left text-base group-hover:-translate-x-0.5 transition-transform duration-200"></i>
                <span>Back</span>
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-soft-gray-900 tracking-tight">Add New Achievement</h1>
                <p class="text-sm text-soft-gray-600 mt-1">{{ $company->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('intrapreneurs.add_achievement', $company) }}" class="space-y-6">
                    @csrf

                    {{-- Achievement Textarea --}}
                    <div>
                        <label for="achievement" class="block text-sm font-medium text-gray-700 mb-2">
                            Achievement Details <span class="text-red-500">*</span>
                        </label>
                        <textarea name="achievement" 
                                  id="achievement" 
                                  rows="4" 
                                  required
                                  class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm @error('achievement') border-red-500 @enderror"
                                  placeholder="e.g. Winner of National Business Plan Competition 2026...">{{ old('achievement') }}</textarea>
                        @error('achievement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-between pt-6 border-t-2 border-soft-gray-100">
                        <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-neutral px-8 py-3.5">
                            Cancel
                        </a>
                        <button type="submit" class="btn-uco btn-uco-primary px-8 py-3.5">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                            <span>Save Achievement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
