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
                    <x-ui.search-field class="flex-1" value="{{ request('search') }}" placeholder="Search user name or testimony content..." />

                    <div class="w-[115px] flex-shrink-0 sm:w-48">
                        <x-ui.filter-select id="featured" name="featured" @change="updateList()" class="w-full px-2 text-xs sm:px-3 sm:text-sm">
                            <option value="">Status: All</option>
                            <option value="1" @selected(request('featured') === '1')>Featured</option>
                            <option value="0" @selected(request('featured') === '0')>Regular</option>
                        </x-ui.filter-select>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-ui.filter-reset />
                    </div>
                </div>
            </form>
        </div>

        {{-- --- Testimonies List Container --- --}}
        <div id="testimonies-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('uc-testimonies.partials.list')
        </div>

    </div>

</x-app-layout>
