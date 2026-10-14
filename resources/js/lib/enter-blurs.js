// --- Enter leaves the field instead of submitting the form ---
// Active on pages that contain an element with data-enter-blurs.

export default function registerEnterBlurs() {
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
            event.preventDefault();
            event.target.blur();
        }
    });
}
