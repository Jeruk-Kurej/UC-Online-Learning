{{-- --- Work profile fields shared by the create and edit pages ($company is null when creating) --- --}}
<x-forms.input name="name" label="Company name" :value="$company?->name" required wrapper-class="md:col-span-2" placeholder="For example PT Metal Smeltindo Selaras" />

<x-forms.field name="category_id" label="Industry" required>
    <select name="category_id" id="category_id" data-tom-select="Choose an industry" required class="field-input @error('category_id') !border-red-700 @enderror">
        <option value="">Choose an industry</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $company?->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
</x-forms.field>

<x-forms.input name="position" label="Your job title" :value="$company?->position" required placeholder="For example Laboratory Foreman" />

<x-forms.field name="level_position" label="Level">
    <select name="level_position" id="level_position" data-tom-select="Choose a level" class="field-input @error('level_position') !border-red-700 @enderror">
        <option value="">Choose a level</option>
        <option value="Intern" {{ old('level_position', $company?->level_position) === 'Intern' ? 'selected' : '' }}>Intern</option>
        <option value="Staff" {{ old('level_position', $company?->level_position) === 'Staff' ? 'selected' : '' }}>Staff or officer</option>
        <option value="Supervisor" {{ old('level_position', $company?->level_position) === 'Supervisor' ? 'selected' : '' }}>Supervisor or team leader</option>
        <option value="Manager" {{ old('level_position', $company?->level_position) === 'Manager' ? 'selected' : '' }}>Manager or head of department</option>
        <option value="Director" {{ old('level_position', $company?->level_position) === 'Director' ? 'selected' : '' }}>Director or C-level</option>
    </select>
</x-forms.field>

<x-forms.field name="company_scale" label="Company size">
    <select name="company_scale" id="company_scale" data-tom-select="Choose a size" class="field-input @error('company_scale') !border-red-700 @enderror">
        <option value="">Choose a size</option>
        <option value="Perusahaan swasta lokal/regional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta lokal/regional' ? 'selected' : '' }}>Private company, local or regional</option>
        <option value="Perusahaan swasta nasional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta nasional' ? 'selected' : '' }}>Private company, national</option>
        <option value="Perusahaan swasta multinasional/internasional" {{ old('company_scale', $company?->company_scale) === 'Perusahaan swasta multinasional/internasional' ? 'selected' : '' }}>International or state-owned company</option>
    </select>
</x-forms.field>

<x-forms.input name="year_started_working" label="Year you started working here" type="number" :value="$company?->year_started_working" min="1900" max="{{ date('Y') + 1 }}" placeholder="For example 2021" />

<x-forms.region-fields :provinces="$provinces" :cities="$availableCities" :selected-province-id="$selectedProvinceId" :selected-city-id="$selectedCityId" :record="$company" />
