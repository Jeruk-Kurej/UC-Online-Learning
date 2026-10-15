<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/work-profile-create.css')
    @endpush

    <div class="mx-auto max-w-4xl px-4 py-8">
        {{-- --- Header Section --- --}}
        <div class="reveal-on-scroll mb-8 flex items-center gap-4">
            <a href="{{ route('businesses.my') }}" class="btn-uco btn-uco-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Register Workplace</h1>
                <p class="mt-1 text-sm text-gray-500">Showcase your corporate professional career within the UCO directory.</p>
            </div>
        </div>

        {{-- --- Notice Banner --- --}}
        <div class="reveal-on-scroll mb-8 rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-left">
            <p class="mb-1 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-indigo-800">
                <i class="bi bi-info-circle-fill"></i>
                Professional Career Listing
            </p>
            <p class="text-xs font-medium leading-relaxed text-indigo-700">
                This form registers the company where you are employed. You do not need to own this business; this is used to present and catalog your accomplishments as a professional intrapreneur.
            </p>
        </div>

        <div class="reveal-on-scroll rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
            <form method="POST" action="{{ route('intrapreneurs.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @include('businesses.partials.work-profile-fields')

                    {{-- --- Logo Upload --- --}}
                    <div>
                        <label for="logo_url" class="form-label-custom">Company Logo</label>
                        <input type="file" name="logo_url" id="logo_url" accept="image/*"
                               class="form-input-custom @error('logo_url') border-red-500 @enderror py-1.5">
                        @error('logo_url')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- --- Job Description --- --}}
                    <div class="md:col-span-2">
                        <label for="job_description" class="form-label-custom">Job Description & Responsibilities <span class="text-red-500">*</span></label>
                        <textarea name="job_description" id="job_description" required
                                  class="form-textarea-custom @error('job_description') border-red-500 @enderror"
                                  placeholder="Describe your job roles, day-to-day operations, and career contributions...">{{ old('job_description') }}</textarea>
                        @error('job_description')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- --- Action Buttons --- --}}
                <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
                    <a href="{{ route('businesses.my') }}" class="btn-uco btn-uco-neutral">
                        Cancel
                    </a>
                    <button type="submit" class="btn-uco btn-uco-primary">
                        <i class="bi bi-check-circle-fill"></i>
                        Register Workplace
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
