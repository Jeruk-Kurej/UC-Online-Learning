<x-app-layout bleed>
    @section('title', 'Business Directory')

    <div data-business-directory x-data="businessDirectory(@js($directoryState))" @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <form x-ref="filterForm" action="{{ route('businesses.index') }}" method="GET" @submit.prevent="updateList()">
            <input type="hidden" name="view" :value="viewType">

            {{-- --- Title band on the campus photo: the title, the switch between the two lists, then the search and the filters together --- --}}
            <x-ui.photo-band>
                <div class="mx-auto max-w-6xl px-4 pt-10 sm:px-6 sm:pt-12 lg:px-8">
                    <h1 class="font-display text-4xl font-semibold leading-tight sm:text-5xl">Directory</h1>
                    <p class="mt-3 max-w-2xl text-lg text-gray-100">Find the <strong>businesses</strong> and <strong>work profiles</strong> of our students and alumni.</p>

                    <nav class="segmented mt-6" aria-label="Directory">
                        <x-business.directory-tab type="entrepreneur" label="Entrepreneurs" />
                        <x-business.directory-tab type="intrapreneur" label="Intrapreneurs" />
                    </nav>

                    <div class="mt-4 space-y-3 pb-10 sm:pb-12">
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <x-ui.search-field glass class="flex-1" id="search" value="{{ $filters['search'] }}" placeholder="Search by business or owner name" autocomplete="off" />
                            <x-ui.filter-reset loading="isLoading" class="!border-white/60 !bg-transparent !text-white hover:!bg-white/10" />
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <x-ui.filter-select glass name="category" aria-label="Category" @change="submitDebounced()">
                                <option value="">All categories</option>
                                @foreach ($categories as $type)
                                    <option value="{{ $type->id }}" {{ $filters['category'] == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </x-ui.filter-select>

                            <x-ui.filter-select glass name="province" aria-label="Province" x-model="selectedProvince" @change="selectedCity = ''; submitDebounced()">
                                <option value="">All provinces</option>
                                @foreach ($availableProvinces as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </x-ui.filter-select>

                            <x-ui.filter-select glass name="city" aria-label="City" x-model="selectedCity" @change="submitDebounced()">
                                <option value="">All cities</option>
                                <template x-for="city in (selectedProvince ? filteredCities : availableCities)" :key="city">
                                    <option :value="city" x-text="city"></option>
                                </template>
                            </x-ui.filter-select>
                        </div>
                    </div>
                </div>
            </x-ui.photo-band>
        </form>

        <div class="page">
            @if (auth()->user()?->isAdmin() && $errors->has('featured'))
                <p class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-base text-red-800">{{ $errors->first('featured') }}</p>
            @endif

            {{-- --- Results --- --}}
            <div id="businesses-list-container" class="min-h-[24rem] transition-opacity" :class="{ 'pointer-events-none opacity-60': isLoading }" aria-live="polite">
                @include('businesses.partials.list')
            </div>
        </div>
    </div>
</x-app-layout>
