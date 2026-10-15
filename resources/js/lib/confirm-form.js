// --- Ask before a form is submitted ---
// <form data-confirm="Are you sure?"> — cancelling stops the submission.

export default function registerConfirmForm() {
    document.addEventListener('submit', (event) => {
        const message = event.target.dataset?.confirm;

        if (message && !confirm(message)) {
            event.preventDefault();
        }
    });
}
