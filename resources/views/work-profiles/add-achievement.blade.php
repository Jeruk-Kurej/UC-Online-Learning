<x-app-layout>
    <div class="mx-auto max-w-5xl">
        {{-- --- Page Header - Elegant Design --- --}}
        <div class="mb-8 flex flex-row items-center gap-3">
            <a href="{{ route('intrapreneurs.show', $company) }}" class="btn btn-secondary flex-shrink-0">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="min-w-0 flex-grow">
                <h1 class="text-xl font-bold leading-snug text-soft-gray-900 sm:text-3xl">Add New Achievement</h1>
                <p class="mt-1 text-xs text-soft-gray-600 sm:text-sm">{{ $company->name }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('intrapreneurs.add_achievement', $company) }}" class="space-y-6">
                    @csrf

                    <x-forms.field look="offering" name="achievement" label="Achievement Details" required>
                        <textarea name="achievement"
                                  id="achievement"
                                  rows="4"
                                  required
                                  class="@error('achievement') border-red-500 @enderror block w-full rounded-xl border-gray-200 shadow-sm focus:border-soft-gray-900 focus:ring-soft-gray-900 sm:text-sm"
                                  placeholder="e.g. Winner of National Business Plan Competition 2026...">{{ old('achievement') }}</textarea>
                    </x-forms.field>

                    {{-- --- Submit Buttons --- --}}
                    <div class="flex items-center justify-between border-t-2 border-soft-gray-100 pt-6">
                        <a href="{{ route('intrapreneurs.show', $company) }}" class="btn btn-secondary px-8 py-3.5">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-8 py-3.5">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                            <span>Save Achievement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
