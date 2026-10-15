{{-- --- Work profile fields shared by the create and edit pages ($company is null when creating) --- --}}
<x-forms.field look="work-profile" name="name" label="Company Name" required class="md:col-span-2">
    <input type="text" name="name" id="name" value="{{ old('name', $company?->name) }}" required
           class="form-input-custom @error('name') border-red-500 @enderror"
           placeholder="e.g. PT Metal Smeltindo Selaras">
</x-forms.field>

<x-forms.field look="work-profile" name="category_id" label="Industry Category" required>
    <select name="category_id" id="category_id" data-tom-select="Select Category" required
            class="form-input-custom @error('category_id') border-red-500 @enderror">
        <option value="">Select Category</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $company?->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</x-forms.field>

<x-forms.field look="work-profile" name="position" label="Your Position / Job Title" required>
    <input type="text" name="position" id="position" value="{{ old('position', $company?->position) }}" required
           class="form-input-custom @error('position') border-red-500 @enderror"
           placeholder="e.g. Inspection Laboratory Foreman">
</x-forms.field>

<x-forms.field look="work-profile" name="level_position" label="Job Role Level">
    <select name="level_position" id="level_position" data-tom-select="Select Level"
            class="form-input-custom @error('level_position') border-red-500 @enderror">
        <option value="">Select Level</option>
        <option value="Intern" {{ old('level_position', $company?->level_position) === 'Intern' ? 'selected' : '' }}>Intern / Magang</option>
        <option value="Staff" {{ old('level_position', $company?->level_position) === 'Staff' ? 'selected' : '' }}>Staff / Officer</option>
        <option value="Supervisor" {{ old('level_position', $company?->level_position) === 'Supervisor' ? 'selected' : '' }}>Supervisor / Team Leader</option>
        <option value="Manager" {{ old('level_position', $company?->level_position) === 'Manager' ? 'selected' : '' }}>Manager / Head of Dept</option>
        <option value="Director" {{ old('level_position', $company?->level_position) === 'Director' ? 'selected' : '' }}>Director / C-Level</option>
    </select>
</x-forms.field>

<x-forms.field look="work-profile" name="company_scale" label="Company Scale">
    <select name="company_scale" id="company_scale" data-tom-select="Select Scale"
            class="form-input-custom @error('company_scale') border-red-500 @enderror">
        <option value="">Select Scale</option>
        <option value="Perusahaan swasta lokal/regional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta lokal/regional' ? 'selected' : '' }}>Swasta Lokal / Regional</option>
        <option value="Perusahaan swasta nasional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta nasional' ? 'selected' : '' }}>Swasta Nasional</option>
        <option value="Perusahaan swasta multinasional/internasional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta multinasional/internasional' ? 'selected' : '' }}>Swasta Internasional / BUMN</option>
    </select>
</x-forms.field>

<x-forms.field look="work-profile" name="year_started_working" label="Year Started Working">
    <input type="number" name="year_started_working" id="year_started_working"
           value="{{ old('year_started_working', $company?->year_started_working) }}"
           min="1900" max="{{ date('Y') + 1 }}"
           class="form-input-custom @error('year_started_working') border-red-500 @enderror"
           placeholder="e.g. 2021">
</x-forms.field>

<x-forms.field look="work-profile" name="province" label="Province" :show-error="false">
    <select name="province" id="province" data-region-province data-region-city="#city" class="form-input-custom">
        <option value="">Select Province</option>
        @foreach ($provinces as $prov)
            <option value="{{ $prov->id }}" {{ old('province', $selectedProvinceId) == $prov->id ? 'selected' : '' }}>
                {{ $prov->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>

<x-forms.field look="work-profile" name="city" label="City / Regency" :show-error="false">
    <select name="city" id="city" data-region-city-placeholder="{{ $company ? 'Select City' : 'Select Province First' }}" @unless ($company) data-region-city-starts-disabled @endunless class="form-input-custom">
        <option value="">Select Province First</option>
        @foreach ($availableCities as $city_item)
            <option value="{{ $city_item->id }}" {{ old('city', $company?->city) == $city_item->name ? 'selected' : '' }}>
                {{ $city_item->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>
