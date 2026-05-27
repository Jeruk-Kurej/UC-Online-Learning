import './bootstrap';
import './image-preview';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
