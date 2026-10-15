<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/work-profile-edit.css')
    @endpush

    {{-- --- Header Section --- --}}
    <div class="reveal-on-scroll mb-6 flex items-center gap-4">
        <a href="{{ route('intrapreneurs.show', $company) }}" class="btn-uco btn-uco-secondary">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit Work Profile</h1>
            <p class="mt-1 text-sm text-gray-600">Update your professional employment listings in the UCO directory.</p>
        </div>
    </div>

    <div class="reveal-on-scroll rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('intrapreneurs.update', $company) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @include('work-profiles.partials.fields')

                {{-- --- Logo Upload with Live Preview & Remove Option --- --}}
                <x-forms.removable-image class="pb-5 md:col-span-2" label="Company Logo" name="logo_url" delete-name="delete_logo"
                                         :url="$company->logo_url" :has-image="$company->has_logo" :initial="$company->initial" noun="Logo"
                                         hint="JPG, PNG, WEBP — Max 5MB" box-class="w-[120px] h-[120px] rounded-xl bg-[#f8fafc] border-[1.5px] border-[#e2e8f0] group shadow-sm flex items-center justify-center p-2"
                                         actions-class="h-[120px]" image-class="" initials-class="uco-initials-bg">
                    @error('logo_url')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </x-forms.removable-image>

                <x-forms.field look="work-profile" name="job_description" label="Job Description & Responsibilities" required class="md:col-span-2">
                    <textarea name="job_description" id="job_description" rows="4" required
                              class="form-input-custom @error('job_description') border-red-500 @enderror"
                              placeholder="Describe your job roles, day-to-day operations, and career contributions...">{{ old('job_description', $company->job_description) }}</textarea>
                </x-forms.field>
            </div>

            {{-- --- Action Buttons --- --}}
            <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
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
