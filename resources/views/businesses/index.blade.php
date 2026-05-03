@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    <div class="businesses-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showImportModal: false }">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-3xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between text-left">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-uco-orange-700">
                        UCO Directory
                    </span>
                    <h1 class="text-3xl font-extrabold text-soft-gray-900 md:text-4xl">Business Directory</h1>
                    <p class="text-sm text-soft-gray-600 mt-1">Explore businesses and startups from our student and alumni network.</p>
                </div>

                @auth
                    @if (auth()->user()->isAdmin())
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-uco-yellow-50 border border-uco-yellow-200 text-uco-yellow-700 text-xs font-black rounded-xl">
                                <i class="bi bi-star-fill text-uco-yellow-500"></i>
                                {{ $featuredBusinessCount }}/8 Featured
                            </span>
                            <button @click="showImportModal = true" class="inline-flex items-center px-5 py-3 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                                <i class="bi bi-cloud-upload mr-2"></i>
                                Import CSV
                            </button>

                            <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-5 py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-black transition shadow-sm">
                                <i class="bi bi-plus-lg mr-2"></i>
                                Create Business
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </section>

        {{-- Entrepreneur / Intrapreneur Tabs --}}
        <div class="flex gap-2 mb-8">
            <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}" 
               class="px-6 py-3 rounded-xl font-bold text-sm transition {{ $viewType === 'entrepreneur' ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 border hover:bg-gray-50' }}">
                <i class="bi bi-briefcase mr-1"></i> Entrepreneurs
            </a>
            <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}" 
               class="px-6 py-3 rounded-xl font-bold text-sm transition {{ $viewType === 'intrapreneur' ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 border hover:bg-gray-50' }}">
                <i class="bi bi-building mr-1"></i> Intrapreneurs
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-4 mb-10 shadow-sm">
            <form action="{{ route('businesses.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
                <input type="hidden" name="view" value="{{ $viewType }}">
                <div class="flex-1 relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, category, or description..." 
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-uco-orange-500/20 focus:border-uco-orange-500 transition-all">
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <select name="category" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-uco-orange-500/20 transition-all text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $type)
                            <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <select name="province" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-uco-orange-500/20 transition-all text-sm hidden md:block">
                        <option value="">All Provinces</option>
                        @foreach($availableProvinces as $p)
                            <option value="{{ $p }}" {{ request('province') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                    <select name="city" class="bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-uco-orange-500/20 transition-all text-sm">
                        <option value="">All Cities</option>
                        @foreach($availableCities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-uco-orange-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-uco-orange-600 transition-all shadow-lg shadow-uco-orange-500/20 active:scale-95">
                    Apply Filters
                </button>
            </form>
        </div>

        {{-- Grid --}}
        @if(auth()->user()?->isAdmin() && $errors->has('featured'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ $errors->first('featured') }}
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($businesses as $business)
                <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden hover:border-uco-orange-200 hover:shadow-2xl transition-all duration-500 group relative">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="absolute top-3 right-3 z-10">
                                <form action="{{ route('businesses.toggle-featured', $business) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        title="{{ $business->is_featured ? 'Remove from featured' : 'Add to featured' }}"
                                        class="w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-sm border
                                            {{ $business->is_featured
                                                ? 'bg-uco-yellow-400 border-uco-yellow-500 text-white hover:bg-uco-yellow-500'
                                                : 'bg-white border-gray-200 text-gray-300 hover:text-uco-yellow-400 hover:border-uco-yellow-300' }}">
                                        <i class="bi bi-star-fill text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                    <a href="{{ $viewType === 'entrepreneur' ? route('businesses.show', $business) : route('intrapreneurs.show', $business) }}" class="block p-6">
                        <div class="flex items-start gap-5">
                            <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 group-hover:border-uco-orange-100 transition-colors overflow-hidden flex-shrink-0 shadow-inner">
                                @if($business->logo_url)
                                    <img src="{{ $business->logo_url }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                        <i class="bi bi-building text-3xl text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col gap-1 mb-3">
                                    <h3 class="font-black text-gray-900 text-lg leading-tight group-hover:text-uco-orange-500 transition-colors line-clamp-2">{{ $business->name }}</h3>
                                    @if($business->category)
                                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $business->category->name }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-4 leading-relaxed font-medium">
                                    {{ $viewType === 'entrepreneur' ? $business->description : $business->job_description }}
                                </p>
                                <div class="flex items-end justify-between gap-4 pt-4 border-t border-gray-50">
                                    <div class="space-y-3 min-w-0 flex-1">
                                        @if($viewType === 'entrepreneur' && $business->city)
                                            <span class="flex items-center gap-1 text-[11px] font-bold text-gray-400">
                                                <i class="bi bi-geo-alt-fill text-uco-orange-400"></i>
                                                {{ $business->city }}
                                            </span>
                                        @endif

                                        @if($business->user)
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                    @if($business->user->profile_photo_url)
                                                        <img src="{{ $business->user->profile_photo_url }}" alt="{{ $business->user->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-xs font-black text-gray-400">{{ strtoupper(substr($business->user->name, 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Founder Profile</p>
                                                    <p class="truncate text-[11px] font-bold text-gray-700 flex items-center gap-1">
                                                        <i class="bi bi-person-fill text-gray-400"></i>
                                                        {{ $business->user->name }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <i class="bi bi-arrow-right-circle-fill text-gray-200 group-hover:text-uco-orange-500 text-2xl transition-all group-hover:translate-x-1"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">No businesses found.</div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $businesses->links() }}
        </div>

        {{-- Import Modal --}}
        @if (auth()->user()?->isAdmin())
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
                                document.getElementById('biz_csv_file').files = e.dataTransfer.files;
                                document.getElementById('biz_file_name').textContent = file.name;
                            }
                        }
                     }">
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Import Businesses</h3>
                    <p class="text-sm text-gray-500 mb-6">Upload the UC Online Form Responses CSV file to sync profiles.</p>
                    
                    <form action="{{ route('businesses.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="border-2 border-dashed rounded-2xl p-10 text-center transition group"
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
                            <button type="button" @click="showImportModal = false" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition">Start Import</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

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
                        const res = await fetch(`/import-progress/${this.importId}`);
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
