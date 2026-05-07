@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    <div class="businesses-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="{ 
            showImportModal: false, 
            isLoading: false,
            viewType: '{{ $viewType }}',
            isPending: {{ request('status') === 'pending' ? 'true' : 'false' }},
            debounceTimer: null,
            init() {
                window.addEventListener('popstate', () => {
                    this.updateList(window.location.href, false);
                });
            },
            updateList(url = null, pushState = true) {
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
            }
         }"
         @ajax-pagination.window="updateList($event.detail.url)">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-[2.5rem] border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between text-left">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        UCO Directory
                    </span>
                    <h1 class="text-3xl font-extrabold text-gray-900 md:text-4xl">Business Directory</h1>
                    <p class="text-sm text-gray-500 mt-1">Explore businesses and startups from our student and alumni network.</p>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-black rounded-xl">
                                <i class="bi bi-star-fill text-yellow-500"></i>
                                {{ $featuredBusinessCount }}/8 Featured
                            </span>
                            <button @click="showImportModal = true" class="inline-flex items-center px-5 py-3 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                                <i class="bi bi-cloud-upload mr-2"></i>
                                Import CSV
                            </button>
                        @endif

                        <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-5 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-black transition shadow-sm">
                            <i class="bi bi-plus-lg mr-2"></i>
                            Create Business
                        </a>
                    </div>
                @endauth
            </div>
        </section>

        {{-- Entrepreneur / Intrapreneur Tabs --}}
        <div class="flex gap-2 mb-8 flex-wrap reveal-on-scroll" :class="{ 'opacity-50 pointer-events-none': isLoading }" style="transition-delay: 100ms;">
            <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}" 
               @click.prevent="updateType('entrepreneur', $el.href)"
               class="px-6 py-3 rounded-xl font-bold text-sm transition"
               :class="viewType === 'entrepreneur' && !isPending ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 border hover:bg-gray-50'">
                <i class="bi bi-briefcase mr-1"></i> Entrepreneurs
            </a>
            <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}" 
               @click.prevent="updateType('intrapreneur', $el.href)"
               class="px-6 py-3 rounded-xl font-bold text-sm transition"
               :class="viewType === 'intrapreneur' ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 border hover:bg-gray-50'">
                <i class="bi bi-building mr-1"></i> Intrapreneurs
            </a>

            @if(auth()->user()?->isAdmin() && ($pendingCount > 0 || request('status') === 'pending'))
            <a href="{{ route('businesses.index', ['status' => 'pending']) }}" 
               @click.prevent="updateType('entrepreneur', $el.href, true)"
               class="px-6 py-3 rounded-xl font-bold text-sm transition"
               :class="isPending ? 'bg-red-600 text-white shadow-lg' : 'bg-white text-red-500 border border-red-100 hover:bg-red-50'">
                <i class="bi bi-clock-history mr-1"></i> Pending Approval
                <span class="ml-1 px-1.5 py-0.5" :class="isPending ? 'bg-white text-red-600' : 'bg-red-100 text-red-600'" rounded-md text-[10px]>{{ $pendingCount }}</span>
            </a>
            @endif
        </div>

        {{-- Filters --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-4 mb-6 shadow-sm reveal-on-scroll" style="transition-delay: 150ms;">
            <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center w-full">
                <input type="hidden" name="view" value="{{ $viewType }}">
                <div class="relative w-full">
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition-all duration-200 placeholder:text-gray-400 text-sm" 
                           placeholder="Search business name..."
                           @input="submitDebounced()"
                           autocomplete="off">
                </div>
                <div class="grid grid-cols-3 gap-2 w-full">
                    <select name="category" id="category_select" @change="submitDebounced()" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-gray-900/10 transition-all text-xs md:text-sm py-3 px-3 w-full">
                        <option value="">All Categories</option>
                        @foreach($categories as $type)
                            <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <select name="province" @change="submitDebounced()" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-gray-900/10 transition-all text-xs md:text-sm py-3 px-3 w-full">
                        <option value="">All Provinces</option>
                        @foreach($availableProvinces as $p)
                            <option value="{{ $p }}" {{ request('province') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <select name="city" @change="submitDebounced()" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-gray-900/10 transition-all text-xs md:text-sm py-3 px-3 w-full">
                        <option value="">All Cities</option>
                        @foreach($availableCities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
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
            </script>
        </div>

        {{-- Scrollable Quick Tags for Categories --}}

        {{-- Grid --}}
        @if(auth()->user()?->isAdmin() && $errors->has('featured'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ $errors->first('featured') }}
            </div>
        @endif
        <div id="businesses-list-container" :class="isLoading ? 'opacity-50 pointer-events-none transition-opacity duration-300' : 'transition-opacity duration-300'">
            @include('businesses.partials.list')
        </div>

        {{-- Import Modal --}}
        @if (auth()->user()?->isAdmin())
            <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8" @click.away="showImportModal = false"
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
                     }">
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Import Businesses</h3>
                    <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                    
                    <form action="{{ route('businesses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="border-2 border-dashed rounded-xl p-10 text-center transition group"
                             :class="isDragging ? 'border-gray-900 bg-orange-50' : 'border-gray-200 hover:border-gray-300'"
                             @dragover="handleDragOver"
                             @dragleave="handleDragLeave"
                             @drop="handleDrop">
                            <input type="file" name="file" required class="hidden" id="biz_csv_file" onchange="document.getElementById('biz_file_name').textContent = this.files[0].name">
                            <label for="biz_csv_file" class="cursor-pointer">
                                <i class="bi bi-file-earmark-spreadsheet text-4xl text-gray-300 group-hover:text-gray-900 transition"></i>
                                <p class="mt-4 text-sm font-bold text-gray-600" id="biz_file_name">Click to select or drag CSV/Excel file here</p>
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="showImportModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Start Import</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Import Progress Tracker --}}
        <div x-data="importProgress()" x-init="checkActiveImport().then(() => startPolling())" class="fixed bottom-6 right-6 z-50 w-96">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden" x-show="visible" x-transition>
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

                <div class="px-5 pb-4 pt-2">
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-3 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                             :class="status === 'completed' ? 'bg-emerald-500' : 'bg-gray-900'"
                             :style="'width: ' + percent + '%'"></div>
                    </div>

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
                    this.poll();
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
                                body: JSON.stringify({ type: 'business' })
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
                    fetch('/clear-active-import', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ type: 'business' })
                    });
                }
            }
        }
        </script>
    </div>
</x-app-layout>
