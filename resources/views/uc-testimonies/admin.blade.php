<x-app-layout>
    @section('title', 'Manage Testimonies')

    <div data-testimony-admin class="testimonies-wrapper mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8"
         x-data="testimonyAdminList"
         @ajax-pagination.window="updateList($event.detail.url, true, true)">

        {{-- --- Page Header --- --}}
        <x-ui.page-header badge="Admin Portal" title="Manage Testimonies" subtitle="Review user testimonies and select which ones are featured on the homepage." stack-until="md" />

        {{-- --- Statistics --- --}}
        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-admin.stat-card class="delay-100" :compact-on-mobile="false" value-id="stat-total-testimonies">
                <x-slot:label>Total Testimonies</x-slot:label>
                {{ $totalTestimonies }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-150" tone="blue" :compact-on-mobile="false" value-id="stat-featured-testimonies">
                <x-slot:label>Featured Testimonies</x-slot:label>
                {{ $featuredTestimonies }}
            </x-admin.stat-card>
        </div>

        {{-- --- Filters & Search --- --}}
        <div class="reveal-on-scroll mb-8 delay-300">
            <form x-ref="filterForm" action="{{ route('uc-testimonies.admin') }}" method="GET" class="space-y-4"
                  @submit.prevent="updateList()">

                {{-- --- Search & Filters Row --- --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                               type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search user name or testimony content..."
                               @input="submitDebounced()"
                               @keydown.enter.prevent="updateList()"
                               class="w-full rounded-md border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                    </div>

                    <div class="w-[115px] flex-shrink-0 sm:w-48">
                        <select id="featured" name="featured" @change="updateList()" class="w-full rounded-md border-gray-300 bg-white px-2 py-2 text-xs shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500 sm:px-3 sm:text-sm">
                            <option value="">Status: All</option>
                            <option value="1" @selected(request('featured') === '1')>Featured</option>
                            <option value="0" @selected(request('featured') === '0')>Regular</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" x-show="!isSubmitting" title="Reset Filters" class="inline-flex h-[38px] w-[38px] items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-900">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex h-[38px] items-center justify-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 text-uco-orange-700 shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 hidden text-xs font-medium sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- --- Testimonies List Container --- --}}
        <div id="testimonies-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('uc-testimonies.partials.list')
        </div>

    </div>

    @push('scripts')
    @endpush

</x-app-layout>
