<x-app-layout>
    @section('title', 'Business Directory')
    <div data-business-directory class="businesses-wrapper mx-auto max-w-[1600px] px-4 pb-8 sm:px-6 lg:px-8"
         x-data="businessDirectory(@js($directoryState))" x-init="init()"
         @ajax-pagination.window="updateList($event.detail.url, true, true)">
        {{-- --- Page Header --- --}}
        <section class="uco-hero-section-bleed reveal-on-scroll relative mb-8 overflow-hidden py-8 md:py-10">
            {{-- --- Background Effects with Gradient Mask --- --}}
            <div class="uco-hero-mask pointer-events-none absolute inset-0 overflow-hidden">
                <div class="uco-hero-mesh"></div>
            </div>
            <div class="relative z-10 mx-auto flex max-w-[1600px] flex-col gap-6 px-4 text-left sm:px-6 md:flex-row md:items-end md:justify-between lg:px-8">
                <div class="space-y-2">
                    <span class="inline-flex items-center rounded-full border border-uco-orange-200 bg-uco-orange-50 px-4 py-1.5 text-xs font-bold text-uco-orange-700">
                        UCO Directory
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Business Directory</h1>
                    <p class="mt-1 text-base text-gray-600">Explore businesses and innovations from our student and alumni network.</p>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        @if (auth()->user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-2 text-xs font-bold text-yellow-700">
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
            <x-business.directory-tab type="entrepreneur" icon="bi-briefcase" label="Entrepreneurs" />
            <x-business.directory-tab type="intrapreneur" icon="bi-building" label="Intrapreneurs" />
        </div>

        {{-- --- Filters --- --}}
        <div class="reveal-on-scroll mb-8 delay-150">
            <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" class="space-y-4" @submit.prevent="updateList()">
                <input type="hidden" name="view" :value="viewType">

                {{-- --- Search & Reset Row --- --}}
                <div class="flex items-center gap-3">
                    <x-ui.search-field class="flex-1" id="search" value="{{ request('search') }}" placeholder="Search business name..." autocomplete="off" />

                    <div class="flex items-center gap-2">
                        <x-ui.filter-reset loading="isLoading" />
                    </div>
                </div>

                {{-- --- Filters Row --- --}}
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.filter-select name="category" @change="submitDebounced()" class="min-w-[150px] flex-1 cursor-pointer px-3 text-sm">
                        <option value="">All Categories</option>
                        @foreach ($categories as $type)
                            <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </x-ui.filter-select>

                    <x-ui.filter-select name="province"
                                        x-model="selectedProvince"
                                        @change="selectedCity = ''; submitDebounced()" class="min-w-[150px] flex-1 cursor-pointer px-3 text-sm">
                        <option value="">All Provinces</option>
                        @foreach ($availableProvinces as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </x-ui.filter-select>

                    <x-ui.filter-select name="city"
                                        x-model="selectedCity"
                                        @change="submitDebounced()" class="min-w-[150px] flex-1 cursor-pointer px-3 text-sm">
                        <option value="">All Cities</option>
                        <template x-for="city in (selectedProvince ? filteredCities : availableCities)" :key="city">
                            <option :value="city" x-text="city"></option>
                        </template>
                    </x-ui.filter-select>
                </div>
            </form>
        </div>

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
