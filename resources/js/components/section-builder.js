// --- About page section builder: add, remove, reorder and edit sections, then save as JSON ---
// x-data="sectionBuilder(initialSections)" with data-save-url on the same element; the server always sends at least the default sections (Page::sectionsFor).

import { sendJson } from '../lib/json-request';

export default function sectionBuilder(initialSections) {
    return {
        sections: initialSections,

        getSectionTypeName(type) {
            const names = {
                hero: 'Page title',
                feature_cards: 'Cards',
                stats_grid: 'Numbers',
                text_block: 'Text',
                cta_banner: 'Call to action',
            };
            return names[type] || 'Section';
        },

        addSection(type) {
            let newSec = { type: type };
            if (type === 'hero') {
                newSec.badge = '';
                newSec.title = 'Title';
                newSec.subtitle = 'Write a short text here.';
            } else if (type === 'feature_cards') {
                newSec.badge = '';
                newSec.title = 'Title';
                newSec.subtitle = '';
                newSec.cards = [
                    { title: 'First card', description: 'Write a short text here.', icon: 'bi-rocket-takeoff' },
                    { title: 'Second card', description: 'Write a short text here.', icon: 'bi-people' },
                ];
            } else if (type === 'stats_grid') {
                newSec.title = 'Title';
                newSec.items = [
                    { number: '0', label: 'What it counts' },
                    { number: '0', label: 'What it counts' },
                ];
            } else if (type === 'text_block') {
                newSec.heading = 'Heading';
                newSec.content = 'Write the text here.';
            } else if (type === 'cta_banner') {
                newSec.heading = 'Heading';
                newSec.subtitle = 'Write a short text here.';
                newSec.primary_btn_text = '';
                newSec.secondary_btn_text = 'See the directory';
            }
            this.sections.push(newSec);
        },

        removeSection(idx) {
            if (confirm('Remove this section?')) {
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
                title: 'Card title',
                description: 'Write a short text here.',
                icon: 'bi-rocket-takeoff',
            });
        },

        removeCard(secIdx, cardIdx) {
            this.sections[secIdx].cards.splice(cardIdx, 1);
        },

        addStat(secIdx) {
            if (!this.sections[secIdx].items) this.sections[secIdx].items = [];
            this.sections[secIdx].items.push({
                number: '0',
                label: 'What it counts',
            });
        },

        removeStat(secIdx, statIdx) {
            this.sections[secIdx].items.splice(statIdx, 1);
        },

        saveSections() {
            sendJson(this.$root.dataset.saveUrl, 'PUT', {
                title: 'About Us',
                content_json: { sections: this.sections },
            })
                .then((res) => res.json())
                .then((data) => {
                    window.showToast('The page is saved.', 'success');
                })
                .catch((err) => {
                    console.error(err);
                    window.showToast('The page could not be saved. Try again.', 'error');
                });
        },
    };
}
