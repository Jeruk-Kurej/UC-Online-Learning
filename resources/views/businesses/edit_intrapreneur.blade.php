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
                    {{-- Company Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="form-label-custom">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                               class="form-input-custom @error('name') border-red-500 @enderror"
                               placeholder="e.g. PT Metal Smeltindo Selaras">
                        @error('name')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="form-label-custom">Industry Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" data-tom-select="Select Category" required 
                                class="form-input-custom @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $company->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Position / Job Role --}}
                    <div>
                        <label for="position" class="form-label-custom">Your Position / Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position', $company->position) }}" required
                               class="form-input-custom @error('position') border-red-500 @enderror"
                               placeholder="e.g. Inspection Laboratory Foreman">
                        @error('position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Level --}}
                    <div>
                        <label for="level_position" class="form-label-custom">Job Role Level</label>
                        <select name="level_position" id="level_position" data-tom-select="Select Level" 
                                class="form-input-custom @error('level_position') border-red-500 @enderror">
                            <option value="">Select Level</option>
                            <option value="Intern" {{ old('level_position', $company->level_position) === 'Intern' ? 'selected' : '' }}>Intern / Magang</option>
                            <option value="Staff" {{ old('level_position', $company->level_position) === 'Staff' ? 'selected' : '' }}>Staff / Officer</option>
                            <option value="Supervisor" {{ old('level_position', $company->level_position) === 'Supervisor' ? 'selected' : '' }}>Supervisor / Team Leader</option>
                            <option value="Manager" {{ old('level_position', $company->level_position) === 'Manager' ? 'selected' : '' }}>Manager / Head of Dept</option>
                            <option value="Director" {{ old('level_position', $company->level_position) === 'Director' ? 'selected' : '' }}>Director / C-Level</option>
                        </select>
                        @error('level_position')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Company Scale --}}
                    <div>
                        <label for="company_scale" class="form-label-custom">Company Scale</label>
                        <select name="company_scale" id="company_scale" data-tom-select="Select Scale" 
                                class="form-input-custom @error('company_scale') border-red-500 @enderror">
                            <option value="">Select Scale</option>
                            <option value="Perusahaan swasta lokal/regional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta lokal/regional' ? 'selected' : '' }}>Swasta Lokal / Regional</option>
                            <option value="Perusahaan swasta nasional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta nasional' ? 'selected' : '' }}>Swasta Nasional</option>
                            <option value="Perusahaan swasta multinasional/internasional" {{ old('company_scale', $company->company_scale) === 'Perusahaan swasta multinasional/internasional' ? 'selected' : '' }}>Swasta Internasional / BUMN</option>
                        </select>
                        @error('company_scale')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Year Started Working --}}
                    <div>
                        <label for="year_started_working" class="form-label-custom">Year Started Working</label>
                        <input type="number" name="year_started_working" id="year_started_working" 
                               value="{{ old('year_started_working', $company->year_started_working) }}" 
                               min="1900" max="{{ date('Y') + 1 }}"
                               class="form-input-custom @error('year_started_working') border-red-500 @enderror"
                               placeholder="e.g. 2021">
                        @error('year_started_working')<p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Province --}}
                    <div>
                        <label for="province" class="form-label-custom">Province</label>
                        <select name="province" id="province" data-region-province data-region-city="#city" class="form-input-custom">
                            <option value="">Select Province</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov->id }}" {{ (old('province', $selectedProvinceId) == $prov->id) ? 'selected' : '' }}>
                                    {{ ucwords(strtolower($prov->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city" class="form-label-custom">City / Regency</label>
                        <select name="city" id="city" data-region-city-placeholder="Select City" class="form-input-custom">
                            <option value="">Select Province First</option>
                            @foreach($availableCities as $city_item)
                                <option value="{{ $city_item->id }}" {{ (old('city', $company->city) == $city_item->name) ? 'selected' : '' }}>
                                    {{ ucwords(strtolower($city_item->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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
