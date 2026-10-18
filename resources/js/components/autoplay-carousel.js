// --- Slide carousel that advances by itself every 3.5 s ---
// x-data="autoplayCarousel(count)"; the markup reads activeIndex and calls resetAutoplay() on manual moves.

import slideCarousel from './slide-carousel';

export default function autoplayCarousel(total) {
    return {
        ...slideCarousel(total),
        autoplayTimer: null,

        startAutoplay() {
            if (this.total > 1) {
                this.autoplayTimer = setInterval(() => {
                    this.activeIndex = (this.activeIndex + 1) % this.total;
                }, 3500);
            }
        },

        resetAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.startAutoplay();
            }
        },

        init() {
            this.startAutoplay();
        },
    };
}
