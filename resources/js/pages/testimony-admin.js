// --- Admin testimonies: AJAX list and the star button that features a testimony on the home page ---
// Imported by app.js on pages with [data-testimony-admin]; Alpine starts after this module has run.
// <button data-testimony-featured-toggle="{toggle URL}">

import ajaxList from '../lib/ajax-list';
import showFeatured from '../lib/featured-button';
import { readJsonOrThrow, sendJson } from '../lib/json-request';

function testimonyAdminList() {
    return ajaxList({ container: 'testimonies-list-container' });
}

function toggleFeatured(url, buttonElement) {
    if (buttonElement.disabled) return;
    buttonElement.disabled = true;

    sendJson(url, 'POST', undefined, { 'X-Requested-With': 'XMLHttpRequest' })
        .then(readJsonOrThrow)
        .then((data) => {
            if (data.success) {
                const isFeatured = data.is_featured;
                showFeatured(buttonElement, isFeatured);

                const statFeatured = document.getElementById('stat-featured-testimonies');
                if (statFeatured) {
                    let currentCount = parseInt(statFeatured.textContent, 10);
                    statFeatured.textContent = isFeatured ? currentCount + 1 : Math.max(0, currentCount - 1);
                }

                if (window.showToast) {
                    window.showToast(data.message, 'success');
                }
            } else {
                if (window.showToast) {
                    window.showToast(data.message || 'An error occurred.', 'error');
                } else {
                    alert(data.message || 'An error occurred.');
                }
            }
        })
        .catch((err) => {
            console.error('Error toggling featured status:', err);
            const errMsg = err.message || 'An error occurred. Please try again.';
            if (window.showToast) {
                window.showToast(errMsg, 'error');
            } else {
                alert(errMsg);
            }
        })
        .finally(() => {
            buttonElement.disabled = false;
        });
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-testimony-featured-toggle]');

    if (button) {
        toggleFeatured(button.dataset.testimonyFeaturedToggle, button);
    }
});

window.Alpine.data('testimonyAdminList', testimonyAdminList);
