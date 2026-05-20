<x-app-layout>
    <div class="users-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{
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
            }
        }"
        @ajax-pagination.window="updateList($event.detail.url)"
        @ajax-update-list.window="updateList()">
        
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-gray-200 bg-white px-6 py-6 shadow-sm md:px-8 mb-8 reveal-on-scroll">
            <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">User Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Manage student and alumni profiles synced from the central database.</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-semibold rounded-md">
                        <i class="bi bi-star-fill text-uco-yellow-500"></i>
                        {{ $featuredUserCount }}/4 Featured
                    </span>
                    <button @click="showImportModal = true" class="btn-uco btn-uco-secondary px-4 py-2 text-sm">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>

                    @if(auth()->user() && auth()->user()->isAdmin())
                        <a href="{{ route('users.create') }}" class="btn-uco btn-uco-primary px-4 py-2 text-sm">
                            <i class="bi bi-person-plus-fill mr-2"></i>
                            Create User
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Entrepreneurs</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalEntrepreneurs }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 200ms;">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-wider mb-1">Intrapreneurs</p>
                <p class="text-3xl font-bold text-green-600">{{ $totalIntrapreneurs }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 250ms;">
                <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider mb-1">Alumni</p>
                <p class="text-3xl font-bold text-purple-600">{{ $totalAlumni }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 mb-8 shadow-sm reveal-on-scroll" style="transition-delay: 300ms;">
            <form x-ref="filterForm" action="{{ route('users.index') }}" method="GET" class="space-y-4"
                @submit.prevent="updateList()">
                
                {{-- Search & Reset Row --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name, email, username, or NIS..."
                            @input="submitDebounced()"
                            @keydown.enter.prevent="updateList()"
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.2" stroke-width="3"></circle>
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            <span class="ml-2 text-xs font-medium hidden sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>

                {{-- Filters Row --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select id="sort_name" name="sort_name" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Sort Name: Default</option>
                        <option value="asc" @selected(request('sort_name') === 'asc')>A → Z</option>
                        <option value="desc" @selected(request('sort_name') === 'desc')>Z → A</option>
                    </select>

                    <select id="sort_year" name="sort_year" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Student Year: Default</option>
                        <option value="desc" @selected(request('sort_year') === 'desc')>Latest → Earliest</option>
                        <option value="asc" @selected(request('sort_year') === 'asc')>Earliest → Latest</option>
                    </select>

                    <select id="student_status" name="student_status" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Status: All</option>
                        <option value="active" @selected(request('student_status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('student_status') === 'inactive')>Inactive</option>
                        <option value="cuti" @selected(request('student_status') === 'cuti')>Cuti</option>
                        <option value="alumni" @selected(request('student_status') === 'alumni')>Alumni</option>
                    </select>

                    <select id="current_status" name="current_status" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Category: All</option>
                        <option value="Entrepreneur" @selected(request('current_status') === 'Entrepreneur')>Entrepreneur</option>
                        <option value="Intrapreneur" @selected(request('current_status') === 'Intrapreneur')>Intrapreneur</option>
                    </select>

                    <select id="major" name="major" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Major: All</option>
                        @foreach($availableMajors as $majorOption)
                            <option value="{{ $majorOption }}" @selected(request('major') === $majorOption)>{{ $majorOption }}</option>
                        @endforeach
                    </select>

                    <select id="year_of_enrollment" name="year_of_enrollment" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Year: All</option>
                        @foreach($availableEnrollmentYears as $yearOption)
                            <option value="{{ $yearOption }}" @selected(request('year_of_enrollment') === $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
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

                // Intercept toggle actions to make them seamless
                document.addEventListener('submit', function(e) {
                    const form = e.target;
                    const action = form.action;
                    if (action && (action.includes('/toggle-featured') || action.includes('/toggle-status'))) {
                        e.preventDefault();
                        
                        fetch(action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.dispatchEvent(new CustomEvent('ajax-update-list'));
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
                            console.error('Toggle action failed:', err);
                        });
                    }
                });
            </script>
        </div>

        {{-- Flash messages removed - handled by global toasts --}}
        @if($errors->has('featured'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ $errors->first('featured') }}
            </div>
        @endif

        {{-- Users List Container --}}
        <div id="users-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('users.partials.list')
        </div>

        {{-- Import Modal --}}
        <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-8" @click.away="showImportModal = false"
                 x-data="{
                    isDragging: false,
                    handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                    handleDragLeave() { this.isDragging = false; },
                    handleDrop(e) {
                        e.preventDefault();
                        this.isDragging = false;
                        if (e.dataTransfer.files.length > 0) {
                            const file = e.dataTransfer.files[0];
                            document.getElementById('csv_file').files = e.dataTransfer.files;
                            document.getElementById('file_name').textContent = file.name;
                        }
                    }
                 }">
                <h3 class="text-2xl font-black text-gray-900 mb-2">Import Data</h3>
                <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="border-2 border-dashed rounded-lg p-10 text-center transition group"
                         :class="isDragging ? 'border-uco-orange-500 bg-orange-50' : 'border-gray-200 hover:border-uco-orange-300'"
                         @dragover="handleDragOver"
                         @dragleave="handleDragLeave"
                         @drop="handleDrop">
                        <input type="file" name="file" required class="hidden" id="csv_file" onchange="document.getElementById('file_name').textContent = this.files[0].name">
                        <label for="csv_file" class="cursor-pointer">
                            <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-uco-orange-500 transition"></i>
                            <p class="mt-4 text-sm font-bold text-gray-600" id="file_name">Click to select or drag CSV/Excel file here</p>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showImportModal = false" class="btn-uco btn-uco-neutral flex-1 py-3">Cancel</button>
                        <button type="submit" class="btn-uco btn-uco-primary flex-1 py-3">Start Import</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Import Progress Tracker (Always rendered, fetches active import on load) --}}
        <div x-data="importProgress()" x-init="checkActiveImport().then(() => startPolling())" class="fixed bottom-6 right-6 z-50 w-96">
            <div class="bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden" x-show="visible" x-transition>
                {{-- Header --}}
                <div class="px-5 py-4 flex items-center justify-between" :class="status === 'completed' ? 'bg-emerald-50' : 'bg-gray-50'">
                    <div class="flex items-center gap-3">
                        <template x-if="status !== 'completed'">
                            <div class="w-5 h-5 border-2 border-gray-400 border-t-gray-900 rounded-full animate-spin"></div>
                        </template>
                        <template x-if="status === 'completed'">
                            <i class="bi bi-check-circle-fill text-emerald-500 text-xl"></i>
                        </template>
                        <span class="font-bold text-sm text-gray-900" x-text="status === 'completed' ? 'Import Complete!' : 'Importing...'"></span>
                    </div>
                    <button @click="dismiss()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                {{-- Progress Bar --}}
                <div class="px-5 pb-4 pt-2">
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                             :class="status === 'completed' ? 'bg-emerald-500' : 'bg-gray-900'"
                             :style="'width: ' + percent + '%'"></div>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-gray-50 rounded-xl p-2">
                            <p class="text-xs text-gray-500">Processed</p>
                            <p class="text-sm font-black text-gray-900" x-text="current + '/' + total"></p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-2">
                            <p class="text-xs text-emerald-600">Success</p>
                            <p class="text-sm font-black text-emerald-700" x-text="success"></p>
                        </div>
                        <div class="bg-amber-50 rounded-xl p-2">
                            <p class="text-xs text-amber-600">Skipped</p>
                            <p class="text-sm font-black text-amber-700" x-text="skipped"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function importProgress() {
            return {
                importId: '',
                status: 'processing',
                total: 0,
                current: 0,
                success: 0,
                skipped: 0,
                percent: 0,
                visible: false,
                polling: null,

                async checkActiveImport() {
                    try {
                        const res = await fetch('/import-progress/check', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });
                        const data = await res.json();
                        
                        if (data.importId) {
                            this.importId = data.importId;
                            this.visible = true;
                        }
                    } catch (e) {
                        console.error('Check active import error:', e);
                    }
                },

                startPolling() {
                    if (!this.importId) return;
                    
                    this.poll(); // immediate first call
                    this.polling = setInterval(() => this.poll(), 2000);
                },

                async poll() {
                    if (!this.importId) return;
                    
                    try {
                        const res = await fetch(`/import-progress/${this.importId}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();

                        this.status = data.status || 'processing';
                        this.total = data.total || 0;
                        this.current = data.current || 0;
                        this.success = data.success || 0;
                        this.skipped = data.skipped || 0;
                        this.percent = this.total > 0 ? Math.min(100, Math.round((this.current / this.total) * 100)) : 0;

                        if (this.status === 'completed' || this.status === 'failed') {
                            clearInterval(this.polling);
                            fetch('/clear-active-import', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ type: 'user' })
                            }).then(() => {
                                setTimeout(() => window.location.reload(), 3000);
                            });
                        }
                    } catch (e) {
                        console.error('Progress poll error:', e);
                    }
                },

                dismiss() {
                    this.visible = false;
                    this.importId = '';
                    clearInterval(this.polling);
                    // Clear server-side session
                    fetch('/clear-active-import', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ type: 'user' })
                    });
                }
            }
        }
        </script>
    </div>

</x-app-layout>
