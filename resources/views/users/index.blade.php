<x-app-layout>
    <div class="users-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        @start-import.window="showImportModal = false"
        @close-import-modal.window="showImportModal = false"
        x-data="{
            isSubmitting: false,
            debounceTimer: null,
            showImportModal: false,
            init() {
                window.addEventListener('popstate', () => {
                    this.updateList(window.location.href, false);
                });
            },
            updateList(url = null, pushState = true, shouldScroll = false) {
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
                    if (shouldScroll) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
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
        @ajax-pagination.window="updateList($event.detail.url, true, true)"
        @ajax-update-list.window="updateList(window.location.href, false, false)">
        
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
                    <span class="inline-flex flex-col gap-0.5 px-3 py-2 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-semibold rounded-md min-w-[9.5rem]">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="bi bi-star-fill text-uco-yellow-500 flex-shrink-0"></i>
                            <span><span id="stat-featured-total">{{ $featuredUserCount }}</span> Featured</span>
                        </span>
                        <span class="text-[10px] font-bold text-uco-yellow-600/90 leading-tight pl-[1.35rem]">
                            <span class="text-green-600" id="stat-featured-intra">{{ $featuredIntrapreneurCount }}</span> Intra
                            <span class="text-uco-yellow-400 mx-0.5">·</span>
                            <span class="text-blue-600" id="stat-featured-entre">{{ $featuredEntrepreneurCount }}</span> Entre
                        </span>
                    </span>
                    @php /** @var \App\Models\User|null $authUser */ $authUser = auth()->user(); @endphp
                    @if($authUser && $authUser->isAdmin())
                    <button id="btn-open-import-modal" type="button" @click="showImportModal = true" class="btn-uco btn-uco-secondary px-4 py-2 text-sm">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>
                    @endif

                    @if($authUser && $authUser->isAdmin())
                        <a href="{{ route('users.create') }}" class="btn-uco btn-uco-primary px-4 py-2 text-sm">
                            <i class="bi bi-person-plus-fill mr-2"></i>
                            Create User
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8"
             @ajax-update-stats.window="
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
                    })">
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 100ms;">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Total Users</p>
                <p class="text-3xl font-bold text-gray-900" id="stat-total">{{ $totalUsers }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 150ms;">
                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Entrepreneurs</p>
                <p class="text-3xl font-bold text-blue-600" id="stat-entrepreneurs">{{ $totalEntrepreneurs }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 200ms;">
                <p class="text-[10px] font-bold text-green-500 uppercase tracking-wider mb-1">Intrapreneurs</p>
                <p class="text-3xl font-bold text-green-600" id="stat-intrapreneurs">{{ $totalIntrapreneurs }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll" style="transition-delay: 250ms;">
                <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider mb-1">Alumni</p>
                <p class="text-3xl font-bold text-purple-600" id="stat-alumni">{{ $totalAlumni }}</p>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-8 reveal-on-scroll" style="transition-delay: 300ms;">
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
                                window.dispatchEvent(new CustomEvent('ajax-update-stats'));
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
                            document.getElementById('csv_file').files = e.dataTransfer.files;
                            document.getElementById('file_name').textContent = file.name;
                        }
                    }
                 }"
                 @click.stop>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-2xl font-black text-gray-900">Import Data</h3>
                    <button type="button" @click="$dispatch('close-import-modal')" class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" @submit.prevent="$dispatch('start-import', $el)" class="space-y-6">
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
                        <button type="button" @click="$dispatch('close-import-modal')" class="btn-uco btn-uco-neutral flex-1 py-3">Cancel</button>
                        <button type="submit" class="btn-uco btn-uco-primary flex-1 py-3">Start Import</button>
                    </div>
                </form>
            </div>
        </div>

        @include('partials.import-progress')
    </div>

</x-app-layout>
