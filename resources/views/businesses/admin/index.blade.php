<x-app-layout>
    <div class="w-full max-w-[1600px] mx-auto py-8 px-4" 
         @start-import.window="showImportModal = false"
         @close-import-modal.window="showImportModal = false"
         data-view-type="{{ $viewType ?? 'entrepreneur' }}"
         x-data="{ 
            showImportModal: false,
            isSubmitting: false,
            debounceTimer: null,
            viewType: '',
            submitDebounced() {
                if (this.debounceTimer) clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.updateList(), 500);
            },
            resetFilters() {
                const currentType = this.viewType;
                this.$refs.filterForm.reset();
                this.$refs.filterForm.querySelectorAll('input, select').forEach(el => el.value = '');
                // Re-apply viewType after reset
                const typeInput = this.$refs.filterForm.querySelector('[name=type]');
                if (typeInput) typeInput.value = currentType;
                this.updateList();
            },
            switchType(type) {
                this.viewType = type;
                const typeInput = this.$refs.filterForm.querySelector('[name=type]');
                if (typeInput) typeInput.value = type;
                // Reset other filters
                const searchInput = this.$refs.filterForm.querySelector('[name=search]');
                if (searchInput) searchInput.value = '';
                this.updateList();
            },
            updateList(url = null, pushState = true, shouldScroll = false) {
                this.isSubmitting = true;
                if (!url) {
                    const form = this.$refs.filterForm;
                    if (!form) { this.isSubmitting = false; return; }
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData);
                    url = `${form.action}?${params.toString()}`;
                }

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                .then(res => res.text())
                .then(html => {
                    const container = document.getElementById('businesses-admin-list-container');
                    if (container) {
                        container.innerHTML = html;
                        container.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-visible'));
                    }
                    if (pushState) window.history.pushState({}, '', url);
                    this.isSubmitting = false;
                    if (shouldScroll) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(err => {
                    console.error('Fetch failed:', err);
                    this.isSubmitting = false;
                });
            }
         }"
         x-init="viewType = $el.dataset.viewType || 'entrepreneur'"
         @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Business Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Review and manage business submissions from students.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto">
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-semibold rounded-md flex-grow sm:flex-initial">
                        <i class="bi bi-star-fill text-uco-yellow-500"></i>
                        <span><span class="featured-count">{{ $featuredBusinessesCount }}</span> Featured</span>
                    </span>
                    <button type="button" @click="showImportModal = true" class="btn-uco btn-uco-secondary px-4 py-2 text-sm flex-grow sm:flex-initial justify-center">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>

                    <a href="{{ route('businesses.create') }}" class="btn-uco btn-uco-primary px-4 py-2 text-sm flex-grow sm:flex-initial justify-center">
                        <i class="bi bi-plus-lg mr-2"></i>
                        Create Business
                    </a>
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    <span x-show="viewType === 'entrepreneur'">Total Businesses</span>
                    <span x-show="viewType === 'intrapreneur'">Total Career Profiles</span>
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-wider mb-1">
                    <span x-show="viewType === 'entrepreneur'">Approved</span>
                    <span x-show="viewType === 'intrapreneur'">Active / Visible</span>
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $approvedBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 200ms;">
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mb-1">
                    <span x-show="viewType === 'entrepreneur'">Pending Approval</span>
                    <span x-show="viewType === 'intrapreneur'">Hidden</span>
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-amber-600">{{ $pendingBusinesses }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 250ms;">
                <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-1">
                    <span x-show="viewType === 'entrepreneur'">Rejected / Revision</span>
                    <span x-show="viewType === 'intrapreneur'">— N/A —</span>
                </p>
                <p class="text-2xl sm:text-3xl font-bold text-red-600">{{ $rejectedBusinesses }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-8 reveal-on-scroll" style="transition-delay: 300ms;">
            <form x-ref="filterForm" action="{{ route('businesses.admin') }}" method="GET" @submit.prevent="updateList()">
                {{-- Hidden type input (kept in sync by switchType()) --}}
                <input type="hidden" name="type" :value="viewType">

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            :placeholder="viewType === 'intrapreneur' ? 'Search company or owner name...' : 'Search business or owner name...'"
                            @input="submitDebounced()"
                            @keydown.enter.prevent="updateList()"
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    {{-- Filters & Reset Button --}}
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                        {{-- Type dropdown --}}
                        <select x-model="viewType"
                                @change="switchType($event.target.value)"
                                class="flex-1 sm:flex-initial min-w-[175px] border-gray-300 bg-white rounded-md pl-3 pr-8 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                            <option value="entrepreneur">Type: Entrepreneur</option>
                            <option value="intrapreneur">Type: Intrapreneur</option>
                        </select>

                        {{-- Status filter --}}
                        <select name="status" @change="updateList()"
                                class="flex-1 sm:flex-initial min-w-[145px] border-gray-300 bg-white rounded-md pl-3 pr-8 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                            <option value="">Status: All</option>
                            <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="need_revision" {{ ($status ?? '') === 'need_revision' ? 'selected' : '' }}>Need Revision</option>
                        </select>

                        {{-- Featured filter (entrepreneur only) --}}
                        <template x-if="viewType === 'entrepreneur'">
                            <select name="featured" @change="updateList()"
                                    class="flex-1 sm:flex-initial min-w-[145px] border-gray-300 bg-white rounded-md pl-3 pr-8 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                                <option value="">Featured: All</option>
                                <option value="yes" {{ ($featured ?? '') === 'yes' ? 'selected' : '' }}>Featured</option>
                                <option value="no" {{ ($featured ?? '') === 'no' ? 'selected' : '' }}>Not Featured</option>
                            </select>
                        </template>

                        {{-- Reset --}}
                        <button type="button" @click="resetFilters()" title="Reset Filters"
                                class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm flex-shrink-0">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>

                        {{-- Loading spinner --}}
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.2" stroke-width="3"></circle>
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            <span class="ml-2 text-xs font-medium hidden sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script>
            // Intercept pagination clicks
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination-ajax a');
                if (link) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('ajax-pagination', {
                        detail: { url: link.href }
                    }));
                }
            });
        </script>

        <div id="businesses-admin-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('businesses.admin.partials.list')
        </div>

    {{-- Status Modal --}}
    <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeStatusModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="statusForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="bi bi-gear-fill text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Update Business Status
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4" id="businessNameDisplay"></p>
                                    
                                    <div class="mb-4">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">New Status</label>
                                        <select name="status" id="statusSelect" onchange="toggleReasonField()"
                                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="pending">Pending Approval</option>
                                            <option value="approved">Approved</option>
                                            <option value="need_revision">Need Revision</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>

                                    <div id="reasonField" class="hidden">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-2">Feedback / Reason</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                                  class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500"
                                                  placeholder="Provide specific feedback or reason for rejection..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:items-center sm:gap-2">
                        <button type="submit" class="w-full sm:w-auto btn-uco btn-uco-primary">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeStatusModal()" class="mt-3 sm:mt-0 w-full sm:w-auto btn-uco btn-uco-neutral">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Intercept featured toggle form submissions to handle them via AJAX
        document.addEventListener('submit', function(e) {
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
                        'X-CSRF-TOKEN': tokenInput.value
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const isFeaturedNow = data.message.includes('now featured');
                        const tooltipEl = button.querySelector('.tooltip-text');
                        if (isFeaturedNow) {
                            button.className = "relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500";
                            if (tooltipEl) tooltipEl.textContent = 'Remove featured';
                        } else {
                            button.className = "relative group w-7 h-7 rounded-full inline-flex items-center justify-center transition-all border bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300";
                            if (tooltipEl) tooltipEl.textContent = 'Make featured';
                        }
                        
                        // Update featured badge count in header
                        const countEl = document.querySelector('.featured-count');
                        if (countEl) {
                            let currentCount = parseInt(countEl.textContent, 10) || 0;
                            countEl.textContent = isFeaturedNow ? currentCount + 1 : Math.max(0, currentCount - 1);
                        }
                        
                        // Dispatch global toast message
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: data.message, type: 'success' }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: data.message || 'Error occurred', type: 'error' }
                        }));
                    }
                })
                .catch(err => {
                    console.error('Featured toggle failed:', err);
                });
            }
        });
    </script>

        {{-- Import Modal --}}
        @if (auth()->user()?->isAdmin())
        <div x-show="showImportModal"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="showImportModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-8"
                 x-transition:enter="transition ease-out duration-200 transform"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150 transform"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 x-data="{
                    isDragging: false,
                    handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                    handleDragLeave() { this.isDragging = false; },
                    handleDrop(e) {
                        e.preventDefault();
                        this.isDragging = false;
                        if (e.dataTransfer.files.length > 0) {
                            const file = e.dataTransfer.files[0];
                            document.getElementById('biz_csv_file').files = e.dataTransfer.files;
                            document.getElementById('biz_file_name').textContent = file.name;
                        }
                    }
                 }"
                 @click.stop>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-black text-gray-900">Import Businesses</h3>
                    <button type="button" @click="$dispatch('close-import-modal')" class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>

                <form action="{{ route('businesses.import') }}" method="POST" enctype="multipart/form-data" @submit.prevent="$dispatch('start-import', $el)" class="space-y-6">
                    @csrf
                    <div class="border-2 border-dashed rounded-lg p-10 text-center transition group"
                         :class="isDragging ? 'border-uco-orange-500 bg-orange-50' : 'border-gray-200 hover:border-uco-orange-300'"
                         @dragover="handleDragOver"
                         @dragleave="handleDragLeave"
                         @drop="handleDrop">
                        <input type="file" name="file" required class="hidden" id="biz_csv_file" onchange="document.getElementById('biz_file_name').textContent = this.files[0].name">
                        <label for="biz_csv_file" class="cursor-pointer">
                            <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-uco-orange-500 transition"></i>
                            <p class="mt-4 text-sm font-bold text-gray-600" id="biz_file_name">Click to select or drag CSV/Excel file here</p>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="$dispatch('close-import-modal')" class="btn-uco btn-uco-neutral flex-1 py-3">Cancel</button>
                        <button type="submit" class="btn-uco btn-uco-primary flex-1 py-3">Start Import</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @include('partials.import-progress')
    </div>
</x-app-layout>
