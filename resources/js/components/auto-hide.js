// --- Flash message that hides itself after the given time ---
// x-data="autoHide(milliseconds)"; the markup reads show.

export default function autoHide(milliseconds) {
    return {
        show: true,

        init() {
            setTimeout(() => (this.show = false), milliseconds);
        },
    };
}
