// --- Filterable list that reloads its results over AJAX ---
// Spread into a page's Alpine data: { ...ajaxList({ container: 'users-list-container' }), ...page specifics }.
// The filters form is x-ref="filterForm"; the server answers X-Requested-With requests with the list partial.

export default function ajaxList({ container, loadingKey = 'isSubmitting', revealItems = true, restoreOnBack = true }) {
    return {
        [loadingKey]: false,
        debounceTimer: null,

        init() {
            if (restoreOnBack) {
                window.addEventListener('popstate', () => {
                    this.updateList(window.location.href, false);
                });
            }
        },

        updateList(url = null, pushState = true, shouldScroll = false) {
            this[loadingKey] = true;
            if (!url) {
                const form = this.$refs.filterForm;
                if (!form) { console.error('filterForm ref not found!'); this[loadingKey] = false; return; }
                const params = new URLSearchParams(new FormData(form));
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
                const list = document.getElementById(container);
                if (list) {
                    list.innerHTML = html;
                    if (revealItems) {
                        list.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-visible'));
                    }
                }
                if (pushState) window.history.pushState({}, '', url);
                this[loadingKey] = false;
                if (shouldScroll) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            })
            .catch(err => {
                console.error('Fetch failed:', err);
                this[loadingKey] = false;
            });
        },

        submitDebounced() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.updateList(), 500);
        },

        resetFilters() {
            this.$refs.filterForm.reset();
            this.$refs.filterForm.querySelectorAll('input, select').forEach(el => el.value = '');
            this.updateList();
        },
    };
}
