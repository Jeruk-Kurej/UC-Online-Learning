// --- window.showToast(message, type): raises a toast through the `notify` event that toastStack listens to ---

export default function showToast(message, type = 'success') {
    window.dispatchEvent(
        new CustomEvent('notify', {
            detail: { message, type },
        }),
    );
}
