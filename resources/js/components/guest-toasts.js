// --- Toasts on the login page and guest layout: session messages on load, `notify` events later ---
// x-data="guestToasts(initialMessages)"; each toast hides after 5 s.

let nextId = 0;

export default function guestToasts(initial = []) {
    return {
        toasts: [],

        init() {
            initial.forEach((detail) => this.add({ detail }));
        },

        add(e) {
            const id = ++nextId;
            const { message, type } = e.detail;
            this.toasts.push({ id, message, type: type || 'success' });
            setTimeout(() => this.remove(id), 5000);
        },

        remove(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    };
}
