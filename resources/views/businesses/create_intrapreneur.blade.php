<x-app-layout>
    @push('styles')
        @vite('resources/css/pages/work-profile-create.css')
    @endpush

    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Header Section --}}
        <div class="mb-8 flex items-center gap-4 reveal-on-scroll">
            <a href="{{ route('businesses.my') }}" class="btn-uco btn-uco-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Register Workplace</h1>
                <p class="text-sm text-gray-500 mt-1">Showcase your corporate professional career within the UCO directory.</p>
            </div>
        </div>

        {{-- Notice Banner --}}
        <div class="mb-8 p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-left reveal-on-scroll">
            <p class="text-xs font-bold text-indigo-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <i class="bi bi-info-circle-fill"></i>
                Professional Career Listing
            </p>
            <p class="text-xs text-indigo-700 leading-relaxed font-medium">
                This form registers the company where you are employed. You do not need to own this business; this is used to present and catalog your accomplishments as a professional intrapreneur.
            </p>
        </div>

        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 sm:p-8 reveal-on-scroll">
            <form method="POST" action="{{ route('intrapreneurs.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('businesses.partials.work-profile-fields')

                    {{-- Logo Upload --}}
                    <div>
                        <label for="logo_url" class="form-label-custom">Company Logo</label>
                        <input type="file" name="logo_url" id="logo_url" accept="image/*"
                               class="form-input-custom py-1.5 @error('logo_url') border-red-500 @enderror">
                        @error('logo_url')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Job Description --}}
                    <div class="md:col-span-2">
                        <label for="job_description" class="form-label-custom">Job Description & Responsibilities <span class="text-red-500">*</span></label>
                        <textarea name="job_description" id="job_description" required
                                  class="form-textarea-custom @error('job_description') border-red-500 @enderror"
                                  placeholder="Describe your job roles, day-to-day operations, and career contributions...">{{ old('job_description') }}</textarea>
                        @error('job_description')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between border-t border-slate-100 pt-6 mt-8">
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
