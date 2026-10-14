// --- Public business directory: filters, province → city dropdown, AJAX list ---
// Imported by app.js on pages with [data-business-directory]; Alpine starts after this module has run.
// `state` comes from the page: isPending, provinceCityMap, selectedProvince, selectedCity.

function businessDirectory(state) {
    return {
        showImportModal: false, 
        isLoading: false,
        viewType: '',
        isPending: state.isPending,
        debounceTimer: null,

        // Dependent dropdown: cities of the chosen province
        provinceCityMap: state.provinceCityMap,
        selectedProvince: state.selectedProvince,
        selectedCity: state.selectedCity,

        get filteredCities() {
            if (!this.selectedProvince) return [];
            return this.provinceCityMap[this.selectedProvince] || [];
        },

        init() {
            window.addEventListener('popstate', () => {
                this.updateList(window.location.href, false);
            });
        },
        updateList(url = null, pushState = true, shouldScroll = false) {
            this.isLoading = true;
            if (!url) {
                const form = this.$refs.filterForm;
                if (!form) { console.error('filterForm ref not found!'); this.isLoading = false; return; }
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
                const container = document.getElementById('businesses-list-container');
                if (container) {
                    container.innerHTML = html;
                    container.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-visible'));
                }
                if (pushState) window.history.pushState({}, '', url);
                this.isLoading = false;
                if (shouldScroll) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this.isLoading = false;
            });
        },
        updateType(type, url, pending = false) {
            this.viewType = type;
            this.isPending = pending;
            this.updateList(url);
        },
        submitDebounced() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.updateList(), 500);
        },
        resetFilters() {
            this.selectedProvince = '';
            this.selectedCity = '';
            const form = this.$refs.filterForm;
            form.querySelectorAll('input[type=text], select').forEach(el => el.value = '');
            this.updateList();
        }
    };
}

window.Alpine.data('businessDirectory', businessDirectory);
