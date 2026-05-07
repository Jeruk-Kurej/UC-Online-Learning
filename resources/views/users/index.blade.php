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
        @ajax-pagination.window="updateList($event.detail.url)">
        
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-[2.5rem] border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        Admin Portal
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">User Management</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Manage student and alumni profiles synced from the central database.</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-4 py-3 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-black rounded-2xl">
                        <i class="bi bi-star-fill text-uco-yellow-500"></i>
                        {{ $featuredUserCount }}/4 Featured
                    </span>
                    <button @click="showImportModal = true" class="inline-flex items-center px-6 py-4 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-2xl hover:bg-gray-50 transition shadow-sm">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>

                    @if(auth()->user() && auth()->user()->isAdmin())
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-6 py-4 bg-gray-900 text-white text-sm font-bold rounded-2xl hover:bg-black transition shadow-sm">
                            <i class="bi bi-person-plus-fill mr-2"></i>
                            Create User
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total Users</p>
                <p class="text-4xl font-black text-gray-900">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-1">Entrepreneurs</p>
                <p class="text-4xl font-black text-blue-600">{{ $totalEntrepreneurs }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 reveal-on-scroll" style="transition-delay: 200ms;">
                <p class="text-[10px] font-black text-green-400 uppercase tracking-[0.2em] mb-1">Intrapreneurs</p>
                <p class="text-4xl font-black text-green-600">{{ $totalIntrapreneurs }}</p>
            </div>
            <div class="bg-white border rounded-[2.5rem] p-8 shadow-sm hover:shadow-xl transition-all duration-500 reveal-on-scroll" style="transition-delay: 250ms;">
                <p class="text-[10px] font-black text-purple-400 uppercase tracking-[0.2em] mb-1">Alumni</p>
                <p class="text-4xl font-black text-purple-600">{{ $totalAlumni }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white border rounded-[2.5rem] p-6 mb-8 shadow-sm reveal-on-scroll" style="transition-delay: 300ms;">
            <form x-ref="filterForm" action="{{ route('users.index') }}" method="GET" class="space-y-4"
                @submit.prevent="updateList()">
                <div class="flex flex-col gap-4 lg:flex-row">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name, email, username, or NIS..."
                        @input="submitDebounced()"
                        @keydown.enter.prevent="updateList()"
                        class="flex-1 border-gray-200 bg-gray-50 rounded-2xl px-6 py-4 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all"
                    >
                    <div class="flex gap-3">
                        <button type="button" @click="resetFilters()" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-bold hover:bg-gray-50 transition whitespace-nowrap">
                            Reset
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-uco-orange-700 bg-uco-orange-50 border border-uco-orange-200 rounded-2xl">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.2" stroke-width="3"></circle>
                                <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                            </svg>
                            Updating results...
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                    <div>
                        <label for="sort_name" class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Sort Name</label>
                        <select id="sort_name" name="sort_name" @change="updateList()" class="w-full border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                            <option value="">Default</option>
                            <option value="asc" @selected(request('sort_name') === 'asc')>A → Z</option>
                            <option value="desc" @selected(request('sort_name') === 'desc')>Z → A</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort_year" class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Sort Angkatan</label>
                        <select id="sort_year" name="sort_year" @change="updateList()" class="w-full border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                            <option value="">Default</option>
                            <option value="desc" @selected(request('sort_year') === 'desc')>Terbaru → Terlama</option>
                            <option value="asc" @selected(request('sort_year') === 'asc')>Terlama → Terbaru</option>
                        </select>
                    </div>

                    <div>
                        <label for="student_status" class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</label>
                        <select id="student_status" name="student_status" @change="updateList()" class="w-full border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                            <option value="">Semua Status</option>
                            <option value="active" @selected(request('student_status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('student_status') === 'inactive')>Inactive</option>
                            <option value="cuti" @selected(request('student_status') === 'cuti')>Cuti</option>
                            <option value="alumni" @selected(request('student_status') === 'alumni')>Alumni</option>
                        </select>
                    </div>

                    <div>
                        <label for="major" class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jurusan</label>
                        <select id="major" name="major" @change="updateList()" class="w-full border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                            <option value="">Semua Jurusan</option>
                            @foreach($availableMajors as $majorOption)
                                <option value="{{ $majorOption }}" @selected(request('major') === $majorOption)>{{ $majorOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="year_of_enrollment" class="block mb-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tahun Angkatan</label>
                        <select id="year_of_enrollment" name="year_of_enrollment" @change="updateList()" class="w-full border-gray-200 bg-gray-50 rounded-2xl px-4 py-3 focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all">
                            <option value="">Semua Tahun</option>
                            @foreach($availableEnrollmentYears as $yearOption)
                                <option value="{{ $yearOption }}" @selected(request('year_of_enrollment') === $yearOption)>{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
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
            </script>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif
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
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8" @click.away="showImportModal = false"
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
                    <div class="border-2 border-dashed rounded-2xl p-10 text-center transition group"
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
                        <button type="button" @click="showImportModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Start Import</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Import Progress Tracker (Always rendered, fetches active import on load) --}}
        <div x-data="importProgress()" x-init="checkActiveImport().then(() => startPolling())" class="fixed bottom-6 right-6 z-50 w-96">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden" x-show="visible" x-transition>
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
