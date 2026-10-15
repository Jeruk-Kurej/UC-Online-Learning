import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// --- Featured page: testimony carousels and GSAP scroll/hover animations ---
// Imported by app.js on pages with [data-featured-page]; Alpine starts after this module has run.
// gsap stays reachable as window.gsap, as it was when it came from the CDN.

window.gsap = gsap;

function testimonyCarousel() {
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
                behavior: 'smooth'
            });
            this.activeSlide = index;
        }
    }
}

function initFeaturedAnimations() {
    // --- GSAP animations ---
    gsap.registerPlugin(ScrollTrigger);

    // --- Default transitions are turned off only for animated items (avoids a flash of unstyled content) ---
    const animatedElements = document.querySelectorAll(
        'section:nth-of-type(2) .reveal-on-scroll, section:nth-of-type(3) .reveal-on-scroll, section:nth-of-type(4) .reveal-on-scroll, section:nth-of-type(5) .reveal-on-scroll'
    );
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(40px)';
        el.style.transition = 'none';
    });

    // --- Featured Intrapreneur Students ---
    gsap.to("section:nth-of-type(2) .reveal-on-scroll", {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.05,
        ease: "power2.out",
        scrollTrigger: {
            trigger: "section:nth-of-type(2)",
            start: "top 90%",
            toggleActions: "play none none none"
        }
    });

    // --- Featured Entrepreneur Students ---
    gsap.to("section:nth-of-type(3) .reveal-on-scroll", {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.05,
        ease: "power2.out",
        scrollTrigger: {
            trigger: "section:nth-of-type(3)",
            start: "top 90%",
            toggleActions: "play none none none"
        }
    });

    // --- Featured Ventures ---
    gsap.to("section:nth-of-type(4) .reveal-on-scroll", {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.05,
        ease: "power2.out",
        scrollTrigger: {
            trigger: "section:nth-of-type(4)",
            start: "top 90%",
            toggleActions: "play none none none"
        }
    });

    // --- Community Voices / Testimonies ---
    gsap.to("section:nth-of-type(5) .reveal-on-scroll", {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.05,
        ease: "power2.out",
        scrollTrigger: {
            trigger: "section:nth-of-type(5)",
            start: "top 90%",
            toggleActions: "play none none none"
        }
    });

    // --- Scroll-driven heading parallax (alternating horizontal movement) ---
    gsap.utils.toArray('.uco-parallax-text').forEach((text, idx) => {
        const movementX = idx % 2 === 0 ? 100 : -100;
        gsap.to(text, {
            x: movementX,
            scrollTrigger: {
                trigger: text,
                start: "top bottom",
                end: "bottom top",
                scrub: 1.2
            }
        });
    });

    // --- Spotlight hover coordinates ---
    document.querySelectorAll('.uco-premium-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });

    // --- Magnetic buttons ---
    document.querySelectorAll('.uco-magnetic-btn').forEach(btn => {
        btn.addEventListener('mousemove', e => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - (rect.width / 2);
            const y = e.clientY - rect.top - (rect.height / 2);

            gsap.to(btn, {
                x: x * 0.35,
                y: y * 0.35,
                duration: 0.3,
                ease: "power2.out"
            });
        });

        btn.addEventListener('mouseleave', () => {
            gsap.to(btn, {
                x: 0,
                y: 0,
                duration: 0.65,
                ease: "elastic.out(1.1, 0.4)"
            });
        });
    });

    // --- Background parallax on mouse move ---
    window.addEventListener('mousemove', e => {
        const mouseX = (e.clientX / window.innerWidth - 0.5) * 25;
        const mouseY = (e.clientY / window.innerHeight - 0.5) * 25;

        gsap.to('.uco-floating-shape', {
            x: -mouseX,
            y: -mouseY,
            duration: 1.5,
            ease: "power1.out",
            overwrite: "auto"
        });

        const orbs = document.querySelectorAll('.uco-float-orb');
        if (orbs.length >= 2) {
            gsap.to(orbs[0], {
                x: mouseX,
                y: mouseY,
                duration: 1.5,
                ease: "power1.out",
                overwrite: "auto"
            });
            gsap.to(orbs[1], {
                x: -mouseX,
                y: -mouseY,
                duration: 1.5,
                ease: "power1.out",
                overwrite: "auto"
            });
        }
    });
}

window.Alpine.data('testimonyCarousel', testimonyCarousel);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeaturedAnimations);
} else {
    initFeaturedAnimations();
}
