// --- Toasts raised from JavaScript ---
// window.showToast(message, type) dispatches a `notify` event; at most three stay on screen.

export default function toastStack() {
    return {
        toasts: [],

        add(event) {
            const id = Date.now();
            const { message, type } = event.detail;
            this.toasts.push({ id, message, type: type || 'success' });

            if (this.toasts.length > 3) {
                this.toasts.shift();
            }

            setTimeout(() => this.remove(id), 4000);
        },

        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
    };
}
