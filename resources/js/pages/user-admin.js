// --- Admin user list: filters and AJAX list, stats refresh after an import ---
// Imported by app.js on pages with [data-user-admin]; Alpine starts after this module has run.

function userAdminList() {
    return {
        isSubmitting: false,
        debounceTimer: null,
        showImportModal: false,
        init() {
            window.addEventListener('popstate', () => {
                this.updateList(window.location.href, false);
            });
        },
        updateList(url = null, pushState = true) {
            this.isSubmitting = true;
            if (!url) {
                const form = this.$refs.filterForm;
                if (!form) { console.error('filterForm ref not found!'); this.isSubmitting = false; return; }
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                url = `${form.action}?${params.toString()}`;
            }

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(res => {
                if (!res.ok) { console.error('AJAX error:', res.status, res.url); }
                return res.text();
            })
            .then(html => {
                const container = document.getElementById('users-list-container');
                if (container) {
                    container.innerHTML = html;
                    container.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-visible'));
                }
                if (pushState) window.history.pushState({}, '', url);
                this.isSubmitting = false;
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.isSubmitting = false;
            });
        },
        resetFilters() {
            this.$refs.filterForm.reset();
            this.$refs.filterForm.querySelectorAll('input, select').forEach(el => el.value = '');
            this.updateList();
        },
        submitDebounced() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.updateList(), 500);
        },

        refreshStats() {
            fetch('/users/stats', { headers: {'X-Requested-With':'XMLHttpRequest'} })
                .then(r => r.json())
                .then(d => {
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
