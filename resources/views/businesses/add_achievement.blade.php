<x-app-layout>
    @push('styles')
        <style>
            /* Custom styling if any */
        </style>
    @endpush
    <div class="max-w-5xl mx-auto">
        {{-- Page Header - Elegant Design --}}
        <div class="mb-8 flex flex-row items-center gap-3">
            <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-secondary flex-shrink-0">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-grow min-w-0">
                <h1 class="text-xl sm:text-3xl font-bold text-soft-gray-900 tracking-tight leading-snug">Add New Achievement</h1>
                <p class="text-xs sm:text-sm text-soft-gray-600 mt-1">{{ $company->name }}</p>
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
