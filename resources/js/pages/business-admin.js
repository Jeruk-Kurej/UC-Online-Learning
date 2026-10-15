// --- Admin business list: filters and AJAX list, status modal, featured toggle ---
// Imported by app.js on pages with [data-business-admin]; Alpine starts after this module has run.

import ajaxList from '../lib/ajax-list';

function businessAdminList() {
    return {
        ...ajaxList({ container: 'businesses-admin-list-container', restoreOnBack: false }),
        showImportModal: false,
        viewType: '',
        resetFilters() {
            const currentType = this.viewType;
            this.$refs.filterForm.reset();
            this.$refs.filterForm.querySelectorAll('input, select').forEach((el) => (el.value = ''));
            const typeInput = this.$refs.filterForm.querySelector('[name=type]');
            if (typeInput) typeInput.value = currentType;
            this.updateList();
        },
        switchType(type) {
            this.viewType = type;
            const typeInput = this.$refs.filterForm.querySelector('[name=type]');
            if (typeInput) typeInput.value = type;
            const searchInput = this.$refs.filterForm.querySelector('[name=search]');
            if (searchInput) searchInput.value = '';
            this.updateList();
        },
    };
}

function openStatusModal(data) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const nameDisplay = document.getElementById('businessNameDisplay');
    const statusSelect = document.getElementById('statusSelect');
    const reasonArea = document.getElementById('rejection_reason');

    if (data.type === 'intrapreneur') {
        form.action = `/admin/intrapreneurs/${data.id}/status`;
    } else {
        form.action = `/admin/business/${data.id}/status`;
    }
    nameDisplay.innerText = `Updating: ${data.name}`;
    statusSelect.value = data.status;
    reasonArea.value = data.reason || '';

    modal.classList.remove('hidden');
    toggleReasonField();
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

function toggleReasonField() {
    const status = document.getElementById('statusSelect').value;
    const reasonField = document.getElementById('reasonField');
    if (status === 'rejected' || status === 'need_revision') {
        reasonField.classList.remove('hidden');
    } else {
        reasonField.classList.add('hidden');
    }
}

// --- Featured toggle over AJAX ---
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form && form.classList.contains('toggle-featured-form')) {
        e.preventDefault();
        const button = form.querySelector('button');
        const tokenInput = form.querySelector('input[name="_token"]');
        if (!button || !tokenInput) return;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': tokenInput.value,
            },
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    const isFeaturedNow = data.message.includes('now featured');
                    const tooltipEl = button.querySelector('.tooltip-text');
                    if (isFeaturedNow) {
                        button.className =
                            'relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500';
                        if (tooltipEl) tooltipEl.textContent = 'Remove featured';
                    } else {
                        button.className =
                            'relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300';
                        if (tooltipEl) tooltipEl.textContent = 'Make featured';
                    }

                    const countEl = document.querySelector('.featured-count');
                    if (countEl) {
                        let currentCount = parseInt(countEl.textContent, 10) || 0;
                        countEl.textContent = isFeaturedNow ? currentCount + 1 : Math.max(0, currentCount - 1);
                    }

                    window.dispatchEvent(
                        new CustomEvent('notify', {
                            detail: { message: data.message, type: 'success' },
                        }),
                    );
                } else {
                    window.dispatchEvent(
                        new CustomEvent('notify', {
                            detail: { message: data.message || 'Error occurred', type: 'error' },
                        }),
                    );
                }
            })
            .catch((err) => {
                console.error('Featured toggle failed:', err);
            });
    }
});

// --- Wiring (markup uses data attributes instead of inline handlers) ---

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-status-modal]');
    if (opener) {
        openStatusModal(JSON.parse(opener.dataset.statusModal));
    }

    if (event.target.closest('[data-status-modal-close]')) {
        closeStatusModal();
    }
});

document.addEventListener('change', (event) => {
    if (event.target.closest('[data-status-select]')) {
        toggleReasonField();
    }
});

window.Alpine.data('businessAdminList', businessAdminList);
