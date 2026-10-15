@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    @section('title', 'Business Directory')
    <div data-business-directory class="businesses-wrapper mx-auto max-w-[1600px] px-4 pb-8 sm:px-6 lg:px-8"
         data-view-type="{{ $viewType }}"
         x-data="businessDirectory(@js([
    'isPending' => request('status') === 'pending',
    'provinceCityMap' => $provinceCityMap,
    'selectedProvince' => (string) request('province'),
    'selectedCity' => (string) request('city'),
]))"
         x-init="viewType = $el.dataset.viewType || 'entrepreneur';
         init()"
         @ajax-pagination.window="updateList($event.detail.url, true, true)">
        {{-- --- Page Header --- --}}
        <section class="uco-hero-section-bleed reveal-on-scroll relative mb-8 overflow-hidden py-8 md:py-10">
            {{-- --- Background Effects with Gradient Mask --- --}}
            <div class="uco-hero-mask pointer-events-none absolute inset-0 overflow-hidden">
                <div class="uco-hero-mesh"></div>
            </div>
            <div class="relative z-10 mx-auto flex max-w-[1600px] flex-col gap-6 px-4 text-left sm:px-6 md:flex-row md:items-end md:justify-between lg:px-8">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-uco-orange-700">
                        UCO Directory
                    </span>
                    <h1 class="text-2xl font-extrabold text-gray-900 md:text-3xl">Business Directory</h1>
                    <p class="mt-1 text-base text-gray-500">Explore businesses and innovations from our student and alumni network.</p>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-2 text-xs font-black text-yellow-700">
                                <i class="bi bi-star-fill text-yellow-500"></i>
                                {{ $featuredBusinessCount }} Featured
                            </span>
                        @endif
                    </div>
                @endauth
            </div>
        </section>

        {{-- --- Entrepreneur / Intrapreneur Tabs --- --}}
        <div class="reveal-on-scroll mb-8 grid grid-cols-2 gap-3 delay-100 sm:flex sm:justify-center sm:gap-4" :class="{ 'opacity-50 pointer-events-none': isLoading }">
            {{-- --- Entrepreneurs Card --- --}}
            <a href="{{ route('businesses.index', ['view' => 'entrepreneur']) }}"
               @click.prevent="updateType('entrepreneur', $el.href)"
               class="group relative flex w-full select-none items-center justify-start gap-2 overflow-hidden rounded-xl border bg-white/90 px-3 py-3 backdrop-blur-md transition-all duration-300 sm:w-auto sm:min-w-[240px] sm:gap-4 sm:rounded-2xl sm:px-6 sm:py-4"
               :class="(viewType === 'entrepreneur' && !isPending) ?
               'border-uco-orange-400/80 -translate-y-1 shadow-[0_20px_40px_rgba(247,147,30,0.16),0_4px_12px_rgba(247,147,30,0.06)] ring-4 ring-uco-orange-500/10' :
               'border-slate-200/80 hover:border-slate-300/80 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.05)] hover:ring-4 hover:ring-slate-100'">

                {{-- --- Shimmer Overlay --- --}}
                <div class="absolute inset-0 bg-gradient-to-br from-uco-orange-500/5 to-yellow-500/5 opacity-0 transition-opacity duration-500"
                     :class="(viewType === 'entrepreneur' && !isPending) && 'opacity-100'"></div>

                {{-- --- Slow Repeating Shimmer Sweep / Hover Sweep --- --}}
                <div class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 skew-x-12 bg-gradient-to-r from-transparent via-white/80 to-transparent transition-all"
                     :class="(viewType === 'entrepreneur' && !isPending) ? 'animate-uco-shimmer' : 'group-hover:animate-uco-shimmer-once'"></div>

                {{-- --- Floating Light Orb inside (Active) --- --}}
                <div class="absolute -bottom-6 -right-6 h-16 w-16 scale-75 rounded-full bg-uco-orange-500/15 opacity-0 blur-md filter transition-all duration-500"
                     :class="(viewType === 'entrepreneur' && !isPending) && 'opacity-100 scale-125'"></div>

                {{-- --- Icon Box with dynamic hover & active transitions --- --}}
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl transition-all duration-300"
                     :class="(viewType === 'entrepreneur' && !isPending) ?
                     'bg-uco-orange-500 text-white scale-110 rotate-3 shadow-md shadow-uco-orange-500/25' :
                     'bg-slate-50 border border-slate-100 text-slate-400 group-hover:text-slate-600 group-hover:scale-105 group-hover:bg-slate-100/50'">
                    <i class="bi bi-briefcase text-lg"></i>
                </div>

                {{-- --- Typography Label details --- --}}
                <div class="flex min-w-0 flex-col text-left">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Directory</span>
                    <span class="truncate text-xs font-bold transition-colors duration-300 sm:text-sm"
                          :class="(viewType === 'entrepreneur' && !isPending) ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900'">
                        Entrepreneurs
                    </span>
                </div>
            </a>

            {{-- --- Intrapreneurs Card --- --}}
            <a href="{{ route('businesses.index', ['view' => 'intrapreneur']) }}"
               @click.prevent="updateType('intrapreneur', $el.href)"
               class="group relative flex w-full select-none items-center justify-start gap-2 overflow-hidden rounded-xl border bg-white/90 px-3 py-3 backdrop-blur-md transition-all duration-300 sm:w-auto sm:min-w-[240px] sm:gap-4 sm:rounded-2xl sm:px-6 sm:py-4"
               :class="viewType === 'intrapreneur'
                   ?
                   'border-uco-orange-400/80 -translate-y-1 shadow-[0_20px_40px_rgba(247,147,30,0.16),0_4px_12px_rgba(247,147,30,0.06)] ring-4 ring-uco-orange-500/10' :
                   'border-slate-200/80 hover:border-slate-300/80 hover:-translate-y-0.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_24px_rgba(0,0,0,0.05)] hover:ring-4 hover:ring-slate-100'">

                {{-- --- Shimmer Overlay --- --}}
                <div class="absolute inset-0 bg-gradient-to-br from-uco-orange-500/5 to-yellow-500/5 opacity-0 transition-opacity duration-500"
                     :class="viewType === 'intrapreneur' && 'opacity-100'"></div>

                {{-- --- Slow Repeating Shimmer Sweep / Hover Sweep --- --}}
                <div class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 skew-x-12 bg-gradient-to-r from-transparent via-white/80 to-transparent transition-all"
                     :class="viewType === 'intrapreneur' ? 'animate-uco-shimmer' : 'group-hover:animate-uco-shimmer-once'"></div>

                {{-- --- Floating Light Orb inside (Active) --- --}}
                <div class="absolute -bottom-6 -right-6 h-16 w-16 scale-75 rounded-full bg-uco-orange-500/15 opacity-0 blur-md filter transition-all duration-500"
                     :class="viewType === 'intrapreneur' && 'opacity-100 scale-125'"></div>

                {{-- --- Icon Box with dynamic hover & active transitions --- --}}
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl transition-all duration-300"
                     :class="viewType === 'intrapreneur'
                         ?
                         'bg-uco-orange-500 text-white scale-110 rotate-3 shadow-md shadow-uco-orange-500/25' :
                         'bg-slate-50 border border-slate-100 text-slate-400 group-hover:text-slate-600 group-hover:scale-105 group-hover:bg-slate-100/50'">
                    <i class="bi bi-building text-lg"></i>
                </div>

                {{-- --- Typography Label details --- --}}
                <div class="flex min-w-0 flex-col text-left">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Directory</span>
                    <span class="truncate text-xs font-bold transition-colors duration-300 sm:text-sm"
                          :class="viewType === 'intrapreneur' ? 'text-slate-900' : 'text-slate-500 group-hover:text-slate-900'">
                        Intrapreneurs
                    </span>
                </div>
            </a>
        </div>

        {{-- --- Filters --- --}}
        <div class="reveal-on-scroll mb-8 delay-150">
            <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" class="space-y-4" @submit.prevent="updateList()">
                <input type="hidden" name="view" :value="viewType">

                {{-- --- Search & Reset Row --- --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search business name..."
                               @input="submitDebounced()"
                               @keydown.enter.prevent="updateList()"
                               class="w-full rounded-md border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500"
                               autocomplete="off">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" x-show="!isLoading" title="Reset Filters" class="inline-flex h-[38px] w-[38px] items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-900">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isLoading" x-cloak class="inline-flex h-[38px] items-center justify-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 text-uco-orange-700 shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 hidden text-xs font-medium sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>

                {{-- --- Filters Row --- --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select name="category" @change="submitDebounced()" class="min-w-[150px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">All Categories</option>
                        @foreach ($categories as $type)
                            <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>

                    <select name="province"
                            x-model="selectedProvince"
                            @change="selectedCity = ''; submitDebounced()"
                            class="min-w-[150px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">All Provinces</option>
                        @foreach ($availableProvinces as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>

                    <select name="city"
                            x-model="selectedCity"
                            @change="submitDebounced()"
                            class="min-w-[150px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">All Cities</option>
                        <template x-for="city in (selectedProvince ? filteredCities : {{ json_encode($availableCities->values()) }})" :key="city">
                            <option :value="city" x-text="city"></option>
                        </template>
                    </select>
                </div>
            </form>
        </div>

        {{-- --- Scrollable Quick Tags for Categories --- --}}

        {{-- --- Grid --- --}}
        @if (auth()->user()?->isAdmin() && $errors->has('featured'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ $errors->first('featured') }}
            </div>
        @endif
        <div id="businesses-list-container-wrapper" class="relative min-h-[400px]">
            <div id="businesses-list-container" :class="isLoading ? 'opacity-60 pointer-events-none filter blur-[1px] transition-all duration-300' : 'transition-all duration-300'">
                @include('businesses.partials.list')
            </div>
        </div>

    </div>
</x-app-layout>
