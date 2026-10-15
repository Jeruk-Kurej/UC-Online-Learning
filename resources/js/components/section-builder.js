// --- About page section builder: add, remove, reorder and edit sections, then save as JSON ---
// x-data="sectionBuilder(initialSections)"; the server always sends at least the default sections (Page::sectionsFor).

export default function sectionBuilder(initialSections) {
    return {
        sections: initialSections,

        getSectionTypeName(type) {
            const names = {
                hero: '🎯 Hero Header Banner',
                feature_cards: '🚀 Feature Cards Grid',
                stats_grid: '📊 Impact Stats Grid',
                text_block: '📝 Text / FAQ Content Block',
                cta_banner: '📣 Call-To-Action Banner',
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
                    { title: 'Feature 2', description: 'Description for feature card 2.', icon: 'bi-people' },
                ];
            } else if (type === 'stats_grid') {
                newSec.title = 'Our Key Achievements';
                newSec.items = [
                    { number: '100+', label: 'Metric 1' },
                    { number: '50+', label: 'Metric 2' },
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
                icon: 'bi-rocket-takeoff',
            });
        },

        removeCard(secIdx, cardIdx) {
            this.sections[secIdx].cards.splice(cardIdx, 1);
        },

        addStat(secIdx) {
            if (!this.sections[secIdx].items) this.sections[secIdx].items = [];
            this.sections[secIdx].items.push({
                number: '10+',
                label: 'New Metric Label',
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
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    title: 'About Us',
                    content_json: { sections: this.sections },
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    alert('All section changes saved successfully!');
                })
                .catch((err) => {
                    console.error(err);
                    alert('Error saving section data.');
                });
        },
    };
}
