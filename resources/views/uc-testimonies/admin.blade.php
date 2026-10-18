<x-app-layout>
    @section('title', 'Manage testimonies')

    <div data-testimony-admin x-data="testimonyAdminList" @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <x-ui.page-header title="Manage testimonies" subtitle="Choose which testimonies show on the home page." />

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-admin.stat-card value-id="stat-total-testimonies">
                <x-slot:label>All testimonies</x-slot:label>
                {{ $totalTestimonies }}
            </x-admin.stat-card>
            <x-admin.stat-card value-id="stat-featured-testimonies">
                <x-slot:label>Featured on the home page</x-slot:label>
                {{ $featuredTestimonies }}
            </x-admin.stat-card>
        </div>

        <form x-ref="filterForm" action="{{ route('uc-testimonies.admin') }}" method="GET" class="card mb-6 space-y-3" @submit.prevent="updateList()">
            <div class="flex flex-col gap-3 sm:flex-row">
                <x-ui.search-field class="flex-1" value="{{ request('search') }}" placeholder="Search by name or by what the testimony says" />
                <x-ui.filter-select id="featured" name="featured" aria-label="Featured" class="sm:w-56" @change="updateList()">
                    <option value="">Featured or not</option>
                    <option value="1" @selected(request('featured') === '1')>Featured</option>
                    <option value="0" @selected(request('featured') === '0')>Not featured</option>
                </x-ui.filter-select>
                <x-ui.filter-reset />
            </div>
        </form>

        <div id="testimonies-list-container" :class="{ 'pointer-events-none opacity-50': isSubmitting }" aria-live="polite">
            @include('uc-testimonies.partials.list')
        </div>
    </div>
</x-app-layout>
