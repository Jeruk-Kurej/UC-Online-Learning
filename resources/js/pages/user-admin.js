// --- Admin user list: filters and AJAX list, Hide/Show and Feature without a reload, stats refresh after changes and imports ---
// Imported by app.js on pages with [data-user-admin]; Alpine starts after this module has run.

import ajaxList from '../lib/ajax-list';
import registerRowToggles from '../lib/row-toggle';

// --- Counter element id → key in the stats JSON (Admin\UserController::stats) ---

const STAT_COUNTERS = {
    'stat-total': 'total',
    'stat-entrepreneurs': 'entrepreneurs',
    'stat-intrapreneurs': 'intrapreneurs',
    'stat-alumni': 'alumni',
    'stat-featured-total': 'featured',
    'stat-featured-intra': 'featured_intrapreneurs',
    'stat-featured-entre': 'featured_entrepreneurs',
};

function userAdminList() {
    return {
        ...ajaxList({ container: 'users-list-container' }),
        showImportModal: false,

        refreshStats() {
            fetch(this.$root.dataset.statsUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => {
                    if (!response.ok) throw new Error(`Stats request failed: ${response.status}`);
                    return response.json();
                })
                .then((stats) => {
                    Object.entries(STAT_COUNTERS).forEach(([id, key]) => {
                        const counter = document.getElementById(id);
                        if (counter) counter.textContent = stats[key];
                    });
                })
                .catch((error) => console.error(error));
        },
    };
}

window.Alpine.data('userAdminList', userAdminList);
registerRowToggles();
