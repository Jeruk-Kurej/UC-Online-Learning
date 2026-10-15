// --- Business detail page: product/service detail modal, scroll position kept across form posts ---
// Imported by app.js on pages with [data-business-show]; Alpine starts after this module has run.

function businessShowPage() {
    return {
        detailModalOpen: false,
        detailItem: {},

        init() {
            this.$watch('detailModalOpen', (val) => {
                document.body.style.overflow = val ? 'hidden' : '';
            });
        },

        openDetailModal(item) {
            this.detailItem = item;
            this.detailModalOpen = true;
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
                behavior: 'instant',
            });
            sessionStorage.removeItem('uco_business_scroll_pos');
        }, 50);
    }

    const saveScrollPos = () => {
        sessionStorage.setItem('uco_business_scroll_pos', window.scrollY);
    };

    document.querySelectorAll('form, .btn-save-scroll').forEach((el) => {
        el.addEventListener('submit', saveScrollPos);
        el.addEventListener('click', (e) => {
            if (e.currentTarget.tagName === 'A' || e.currentTarget.tagName === 'BUTTON') {
                saveScrollPos();
            }
        });
    });
}

window.Alpine.data('businessShowPage', businessShowPage);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restoreScrollPosition);
} else {
    restoreScrollPosition();
}
