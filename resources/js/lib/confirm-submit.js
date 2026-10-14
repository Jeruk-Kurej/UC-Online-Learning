// --- Confirm, then submit a form ---
// <button type="button" data-confirm-submit="form-id" data-confirm-message="Are you sure?">

export default function registerConfirmSubmit() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-confirm-submit]');

        if (trigger && confirm(trigger.dataset.confirmMessage)) {
            document.getElementById(trigger.dataset.confirmSubmit)?.submit();
        }
    });
}
