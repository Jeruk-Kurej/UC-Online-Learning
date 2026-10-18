{{-- --- Province and City / Regency selects; tom-select-fields loads the cities of the picked province --- --}}
@props(['look', 'selectClass', 'provinces', 'cities', 'selectedProvinceId', 'record'])

<x-forms.field :look="$look" name="province" label="Province" :show-error="false">
    <select name="province" id="province" data-region-province data-region-city="#city" data-regencies-url="{{ route('api.regencies', [], false) }}" class="{{ $selectClass }}">
        <option value="">Select Province</option>
        @foreach ($provinces as $province)
            <option value="{{ $province->id }}" {{ old('province', $selectedProvinceId) == $province->id ? 'selected' : '' }}>
                {{ $province->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>

<x-forms.field :look="$look" name="city" label="City / Regency" :show-error="false">
    <select name="city" id="city" data-region-city-placeholder="{{ $record ? 'Select City' : 'Select Province First' }}" @unless ($record) data-region-city-starts-disabled @endunless class="{{ $selectClass }}">
        <option value="">Select Province First</option>
        @foreach ($cities as $city)
            <option value="{{ $city->id }}" {{ old('city', $record?->city) == $city->name ? 'selected' : '' }}>
                {{ $city->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>
