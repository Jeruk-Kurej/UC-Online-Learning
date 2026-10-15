// --- Public business directory: tabs, filters, province → city dropdown, AJAX list ---
// Imported by app.js on pages with [data-business-directory]; Alpine starts after this module has run.
// `state` comes from BusinessController::directoryState(): viewType, isPending, provinceCityMap, selectedProvince,
// selectedCity, availableCities.

import ajaxList from '../lib/ajax-list';

// --- Directory tab classes per part: [active, inactive] ---

const TAB_CLASSES = {
    card: [
        'border-uco-orange-400/80 -translate-y-1 shadow-[0_20px_40px_rgba(247,147,30,0.16),0_4px_12px_rgba(247,147,30,0.06)] ring-4 ring-uco-orange-500/10',
        'border-slate-200/80 hover:border-slate-300/80 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.05)] hover:ring-4 hover:ring-slate-100',
    ],
    glow: ['opacity-100', ''],
    sweep: ['animate-uco-shimmer', 'group-hover:animate-uco-shimmer-once'],
    orb: ['opacity-100 scale-125', ''],
    icon: [
        'bg-uco-orange-500 text-white scale-110 rotate-3 shadow-md shadow-uco-orange-500/25',
        'bg-slate-50 border border-slate-100 text-slate-400 group-hover:text-slate-600 group-hover:scale-105 group-hover:bg-slate-100/50',
    ],
    label: ['text-slate-900', 'text-slate-500 group-hover:text-slate-900'],
};

function businessDirectory(state) {
    return {
        ...ajaxList({ container: 'businesses-list-container', loadingKey: 'isLoading' }),
        showImportModal: false,
        viewType: state.viewType,
        isPending: state.isPending,
        availableCities: state.availableCities,

        // --- Tabs: Entrepreneurs is not highlighted while ?status=pending is open ---
        isActiveTab(type) {
            return this.viewType === type && !(type === 'entrepreneur' && this.isPending);
        },
        tabClass(type, part) {
            return TAB_CLASSES[part][this.isActiveTab(type) ? 0 : 1];
        },

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
            form.querySelectorAll('input[type=text], select').forEach((el) => (el.value = ''));
            this.updateList();
        },
    };
}

window.Alpine.data('businessDirectory', businessDirectory);
