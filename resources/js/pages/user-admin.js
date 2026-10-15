// --- Admin user list: filters and AJAX list, stats refresh after an import ---
// Imported by app.js on pages with [data-user-admin]; Alpine starts after this module has run.

import ajaxList from '../lib/ajax-list';

function userAdminList() {
    return {
        ...ajaxList({ container: 'users-list-container' }),
        showImportModal: false,

        refreshStats() {
            fetch('/users/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((r) => r.json())
                .then((d) => {
                    document.getElementById('stat-total').textContent = d.total;
                    document.getElementById('stat-entrepreneurs').textContent = d.entrepreneurs;
                    document.getElementById('stat-intrapreneurs').textContent = d.intrapreneurs;
                    document.getElementById('stat-alumni').textContent = d.alumni;
                    const statFeaturedTotal = document.getElementById('stat-featured-total');
                    const statFeaturedIntra = document.getElementById('stat-featured-intra');
                    const statFeaturedEntre = document.getElementById('stat-featured-entre');
                    if (statFeaturedTotal) statFeaturedTotal.textContent = d.featured;
                    if (statFeaturedIntra) statFeaturedIntra.textContent = d.featured_intrapreneurs;
                    if (statFeaturedEntre) statFeaturedEntre.textContent = d.featured_entrepreneurs;
                });
        },
    };
}

window.Alpine.data('userAdminList', userAdminList);
