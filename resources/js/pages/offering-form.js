import TomSelect from 'tom-select/dist/js/tom-select.complete.js';

// --- Price type ---

export default function initOfferingForm() {
    const priceTypeSelect = document.getElementById('price_type');
    const priceInput = document.getElementById('price');
    const star = document.getElementById('price-required-star');

    if (! priceTypeSelect || ! priceInput) {
        return;
    }

    const handlePriceTypeChange = (value) => {
        if (value === 'unspecified' || value === 'customize') {
            priceInput.value = '';
            priceInput.disabled = true;
            priceInput.required = false;
            priceInput.placeholder = value === 'unspecified' ? 'Price not specified' : 'Customized by order';
            if (star) star.style.display = 'none';
        } else {
            priceInput.disabled = false;
            priceInput.required = true;
            priceInput.placeholder = priceInput.dataset.fixedPlaceholder;
            if (star) star.style.display = 'inline';
        }
    };

    const ts = new TomSelect(priceTypeSelect, {
        create: false,
        placeholder: '-- Select Price Type --',
        searchField: ['text'],
    });

    ts.on('change', (val) => handlePriceTypeChange(val));
    handlePriceTypeChange(ts.getValue());
}
