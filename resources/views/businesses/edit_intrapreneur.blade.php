<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/work-profile-edit.css')
    @endpush

    {{-- Header Section --}}
    <div class="mb-6 flex items-center gap-4 reveal-on-scroll">
        <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit Work Profile</h1>
            <p class="text-sm text-gray-600 mt-1">Update your professional employment listings in the UCO directory.</p>
        </div>
    </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 sm:p-8 reveal-on-scroll">
            <form method="POST" action="{{ route('intrapreneurs.update', $company) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('businesses.partials.work-profile-fields')

                    {{-- Logo Upload with Live Preview & Remove Option (Tactile Card Layout) --}}
                    <x-forms.removable-image class="md:col-span-2 pb-5" label="Company Logo" name="logo_url" delete-name="delete_logo"
                        :url="$company->logo_url" :has-image="$company->has_logo" :initial="strtoupper(substr($company->name, 0, 1))" noun="Logo"
                        hint="JPG, PNG, WEBP — Max 5MB" box-class="w-[120px] h-[120px] rounded-xl bg-[#f8fafc] border-[1.5px] border-[#e2e8f0] group shadow-sm flex items-center justify-center p-2"
                        actions-class="h-[120px]" image-class="" initials-class="uco-initials-bg">
                        @error('logo_url')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </x-forms.removable-image>

                    {{-- Job Description --}}
                    <div class="md:col-span-2">
                        <label for="job_description" class="form-label-custom">Job Description & Responsibilities <span class="text-red-500">*</span></label>
                        <textarea name="job_description" id="job_description" rows="4" required
                                  class="form-input-custom @error('job_description') border-red-500 @enderror"
                                  placeholder="Describe your job roles, day-to-day operations, and career contributions...">{{ old('job_description', $company->job_description) }}</textarea>
                        @error('job_description')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between border-t border-slate-100 pt-6 mt-8">
                    <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-neutral">
                        Cancel
                    </a>
                    <button type="submit" class="btn-uco btn-uco-primary">
                        <i class="bi bi-check-circle-fill"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
</x-app-layout>
