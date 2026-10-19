// --- Admin list buttons that change one row in place: a form with data-row-toggle="status" or "featured" posts over fetch ---
// Without scripts the same form posts normally and the page reloads. The row is the closest [data-user-row]; it carries data-visible.

import showFeatured from './featured-button';
import { readJsonOrThrow, sendJson } from './json-request';

function showVisibility(row, visible) {
    row.dataset.visible = visible ? '1' : '0';

    const badge = row.querySelector('[data-visibility-badge]');
    if (badge) {
        badge.textContent = visible ? 'Visible' : 'Hidden';
        badge.classList.toggle('badge-success', visible);
        badge.classList.toggle('badge-neutral', !visible);
    }

    const button = row.querySelector('[data-status-button]');
    if (button) {
        button.textContent = visible ? 'Hide' : 'Show';
        button.classList.toggle('btn-danger-outline', visible);
        button.classList.toggle('btn-secondary', !visible);
    }
}

function showFeaturedAvailability(row) {
    const button = row.querySelector('[data-featured-button]');
    if (!button) return;

    const visible = row.dataset.visible === '1';
    button.disabled = !visible;
    if (visible) {
        button.removeAttribute('title');
    } else {
        button.title = 'Only visible records can be featured';
    }
}

const handlers = {
    status(row, data) {
        showVisibility(row, data.is_visible);
        const feature = row.querySelector('[data-featured-button]');
        if (feature) showFeatured(feature, data.is_featured);
        showFeaturedAvailability(row);
    },
    featured(row, data) {
        const feature = row.querySelector('[data-featured-button]');
        if (feature) showFeatured(feature, data.is_featured);
    },
};

async function submitRowToggle(form) {
    const row = form.closest('[data-user-row]');
    const button = form.querySelector('button');
    button.disabled = true;

    try {
        const data = await readJsonOrThrow(
            await sendJson(form.action, 'POST', undefined, { 'X-Requested-With': 'XMLHttpRequest' }),
        );
        handlers[form.dataset.rowToggle](row, data);
        window.showToast(data.message, 'success');
        window.dispatchEvent(new CustomEvent('ajax-update-stats'));
    } catch (error) {
        window.showToast(
            typeof error?.message === 'string' && error.success === false
                ? error.message
                : 'Could not save the change. Try again.',
            'error',
        );
    } finally {
        button.disabled = false;
        showFeaturedAvailability(row);
    }
}

export default function registerRowToggles() {
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form instanceof HTMLFormElement && form.dataset.rowToggle) {
            event.preventDefault();
            submitRowToggle(form);
        }
    });
}
