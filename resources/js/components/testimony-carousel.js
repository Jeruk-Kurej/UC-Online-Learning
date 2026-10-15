// --- Featured page testimony carousel: horizontally scrolled slides, dots, auto-scroll and the full-testimony modal ---
// x-data="testimonyCarousel()"; registered by pages/featured.js (the only page that uses it).

export default function testimonyCarousel() {
    return {
        activeSlide: 0,
        totalSlides: 0,
        slides: [],
        dotIndices: [],
        autoScrollInterval: null,
        showModal: false,
        modalName: '',
        modalStatus: '',
        modalPhoto: '',
        modalText: '',
        openModal(name, status, photo, text) {
            this.modalName = name;
            this.modalStatus = status;
            this.modalPhoto = photo;
            this.modalText = text;
            this.showModal = true;
            this.stopAutoScroll();
        },
        closeModal() {
            this.showModal = false;
            this.startAutoScroll();
        },
        init() {
            this.$nextTick(() => {
                this.refreshSlides();
                this.updateScroll();
                this.startAutoScroll();

                if (this.$refs.track) {
                    this.$refs.track.addEventListener('scrollend', () => this.updateScroll());
                }
            });

            window.addEventListener('resize', () => {
                this.refreshSlides();
                this.updateScroll();
            });
        },
        refreshSlides() {
            if (!this.$refs.track) return;
            this.slides = Array.from(this.$refs.track.querySelectorAll('[data-carousel-slide]'));

            const track = this.$refs.track;
            const maxScrollLeft = track.scrollWidth - track.clientWidth;

            const indices = [];
            this.slides.forEach((slide, index) => {
                const scrollLeftVal = this.getSlideScrollLeft(index);
                if (scrollLeftVal < maxScrollLeft - 15) {
                    indices.push(index);
                }
            });

            if (this.slides.length > 0) {
                const lastIndex = this.slides.length - 1;
                if (!indices.includes(lastIndex)) {
                    indices.push(lastIndex);
                }
            }

            this.dotIndices = indices;
            this.totalSlides = this.dotIndices.length;
        },
        startAutoScroll() {
            this.stopAutoScroll();
            if (this.totalSlides <= 1) return;
            this.autoScrollInterval = setInterval(() => {
                let currentIndex = this.dotIndices.indexOf(this.activeSlide);
                let nextIndex = currentIndex + 1;
                if (nextIndex >= this.totalSlides || nextIndex < 0) {
                    nextIndex = 0;
                }
                this.scrollTo(this.dotIndices[nextIndex]);
            }, 4000);
        },
        stopAutoScroll() {
            if (this.autoScrollInterval) {
                clearInterval(this.autoScrollInterval);
                this.autoScrollInterval = null;
            }
        },
        getSlideScrollLeft(index) {
            const track = this.$refs.track;
            const slide = this.slides[index];
            if (!track || !slide) return 0;
            return slide.offsetLeft - track.offsetLeft;
        },
        updateScroll() {
            if (!this.$refs.track || this.slides.length === 0 || this.dotIndices.length === 0) return;

            const track = this.$refs.track;
            const scrollLeft = track.scrollLeft;
            const maxScrollLeft = track.scrollWidth - track.clientWidth;

            let closestIndex = this.dotIndices[0];
            let minDistance = Infinity;

            this.dotIndices.forEach((slideIndex) => {
                const slideStart = Math.min(this.getSlideScrollLeft(slideIndex), maxScrollLeft);
                const distance = Math.abs(scrollLeft - slideStart);
                if (distance < minDistance) {
                    minDistance = distance;
                    closestIndex = slideIndex;
                }
            });

            this.activeSlide = closestIndex;
        },
        scrollTo(index) {
            if (!this.slides[index] || !this.$refs.track) return;

            const track = this.$refs.track;
            const maxScrollLeft = track.scrollWidth - track.clientWidth;
            const targetScroll = Math.min(this.getSlideScrollLeft(index), maxScrollLeft);

            track.scrollTo({
                left: targetScroll,
                behavior: 'smooth',
            });
            this.activeSlide = index;
        },
    };
}
