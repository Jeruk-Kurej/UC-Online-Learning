@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    @section('title', 'Showcase Directory')
    <div class="businesses-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8" 
          x-data="{ 
             showImportModal: false, 
             isLoading: false,
             viewType: '{{ $viewType }}',
             isPending: {{ request('status') === 'pending' ? 'true' : 'false' }},
             debounceTimer: null,
             
             // Dependent Dropdown Logic
             provinceCityMap: {{ json_encode($provinceCityMap) }},
             selectedProvince: '{{ request('province') }}',
             selectedCity: '{{ request('city') }}',
             
             get filteredCities() {
                 if (!this.selectedProvince) return [];
                 return this.provinceCityMap[this.selectedProvince] || [];
             },

             init() {
                 window.addEventListener('popstate', () => {
                     this.updateList(window.location.href, false);
                 });
             },
             updateList(url = null, pushState = true, shouldScroll = false) {
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
                     if (shouldScroll) {
                         window.scrollTo({ top: 0, behavior: 'smooth' });
                     }
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
             },
             resetFilters() {
                 this.selectedProvince = '';
                 this.selectedCity = '';
                 const form = this.$refs.filterForm;
                 form.querySelectorAll('input[type=text], select').forEach(el => el.value = '');
                 this.updateList();
             }
          }"
          @ajax-pagination.window="updateList($event.detail.url, true, true)">
        {{-- Page Header --}}
        <section class="relative overflow-hidden rounded-xl border border-uco-orange-100 bg-white px-6 py-8 shadow-sm md:px-8 md:py-10 mb-8 reveal-on-scroll">
            <div class="uco-hero-mesh"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between text-left">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        UCO Directory
                    </span>
                    <h1 class="text-2xl font-extrabold text-gray-900 md:text-3xl">Showcase Directory</h1>
                    <p class="text-base text-gray-500 mt-1">Explore businesses and innovations from our student and alumni network.</p>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-black rounded-xl">
                                <i class="bi bi-star-fill text-yellow-500"></i>
                                {{ $featuredBusinessCount }} Featured
                            </span>
                        @endif
                    </div>
                @endauth
            </div>
        </section>

        {{-- Entrepreneur / Intrapreneur Tabs (Tactile Glass Cards) --}}
        <div class="grid grid-cols-2 sm:flex sm:justify-center gap-3 sm:gap-4 mb-8 reveal-on-scroll" :class="{ 'opacity-50 pointer-events-none': isLoading }" style="transition-delay: 100ms;">
            <!-- Entrepreneurs Card -->
            <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}" 
               @click.prevent="updateType('entrepreneur', $el.href)"
               class="relative overflow-hidden flex items-center gap-2 sm:gap-4 px-3 py-3 sm:px-6 sm:py-4 rounded-xl sm:rounded-2xl border transition-all duration-300 select-none group w-full sm:w-auto sm:min-w-[240px] justify-start bg-white/90 backdrop-blur-md"
               :class="(viewType === 'entrepreneur' && !isPending) 
                   ? 'border-uco-orange-400/80 -translate-y-1 shadow-[0_20px_40px_rgba(247,147,30,0.16),0_4px_12px_rgba(247,147,30,0.06)] ring-4 ring-uco-orange-500/10' 
                   : 'border-slate-200/80 hover:border-slate-300/80 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.05)] hover:ring-4 hover:ring-slate-100'">
                
                <!-- Shimmer Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-uco-orange-500/5 to-yellow-500/5 opacity-0 transition-opacity duration-500"
                     :class="(viewType === 'entrepreneur' && !isPending) && 'opacity-100'"></div>

                <!-- Slow Repeating Shimmer Sweep / Hover Sweep -->
                <div class="absolute inset-y-0 -left-1/3 w-1/3 bg-gradient-to-r from-transparent via-white/80 to-transparent skew-x-12 pointer-events-none transition-all"
                     :class="(viewType === 'entrepreneur' && !isPending) ? 'animate-uco-shimmer' : 'group-hover:animate-uco-shimmer-once'"></div>

                <!-- Floating Light Orb inside (Active) -->
                <div class="absolute -right-6 -bottom-6 w-16 h-16 rounded-full bg-uco-orange-500/15 filter blur-md opacity-0 transition-all duration-500 scale-75"
                     :class="(viewType === 'entrepreneur' && !isPending) && 'opacity-100 scale-125'"></div>

                <!-- Icon Box with dynamic hover & active transitions -->
                <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300 flex-shrink-0"
                     :class="(viewType === 'entrepreneur' && !isPending) 
                         ? 'bg-uco-orange-500 text-white scale-110 rotate-3 shadow-md shadow-uco-orange-500/25' 
                         : 'bg-slate-50 border border-slate-100 text-slate-400 group-hover:text-slate-600 group-hover:scale-105 group-hover:bg-slate-100/50'">
                    <i class="bi bi-briefcase text-lg"></i>
                </div>

                <!-- Typography Label details -->
                <div class="flex flex-col text-left min-w-0">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Directory</span>
                    <span class="text-xs sm:text-sm font-bold transition-colors duration-300 truncate"
                          :class="(viewType === 'entrepreneur' && !isPending) ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900'">
                        Entrepreneurs
                    </span>
                </div>
            </a>

            <!-- Intrapreneurs Card -->
            <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}" 
               @click.prevent="updateType('intrapreneur', $el.href)"
               class="relative overflow-hidden flex items-center gap-2 sm:gap-4 px-3 py-3 sm:px-6 sm:py-4 rounded-xl sm:rounded-2xl border transition-all duration-300 select-none group w-full sm:w-auto sm:min-w-[240px] justify-start bg-white/90 backdrop-blur-md"
               :class="viewType === 'intrapreneur' 
                   ? 'border-uco-orange-400/80 -translate-y-1 shadow-[0_20px_40px_rgba(247,147,30,0.16),0_4px_12px_rgba(247,147,30,0.06)] ring-4 ring-uco-orange-500/10' 
                   : 'border-slate-200/80 hover:border-slate-300/80 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.05)] hover:ring-4 hover:ring-slate-100'">
                
                <!-- Shimmer Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-uco-orange-500/5 to-yellow-500/5 opacity-0 transition-opacity duration-500"
                     :class="viewType === 'intrapreneur' && 'opacity-100'"></div>

                <!-- Slow Repeating Shimmer Sweep / Hover Sweep -->
                <div class="absolute inset-y-0 -left-1/3 w-1/3 bg-gradient-to-r from-transparent via-white/80 to-transparent skew-x-12 pointer-events-none transition-all"
                     :class="viewType === 'intrapreneur' ? 'animate-uco-shimmer' : 'group-hover:animate-uco-shimmer-once'"></div>

                <!-- Floating Light Orb inside (Active) -->
                <div class="absolute -right-6 -bottom-6 w-16 h-16 rounded-full bg-uco-orange-500/15 filter blur-md opacity-0 transition-all duration-500 scale-75"
                     :class="viewType === 'intrapreneur' && 'opacity-100 scale-125'"></div>

                <!-- Icon Box with dynamic hover & active transitions -->
                <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300 flex-shrink-0"
                     :class="viewType === 'intrapreneur' 
                         ? 'bg-uco-orange-500 text-white scale-110 rotate-3 shadow-md shadow-uco-orange-500/25' 
                         : 'bg-slate-50 border border-slate-100 text-slate-400 group-hover:text-slate-600 group-hover:scale-105 group-hover:bg-slate-100/50'">
                    <i class="bi bi-building text-lg"></i>
                </div>

                <!-- Typography Label details -->
                <div class="flex flex-col text-left min-w-0">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Directory</span>
                    <span class="text-xs sm:text-sm font-bold transition-colors duration-300 truncate"
                          :class="viewType === 'intrapreneur' ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900'">
                        Intrapreneurs
                    </span>
                </div>
            </a>
        </div>

        {{-- Filters --}}
        <div class="mb-8 reveal-on-scroll" style="transition-delay: 150ms;">
            <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" class="space-y-4" @submit.prevent="updateList()">
                <input type="hidden" name="view" value="{{ $viewType }}">
                
                {{-- Search & Reset Row --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search business name..."
                               @input="submitDebounced()"
                               @keydown.enter.prevent="updateList()"
                               class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                               autocomplete="off">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isLoading" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
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
                    <select name="category" @change="submitDebounced()" class="flex-1 min-w-[150px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($categories as $type)
                            <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    
                    <select name="province" 
                            x-model="selectedProvince"
                            @change="selectedCity = ''; submitDebounced()" 
                            class="flex-1 min-w-[150px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                        <option value="">All Provinces</option>
                        @foreach($availableProvinces as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    
                    <select name="city" 
                            x-model="selectedCity"
                            @change="submitDebounced()" 
                            class="flex-1 min-w-[150px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm cursor-pointer">
                        <option value="">All Cities</option>
                        <template x-for="city in (selectedProvince ? filteredCities : {{ json_encode($availableCities->values()) }})" :key="city">
                            <option :value="city" x-text="city"></option>
                        </template>
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
        <div id="businesses-list-container-wrapper" class="relative min-h-[400px]">
            {{-- Loading Overlay --}}
            <div x-show="isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 z-20 flex items-center justify-center bg-white/40 backdrop-blur-[2px] rounded-xl"
                 x-cloak>
                <div class="flex flex-col items-center gap-3 p-6 bg-white rounded-2xl shadow-xl border border-gray-100">
                    <div class="w-10 h-10 border-4 border-uco-orange-100 border-t-uco-orange-500 rounded-full animate-spin"></div>
                    <span class="text-xs font-black uppercase tracking-widest text-gray-400">Updating Directory</span>
                </div>
            </div>

            <div id="businesses-list-container" :class="isLoading ? 'filter blur-[1px] transition-all duration-300' : 'transition-all duration-300'">
                @include('businesses.partials.list')
            </div>
        </div>

    </div>
</x-app-layout>
