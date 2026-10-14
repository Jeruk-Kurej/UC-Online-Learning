// --- Business detail page: photo/detail modals, hero slider, scroll position kept across form posts ---
// Imported by app.js on pages with [data-business-show]; Alpine starts after this module has run.

function businessShowPage() {
    return {
        showUserModal: false,
        fullscreenOpen: false,
        fullscreenSrc: '',
        fullscreenAlt: '',
        detailModalOpen: false,
        detailItem: {},

        init() {
            this.$watch('showUserModal', val => document.body.style.overflow = val ? 'hidden' : '');
            this.$watch('fullscreenOpen', val => { if (val) { document.body.style.overflow = 'hidden'; } else if (!this.showUserModal && !this.detailModalOpen) { document.body.style.overflow = ''; } });
            this.$watch('detailModalOpen', val => { if (val) { document.body.style.overflow = 'hidden'; } else if (!this.showUserModal && !this.fullscreenOpen) { document.body.style.overflow = ''; } });
        },

        openFullscreen(src, alt = 'Photo') {
            this.fullscreenSrc = src;
            this.fullscreenAlt = alt;
            this.fullscreenOpen = true;
        },

        openDetailModal(item) {
            this.detailItem = item;
            this.detailModalOpen = true;
        },
    };
}

// --- Hero photo slider: advances every 5 s, paused while hovered ---

function heroSlider(heroSlidesCount) {
    return {
        activeHeroSlide: 0,
        heroSlidesCount,
        heroTimer: null,

        startHeroTimer() {
            if (this.heroSlidesCount > 1) {
                this.heroTimer = setInterval(() => {
                    this.activeHeroSlide = (this.activeHeroSlide + 1) % this.heroSlidesCount;
                }, 5000);
            }
        },

        stopHeroTimer() {
            if (this.heroTimer) clearInterval(this.heroTimer);
        },
    };
}

// --- Scroll position kept across reloads caused by forms and action buttons on this page ---

function restoreScrollPosition() {
    const scrollPos = sessionStorage.getItem('uco_business_scroll_pos');
    if (scrollPos) {
        setTimeout(() => {
            window.scrollTo({
                top: parseInt(scrollPos),
                behavior: 'instant'
            });
            sessionStorage.removeItem('uco_business_scroll_pos');
        }, 50);
    }

    const saveScrollPos = () => {
        sessionStorage.setItem('uco_business_scroll_pos', window.scrollY);
    };

    document.querySelectorAll('form, .btn-save-scroll').forEach(el => {
        el.addEventListener('submit', saveScrollPos);
        el.addEventListener('click', (e) => {
            if (e.currentTarget.tagName === 'A' || e.currentTarget.tagName === 'BUTTON') {
                saveScrollPos();
            }
        });
    });
}

window.Alpine.data('businessShowPage', businessShowPage);
window.Alpine.data('heroSlider', heroSlider);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restoreScrollPosition);
} else {
    restoreScrollPosition();
}
