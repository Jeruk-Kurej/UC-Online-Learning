// --- Public business directory: filters, province → city dropdown, AJAX list ---
// Imported by app.js on pages with [data-business-directory]; Alpine starts after this module has run.
// `state` comes from the page: isPending, provinceCityMap, selectedProvince, selectedCity.

import ajaxList from '../lib/ajax-list';

function businessDirectory(state) {
    return {
        ...ajaxList({ container: 'businesses-list-container', loadingKey: 'isLoading' }),
        showImportModal: false,
        viewType: '',
        isPending: state.isPending,

        // --- Dependent dropdown: cities of the chosen province ---
        provinceCityMap: state.provinceCityMap,
        selectedProvince: state.selectedProvince,
        selectedCity: state.selectedCity,

        get filteredCities() {
            if (!this.selectedProvince) return [];
            return this.provinceCityMap[this.selectedProvince] || [];
        },

        updateType(type, url, pending = false) {
            this.viewType = type;
            this.isPending = pending;
            this.updateList(url);
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
