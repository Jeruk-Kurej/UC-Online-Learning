import TomSelect from 'tom-select/dist/js/tom-select.complete.js';

// --- Price type ---

export default function initOfferingForm() {
    const priceTypeSelect = document.getElementById('price_type');
    const priceInput = document.getElementById('price');

    if (!priceTypeSelect || !priceInput) {
        return;
    }

    const handlePriceTypeChange = (value) => {
        if (value === 'unspecified' || value === 'customize') {
            priceInput.value = '';
            priceInput.disabled = true;
            priceInput.required = false;
            priceInput.placeholder = value === 'unspecified' ? 'No price shown' : 'Depends on the order';
        } else {
            priceInput.disabled = false;
            priceInput.required = true;
            priceInput.placeholder = priceInput.dataset.fixedPlaceholder;
        }
    };

    const ts = new TomSelect(priceTypeSelect, {
        create: false,
        placeholder: 'Choose one',
        searchField: ['text'],
    });

    ts.on('change', (val) => handlePriceTypeChange(val));
    handlePriceTypeChange(ts.getValue());
}
