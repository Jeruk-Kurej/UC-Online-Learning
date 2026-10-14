import TomSelect from 'tom-select/dist/js/tom-select.complete.js';

// --- Plain searchable selects ---
// <select data-tom-select="Placeholder text">

function initPlainSelects() {
    document.querySelectorAll('select[data-tom-select]').forEach((select) => {
        new TomSelect(select, { create: false, placeholder: select.dataset.tomSelect, searchField: ['text'] });
    });
}

// --- Province → city ---
// <select data-region-province data-region-city="#city">
// <select id="city" data-region-city-placeholder="Select City" data-region-city-starts-disabled>
// Without data-region-city-starts-disabled, the city list starts disabled only when no province is chosen.

function initRegionPairs() {
    document.querySelectorAll('select[data-region-province]').forEach((provinceElement) => {
        const cityElement = document.querySelector(provinceElement.dataset.regionCity);

        const citySelect = new TomSelect(cityElement, {
            create: false,
            placeholder: cityElement.dataset.regionCityPlaceholder,
            searchField: ['name'],
            valueField: 'id',
            labelField: 'name',
        });

        if ('regionCityStartsDisabled' in cityElement.dataset || ! provinceElement.value) {
            citySelect.disable();
        }

        new TomSelect(provinceElement, {
            create: false,
            placeholder: 'Select Province',
            searchField: ['text'],
            onChange(value) {
                citySelect.clear();
                citySelect.clearOptions();

                if (! value) {
                    citySelect.disable();
                    citySelect.settings.placeholder = 'Select Province First';
                    citySelect.inputState();
                    return;
                }

                citySelect.disable();
                citySelect.settings.placeholder = 'Loading cities...';
                citySelect.inputState();

                fetch(`/api/regencies?province_id=${value}`)
                    .then((response) => response.json())
                    .then((data) => {
                        citySelect.addOptions(data);
                        citySelect.enable();
                        citySelect.settings.placeholder = 'Select City';
                        citySelect.inputState();
                    });
            },
        });
    });
}

export default function initTomSelectFields() {
    initPlainSelects();
    initRegionPairs();
}
