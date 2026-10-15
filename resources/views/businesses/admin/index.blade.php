<x-app-layout>
    <div data-business-admin class="mx-auto w-full max-w-[1600px] px-4 py-8"
         @start-import.window="showImportModal = false"
         @close-import-modal.window="showImportModal = false"
         data-view-type="{{ $viewType ?? 'entrepreneur' }}"
         x-data="businessAdminList"
         x-init="viewType = $el.dataset.viewType || 'entrepreneur'"
         @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <x-ui.page-header badge="Admin Portal" title="Business Management" subtitle="Review and manage business submissions from students.">
            <x-slot:actions>
                <span class="inline-flex flex-grow items-center justify-center gap-1.5 rounded-md border border-uco-yellow-200 bg-uco-yellow-50 px-3 py-2 text-xs font-semibold text-uco-yellow-700 sm:flex-initial">
                    <i class="bi bi-star-fill text-uco-yellow-500"></i>
                    <span><span class="featured-count">{{ $featuredBusinessesCount }}</span> Featured</span>
                </span>
                <button type="button" @click="showImportModal = true" class="btn-uco btn-uco-secondary flex-grow justify-center px-4 py-2 text-sm sm:flex-initial">
                    <i class="bi bi-cloud-upload mr-2"></i>
                    Import CSV
                </button>

                <a href="{{ route('businesses.create') }}" class="btn-uco btn-uco-primary flex-grow justify-center px-4 py-2 text-sm sm:flex-initial">
                    <i class="bi bi-plus-lg mr-2"></i>
                    Create Business
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Statistics --- --}}
        <div class="mb-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
            <x-admin.stat-card class="delay-100">
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Total Businesses</span>
                    <span x-show="viewType === 'intrapreneur'">Total Career Profiles</span>
                </x-slot:label>
                {{ $totalBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-150" tone="green">
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Approved</span>
                    <span x-show="viewType === 'intrapreneur'">Active / Visible</span>
                </x-slot:label>
                {{ $approvedBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-200" tone="amber">
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Pending Approval</span>
                    <span x-show="viewType === 'intrapreneur'">Hidden</span>
                </x-slot:label>
                {{ $pendingBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-[250ms]" tone="red">
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Rejected / Revision</span>
                    <span x-show="viewType === 'intrapreneur'">— N/A —</span>
                </x-slot:label>
                {{ $rejectedBusinesses }}
            </x-admin.stat-card>
        </div>

        {{-- --- Filters & Search --- --}}
        <div class="reveal-on-scroll mb-8 delay-300">
            <form x-ref="filterForm" action="{{ route('businesses.admin') }}" method="GET" @submit.prevent="updateList()">
                {{-- --- Hidden type input (kept in sync by switchType()) --- --}}
                <input type="hidden" name="type" :value="viewType">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    {{-- --- Search Input --- --}}
                    <div class="relative w-full flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                               type="text"
                               name="search"
                               value="{{ $search }}"
                               :placeholder="viewType === 'intrapreneur' ? 'Search company or owner name...' : 'Search business or owner name...'"
                               @input="submitDebounced()"
                               @keydown.enter.prevent="updateList()"
                               class="w-full rounded-md border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                    </div>

                    {{-- --- Filters & Reset Button --- --}}
                    <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">

                        {{-- --- Type dropdown --- --}}
                        <select x-model="viewType"
                                @change="switchType($event.target.value)"
                                class="min-w-[175px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white py-2 pl-3 pr-8 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500 sm:flex-initial">
                            <option value="entrepreneur">Type: Entrepreneur</option>
                            <option value="intrapreneur">Type: Intrapreneur</option>
                        </select>

                        {{-- --- Status filter --- --}}
                        <select name="status" @change="updateList()"
                                class="min-w-[145px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white py-2 pl-3 pr-8 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500 sm:flex-initial">
                            <option value="">Status: All</option>
                            <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="need_revision" {{ ($status ?? '') === 'need_revision' ? 'selected' : '' }}>Need Revision</option>
                        </select>

                        {{-- --- Featured filter (entrepreneur only) --- --}}
                        <template x-if="viewType === 'entrepreneur'">
                            <select name="featured" @change="updateList()"
                                    class="min-w-[145px] flex-1 cursor-pointer rounded-md border-gray-300 bg-white py-2 pl-3 pr-8 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500 sm:flex-initial">
                                <option value="">Featured: All</option>
                                <option value="yes" {{ ($featured ?? '') === 'yes' ? 'selected' : '' }}>Featured</option>
                                <option value="no" {{ ($featured ?? '') === 'no' ? 'selected' : '' }}>Not Featured</option>
                            </select>
                        </template>

                        {{-- --- Reset --- --}}
                        <button type="button" @click="resetFilters()" x-show="!isSubmitting" title="Reset Filters"
                                class="inline-flex h-[38px] w-[38px] flex-shrink-0 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-900">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>

                        {{-- --- Loading spinner --- --}}
                        <div x-show="isSubmitting" x-cloak class="inline-flex h-[38px] items-center justify-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 text-uco-orange-700 shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 hidden text-xs font-medium sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="businesses-admin-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('businesses.admin.partials.list')
        </div>

        {{-- --- Status Modal --- --}}
        <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" data-status-modal-close></div>

                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                <div class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form id="statusForm" method="POST">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="bi bi-gear-fill text-blue-600"></i>
                                </div>
                                <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">
                                        Update Business Status
                                    </h3>
                                    <div class="mt-2">
                                        <p class="mb-4 text-sm text-gray-500" id="businessNameDisplay"></p>

                                        <div class="mb-4">
                                            <label class="mb-2 block text-[10px] font-black uppercase tracking-wider text-gray-400">New Status</label>
                                            <select name="status" id="statusSelect" data-status-select
                                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="pending">Pending Approval</option>
                                                <option value="approved">Approved</option>
                                                <option value="need_revision">Need Revision</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                        </div>

                                        <div id="reasonField" class="hidden">
                                            <label class="mb-2 block text-[10px] font-black uppercase tracking-wider text-gray-400">Feedback / Reason</label>
                                            <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                                      class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500"
                                                      placeholder="Provide specific feedback or reason for rejection..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:items-center sm:gap-2 sm:px-6">
                            <button type="submit" class="btn-uco btn-uco-primary w-full sm:w-auto">
                                Save Changes
                            </button>
                            <button type="button" data-status-modal-close class="btn-uco btn-uco-neutral mt-3 w-full sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- --- Import Modal --- --}}
        @if (auth()->user()?->isAdmin())
            <x-admin.import-modal title="Import Businesses" :action="route('businesses.import')" input-id="biz_csv_file" name-id="biz_file_name" />
        @endif

        @include('partials.import-progress')
    </div>
</x-app-layout>
