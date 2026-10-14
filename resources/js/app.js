import './bootstrap';
import registerConfirmSubmit from './lib/confirm-submit';
import registerEnterBlurs from './lib/enter-blurs';
import registerAjaxPagination from './lib/ajax-pagination';
import registerConfirmForm from './lib/confirm-form';

import Alpine from 'alpinejs';
import toastStack from './components/toast-stack';
import sharedImportManager from './components/import-manager';
import importDropzone from './components/import-dropzone';
import photoPicker from './components/photo-picker';
import removablePhoto from './components/removable-photo';
import activityFiles from './components/activity-files';
import autoplayCarousel from './components/autoplay-carousel';
import achievementList from './components/achievement-list';
import sectionBuilder from './components/section-builder';
import guestToasts from './components/guest-toasts';

window.Alpine = Alpine;

Alpine.data('toastStack', toastStack);
Alpine.data('sharedImportManager', sharedImportManager);
Alpine.data('importDropzone', importDropzone);
Alpine.data('photoPicker', photoPicker);
Alpine.data('removablePhoto', removablePhoto);
Alpine.data('activityFiles', activityFiles);
Alpine.data('autoplayCarousel', autoplayCarousel);
Alpine.data('achievementList', achievementList);
Alpine.data('sectionBuilder', sectionBuilder);
Alpine.data('guestToasts', guestToasts);


// --- Start Alpine once page modules that register Alpine components are loaded ---

const alpinePageModules = [];

if (document.querySelector('[data-featured-page]')) {
    alpinePageModules.push(import('./pages/featured'));
}

if (document.querySelector('[data-business-admin]')) {
    alpinePageModules.push(import('./pages/business-admin'));
}

if (document.querySelector('[data-user-admin]')) {
    alpinePageModules.push(import('./pages/user-admin'));
}

if (document.querySelector('[data-business-directory]')) {
    alpinePageModules.push(import('./pages/business-directory'));
}

if (document.querySelector('[data-testimony-admin]')) {
    alpinePageModules.push(import('./pages/testimony-admin'));
}

if (document.querySelector('[data-business-show]')) {
    alpinePageModules.push(import('./pages/business-show'));
}

const startAlpine = () => Promise.all(alpinePageModules).then(() => Alpine.start());

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startAlpine);
} else {
    startAlpine();
}

function initRevealOnScroll() {
	const targets = document.querySelectorAll('.reveal-on-scroll:not([data-reveal-bound="1"])');

	if (!targets.length) {
		return;
	}

	if (!('IntersectionObserver' in window)) {
		targets.forEach((target) => {
			target.classList.add('is-visible');
			target.dataset.revealBound = '1';
		});
		return;
	}

	if (!window.__ucoRevealObserver) {
		window.__ucoRevealObserver = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					window.__ucoRevealObserver.unobserve(entry.target);
				}
			});
		}, { threshold: 0.02 });
	}

	targets.forEach((target) => {
		target.dataset.revealBound = '1';
		window.__ucoRevealObserver.observe(target);
	});
}

window.initRevealOnScroll = initRevealOnScroll;

// Global Toast Helper
window.showToast = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { message, type }
    }));
};

document.addEventListener('DOMContentLoaded', () => {
	initRevealOnScroll();

    // Global Price Input Handler: 1000-step increments with Arrow Keys
    // This provides a 'Premium' UX where arrows jump by 1000, but allows ANY value (no validation errors)
    document.addEventListener('keydown', (e) => {
        const target = e.target;
        if (target.tagName === 'INPUT' && target.type === 'number') {
            const isPrice = target.name.includes('price') || 
                           target.id === 'price' || 
                           target.classList.contains('price-input');
            
            if (isPrice && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                e.preventDefault();
                const step = 1000;
                const currentVal = parseFloat(target.value) || 0;
                const newVal = e.key === 'ArrowUp' ? currentVal + step : currentVal - step;
                
                // Set value and ensure it's not negative unless allowed
                target.value = Math.max(0, newVal);
                
                // Manually trigger input/change events for Alpine.js or other bindings
                target.dispatchEvent(new Event('input', { bubbles: true }));
                target.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    });
});

// --- Page modules (loaded only where their markup exists) ---

registerConfirmSubmit();
registerAjaxPagination();
registerConfirmForm();

if (document.querySelector('[data-offering-form]')) {
    import('./pages/offering-form').then(({ default: initOfferingForm }) => initOfferingForm());
}

if (document.querySelector('select[data-tom-select], select[data-region-province]')) {
    import('./components/tom-select-fields').then(({ default: initTomSelectFields }) => initTomSelectFields());
}

if (document.querySelector('[data-enter-blurs]')) {
    registerEnterBlurs();
}

if (document.querySelector('[data-user-form]')) {
    import('./pages/user-form').then(({ default: initUserForm }) => initUserForm());
}
