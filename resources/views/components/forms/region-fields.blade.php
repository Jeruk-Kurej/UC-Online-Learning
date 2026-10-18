{{-- --- Province and City / Regency selects; tom-select-fields loads the cities of the picked province --- --}}
@props(['selectClass' => 'field-input', 'provinces', 'cities', 'selectedProvinceId', 'record'])

<x-forms.field name="province" label="Province" :show-error="false">
    <select name="province" id="province" data-region-province data-region-city="#city" data-regencies-url="{{ route('api.regencies', [], false) }}" class="{{ $selectClass }}">
        <option value="">Choose a province</option>
        @foreach ($provinces as $province)
            <option value="{{ $province->id }}" {{ old('province', $selectedProvinceId) == $province->id ? 'selected' : '' }}>
                {{ $province->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>

<x-forms.field name="city" label="City or regency" :show-error="false">
    <select name="city" id="city" data-region-city-placeholder="{{ $record ? 'Choose a city' : 'Choose a province first' }}" @unless ($record) data-region-city-starts-disabled @endunless class="{{ $selectClass }}">
        <option value="">Choose a province first</option>
        @foreach ($cities as $city)
            <option value="{{ $city->id }}" {{ old('city', $record?->city) == $city->name ? 'selected' : '' }}>
                {{ $city->display_name }}
            </option>
        @endforeach
    </select>
</x-forms.field>
