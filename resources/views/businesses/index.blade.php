<x-app-layout>
    @section('title', 'Business Directory')
    <div data-business-directory x-data="businessDirectory(@js($directoryState))" x-init="init()" @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <x-ui.page-header title="Directory" subtitle="Businesses and work profiles of our students and alumni." />

        {{-- --- Entrepreneurs / Intrapreneurs --- --}}
        <nav class="tabs mb-6" aria-label="Directory">
            <x-business.directory-tab type="entrepreneur" label="Entrepreneurs" />
            <x-business.directory-tab type="intrapreneur" label="Intrapreneurs" />
        </nav>

        {{-- --- Filters --- --}}
        <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" class="mb-6 space-y-3" @submit.prevent="updateList()">
            <input type="hidden" name="view" :value="viewType">

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-ui.search-field class="flex-1" id="search" value="{{ request('search') }}" placeholder="Business or owner name" autocomplete="off" />
                <x-ui.filter-reset loading="isLoading" />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <x-ui.filter-select name="category" aria-label="Category" @change="submitDebounced()">
                    <option value="">All categories</option>
                    @foreach ($categories as $type)
                        <option value="{{ $type->id }}" {{ request('category') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </x-ui.filter-select>

                <x-ui.filter-select name="province" aria-label="Province" x-model="selectedProvince" @change="selectedCity = ''; submitDebounced()">
                    <option value="">All provinces</option>
                    @foreach ($availableProvinces as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </x-ui.filter-select>

                <x-ui.filter-select name="city" aria-label="City" x-model="selectedCity" @change="submitDebounced()">
                    <option value="">All cities</option>
                    <template x-for="city in (selectedProvince ? filteredCities : availableCities)" :key="city">
                        <option :value="city" x-text="city"></option>
                    </template>
                </x-ui.filter-select>
            </div>
        </form>

        @if (auth()->user()?->isAdmin() && $errors->has('featured'))
            <p class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-base text-red-800">{{ $errors->first('featured') }}</p>
        @endif

        {{-- --- Results --- --}}
        <div id="businesses-list-container" class="min-h-[24rem] transition-opacity" :class="{ 'pointer-events-none opacity-60': isLoading }" aria-live="polite">
            @include('businesses.partials.list')
        </div>
    </div>
</x-app-layout>
