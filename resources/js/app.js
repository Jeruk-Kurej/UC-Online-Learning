import './bootstrap';
import './image-preview';

window.initSectionBuilder = function(initialSections) {
    return {
        sections: Array.isArray(initialSections) && initialSections.length ? initialSections : [
            {
                type: 'hero',
                badge: 'About UC Online Learning',
                title: 'Building the Future of Student & Alumni Entrepreneurship',
                subtitle: 'Connecting founders, intrapreneurs, and corporate innovators across Universitas Ciputra.'
            },
            {
                type: 'feature_cards',
                badge: 'Pillars of Excellence',
                title: 'Built for Sustainable Impact',
                subtitle: 'Designed to support founders and intrapreneurs at every phase of their growth journey.',
                cards: [
                    { title: 'Rapid Launch', description: 'We provide the tools and network needed to transform academic theories into viable market products within weeks, not years.', icon: 'bi-rocket-takeoff' },
                    { title: 'Global Network', description: 'Connect with a diverse community of alumni mentors, industry experts, and fellow entrepreneurs across all major industries.', icon: 'bi-people' },
                    { title: 'Scalable Growth', description: 'From local startups to multinational enterprises, our platform supports scaling businesses at every stage of their lifecycle.', icon: 'bi-graph-up-arrow' }
                ]
            },
            {
                type: 'stats_grid',
                title: 'Driving Community Impact',
                items: [
                    { number: '500+', label: 'Active Ventures' },
                    { number: '1200+', label: 'Graduated Founders' },
                    { number: '24', label: 'Industry Categories' },
                    { number: '15+', label: 'Years of Heritage' }
                ]
            },
            {
                type: 'cta_banner',
                heading: 'Ready to build your legacy?',
                subtitle: 'Join the UCO community today and gain access to a world of entrepreneurial opportunities.',
                primary_btn_text: 'Get Started Now',
                secondary_btn_text: 'Explore Directory'
            }
        ],

        getSectionTypeName(type) {
            const names = {
                'hero': '🎯 Hero Header Banner',
                'feature_cards': '🚀 Feature Cards Grid',
                'stats_grid': '📊 Impact Stats Grid',
                'text_block': '📝 Text / FAQ Content Block',
                'cta_banner': '📣 Call-To-Action Banner',
            };
            return names[type] || 'Section Block';
        },

        addSection(type) {
            let newSec = { type: type };
            if (type === 'hero') {
                newSec.badge = 'New Vision Tagline';
                newSec.title = 'New Section Headline';
                newSec.subtitle = 'Description paragraph text goes here.';
            } else if (type === 'feature_cards') {
                newSec.badge = 'Category Tag';
                newSec.title = 'Key Benefits & Pillars';
                newSec.subtitle = 'Supporting explanation text for cards.';
                newSec.cards = [
                    { title: 'Feature 1', description: 'Description for feature card 1.', icon: 'bi-rocket-takeoff' },
                    { title: 'Feature 2', description: 'Description for feature card 2.', icon: 'bi-people' }
                ];
            } else if (type === 'stats_grid') {
                newSec.title = 'Our Key Achievements';
                newSec.items = [
                    { number: '100+', label: 'Metric 1' },
                    { number: '50+', label: 'Metric 2' }
                ];
            } else if (type === 'text_block') {
                newSec.heading = 'New Article Section';
                newSec.content = 'Write your paragraph text here...';
            } else if (type === 'cta_banner') {
                newSec.heading = 'Ready to get started?';
                newSec.subtitle = 'Join our community today.';
                newSec.primary_btn_text = 'Get Started';
                newSec.secondary_btn_text = 'Learn More';
            }
            this.sections.push(newSec);
        },

        removeSection(idx) {
            if (confirm('Are you sure you want to remove this section?')) {
                this.sections.splice(idx, 1);
            }
        },

        moveUp(idx) {
            if (idx > 0) {
                const temp = this.sections[idx];
                this.sections[idx] = this.sections[idx - 1];
                this.sections[idx - 1] = temp;
            }
        },

        moveDown(idx) {
            if (idx < this.sections.length - 1) {
                const temp = this.sections[idx];
                this.sections[idx] = this.sections[idx + 1];
                this.sections[idx + 1] = temp;
            }
        },

        addCard(secIdx) {
            if (!this.sections[secIdx].cards) this.sections[secIdx].cards = [];
            this.sections[secIdx].cards.push({
                title: 'New Card Title',
                description: 'Card description content...',
                icon: 'bi-rocket-takeoff'
            });
        },

        removeCard(secIdx, cardIdx) {
            this.sections[secIdx].cards.splice(cardIdx, 1);
        },

        addStat(secIdx) {
            if (!this.sections[secIdx].items) this.sections[secIdx].items = [];
            this.sections[secIdx].items.push({
                number: '10+',
                label: 'New Metric Label'
            });
        },

        removeStat(secIdx, statIdx) {
            this.sections[secIdx].items.splice(statIdx, 1);
        },

        saveSections(updateUrl) {
            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: 'About Us',
                    content_json: { sections: this.sections }
                })
            })
            .then(res => res.json())
            .then(data => {
                alert('All section changes saved successfully!');
            })
            .catch(err => {
                console.error(err);
                alert('Error saving section data.');
            });
        }
    };
};

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
