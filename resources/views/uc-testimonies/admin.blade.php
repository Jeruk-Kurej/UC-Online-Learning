<x-app-layout>
    @section('title', 'Manage Testimonies')

    <div data-testimony-admin class="testimonies-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="testimonyAdminList"
        @ajax-pagination.window="updateList($event.detail.url, true, true)">

        {{-- Page Header --}}
        <x-ui.page-header badge="Admin Portal" title="Manage Testimonies" subtitle="Review user testimonies and select which ones are featured on the homepage." stack-until="md" />

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <x-admin.stat-card class="delay-100" :compact-on-mobile="false" value-id="stat-total-testimonies">
                <x-slot:label>Total Testimonies</x-slot:label>
                {{ $totalTestimonies }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-150" tone="blue" :compact-on-mobile="false" value-id="stat-featured-testimonies">
                <x-slot:label>Featured Testimonies</x-slot:label>
                {{ $featuredTestimonies }}
            </x-admin.stat-card>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-8 reveal-on-scroll delay-300">
            <form x-ref="filterForm" action="{{ route('uc-testimonies.admin') }}" method="GET" class="space-y-4"
                @submit.prevent="updateList()">
                
                {{-- Search & Filters Row --}}
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search user name or testimony content..."
                            @input="submitDebounced()"
                            @keydown.enter.prevent="updateList()"
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    <div class="w-[115px] sm:w-48 flex-shrink-0">
                        <select id="featured" name="featured" @change="updateList()" class="w-full border-gray-300 bg-white rounded-md px-2 sm:px-3 py-2 text-xs sm:text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                            <option value="">Status: All</option>
                            <option value="1" @selected(request('featured') === '1')>Featured</option>
                            <option value="0" @selected(request('featured') === '0')>Regular</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetFilters()" x-show="!isSubmitting" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 text-xs font-medium hidden sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Testimonies List Container --}}
        <div id="testimonies-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('uc-testimonies.partials.list')
        </div>

    </div>

    @push('scripts')
    @endpush

</x-app-layout>
