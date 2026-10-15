// --- Price inputs: arrow up / down step by 1000 and never go below 0; typing any value is still allowed ---
// Applies to number inputs whose name contains "price" or whose id is "price".

export default function registerPriceArrowKeys() {
    document.addEventListener('keydown', (e) => {
        const target = e.target;
        if (target.tagName !== 'INPUT' || target.type !== 'number') {
            return;
        }

        const isPrice = target.name.includes('price') || target.id === 'price';
        if (!isPrice || (e.key !== 'ArrowUp' && e.key !== 'ArrowDown')) {
            return;
        }

        e.preventDefault();
        const step = 1000;
        const currentVal = parseFloat(target.value) || 0;
        const newVal = e.key === 'ArrowUp' ? currentVal + step : currentVal - step;

        target.value = Math.max(0, newVal);

        target.dispatchEvent(new Event('input', { bubbles: true }));
        target.dispatchEvent(new Event('change', { bubbles: true }));
    });
}
