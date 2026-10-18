// --- Slide index for a carousel moved by hand ---
// x-data="slideCarousel(count)"; the markup reads activeIndex and total (see people/slide-controls).

export default function slideCarousel(total) {
    return {
        activeIndex: 0,
        total,
    };
}
