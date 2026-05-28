<x-app-layout>
    @push('styles')
        <style>
            .form-label-custom {
                display: block; 
                font-size: 11px; 
                font-weight: 800; 
                color: #475569; 
                text-transform: uppercase; 
                margin-bottom: 8px;
                letter-spacing: 0.5px;
            }

            .form-input-custom {
                width: 100%; 
                height: 44px; 
                padding: 0 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                background: white;
            }

            .form-input-custom:focus {
                border-color: #f7931e !important;
                box-shadow: 0 0 0 3px rgba(247, 147, 30, 0.1) !important;
            }

            .form-textarea-custom {
                width: 100%; 
                padding: 12px 15px; 
                border: 1.5px solid #e2e8f0; 
                border-radius: 8px; 
                font-size: 13px; 
                font-weight: 600; 
                outline: none; 
                box-sizing: border-box;
                transition: all 0.2s ease;
                min-height: 120px;
            }

            .form-textarea-custom:focus {
                border-color: #f7931e !important;
                box-shadow: 0 0 0 3px rgba(247, 147, 30, 0.1) !important;
            }
        </style>
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
                    {{-- Company Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="form-label-custom">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="form-input-custom @error('name') border-red-500 @enderror"
                               placeholder="e.g. PT Metal Smeltindo Selaras">
                        @error('name')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="form-label-custom">Industry Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required 
                                class="form-input-custom @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Position / Job Role --}}
                    <div>
                        <label for="position" class="form-label-custom">Your Position / Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" required
                               class="form-input-custom @error('position') border-red-500 @enderror"
                               placeholder="e.g. Inspection Laboratory Foreman">
                        @error('position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Level --}}
                    <div>
                        <label for="level_position" class="form-label-custom">Job Role Level</label>
                        <select name="level_position" id="level_position" 
                                class="form-input-custom @error('level_position') border-red-500 @enderror">
                            <option value="">Select Level</option>
                            <option value="Intern" {{ old('level_position') === 'Intern' ? 'selected' : '' }}>Intern / Magang</option>
                            <option value="Staff" {{ old('level_position') === 'Staff' ? 'selected' : '' }}>Staff / Officer</option>
                            <option value="Supervisor" {{ old('level_position') === 'Supervisor' ? 'selected' : '' }}>Supervisor / Team Leader</option>
                            <option value="Manager" {{ old('level_position') === 'Manager' ? 'selected' : '' }}>Manager / Head of Dept</option>
                            <option value="Director" {{ old('level_position') === 'Director' ? 'selected' : '' }}>Director / C-Level</option>
                        </select>
                        @error('level_position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Company Scale --}}
                    <div>
                        <label for="company_scale" class="form-label-custom">Company Scale</label>
                        <select name="company_scale" id="company_scale" 
                                class="form-input-custom @error('company_scale') border-red-500 @enderror">
                            <option value="">Select Scale</option>
                            <option value="Perusahaan swasta lokal/regional" {{ old('company_scale') === 'Perusahaan swasta lokal/regional' ? 'selected' : '' }}>Swasta Lokal / Regional</option>
                            <option value="Perusahaan swasta nasional" {{ old('company_scale') === 'Perusahaan swasta nasional' ? 'selected' : '' }}>Swasta Nasional</option>
                            <option value="Perusahaan swasta multinasional/internasional" {{ old('company_scale') === 'Perusahaan swasta multinasional/internasional' ? 'selected' : '' }}>Swasta Internasional / BUMN</option>
                        </select>
                        @error('company_scale')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Year Started Working --}}
                    <div>
                        <label for="year_started_working" class="form-label-custom">Year Started Working</label>
                        <input type="number" name="year_started_working" id="year_started_working" 
                               value="{{ old('year_started_working') }}" 
                               min="1900" max="{{ date('Y') + 1 }}"
                               class="form-input-custom @error('year_started_working') border-red-500 @enderror"
                               placeholder="e.g. 2021">
                        @error('year_started_working')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

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
