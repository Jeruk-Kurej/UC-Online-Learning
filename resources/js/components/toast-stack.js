// --- Toasts raised from JavaScript ---
// window.showToast(message, type) dispatches a `notify` event; at most three stay on screen. Ids count up, so two toasts in the same millisecond never share one.

let nextId = 0;

export default function toastStack() {
    return {
        toasts: [],

        add(event) {
            const id = ++nextId;
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
