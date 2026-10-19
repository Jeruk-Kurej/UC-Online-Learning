<x-app-layout>
    @section('title', 'Manage businesses')

    <div data-business-admin @start-import.window="showImportModal = false" @close-import-modal.window="showImportModal = false" data-view-type="{{ $viewType ?? 'entrepreneur' }}" x-data="businessAdminList" x-init="viewType = $el.dataset.viewType || 'entrepreneur'" @ajax-pagination.window="updateList($event.detail.url, true, true)">
        <x-ui.page-header title="Manage businesses" subtitle="Review what students and alumni submit before it appears in the directory.">
            <x-slot:actions>
                <button type="button" @click="showImportModal = true" class="btn btn-secondary">
                    <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                    Import from a file
                </button>
                <a href="{{ route('businesses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    Add a business
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Numbers --- --}}
        <div class="mb-3 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-admin.stat-card>
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">All businesses</span>
                    <span x-show="viewType === 'intrapreneur'">All work profiles</span>
                </x-slot:label>
                {{ $totalBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card>
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Approved</span>
                    <span x-show="viewType === 'intrapreneur'">Visible</span>
                </x-slot:label>
                {{ $approvedBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card>
                <x-slot:label>
                    <span x-show="viewType === 'entrepreneur'">Waiting for approval</span>
                    <span x-show="viewType === 'intrapreneur'">Hidden</span>
                </x-slot:label>
                {{ $pendingBusinesses }}
            </x-admin.stat-card>
            <x-admin.stat-card x-show="viewType === 'entrepreneur'">
                <x-slot:label>Rejected or needs revision</x-slot:label>
                {{ $rejectedBusinesses }}
            </x-admin.stat-card>
        </div>
        <p class="mb-8 text-base text-gray-700" x-show="viewType === 'entrepreneur'">Featured on the home page: <span class="featured-count font-semibold">{{ $featuredBusinessesCount }}</span></p>

        {{-- --- Search and filters --- --}}
        <form x-ref="filterForm" action="{{ route('businesses.admin') }}" method="GET" class="card mb-6 space-y-3" @submit.prevent="updateList()">
            <input type="hidden" name="type" :value="viewType">

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-ui.search-field class="flex-1" value="{{ $search }}" ::placeholder="viewType === 'intrapreneur' ? 'Search by company or person' : 'Search by business or owner'" />
                <x-ui.filter-reset />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <x-ui.filter-select x-model="viewType" aria-label="Type" @change="switchType($event.target.value)">
                    <option value="entrepreneur">Businesses (entrepreneurs)</option>
                    <option value="intrapreneur">Work profiles (intrapreneurs)</option>
                </x-ui.filter-select>

                <x-ui.filter-select name="status" aria-label="Status" @change="updateList()">
                    <option value="">All statuses</option>
                    <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending approval</option>
                    <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="need_revision" {{ ($status ?? '') === 'need_revision' ? 'selected' : '' }}>Needs revision</option>
                </x-ui.filter-select>

                <template x-if="viewType === 'entrepreneur'">
                    <x-ui.filter-select name="featured" aria-label="Featured" @change="updateList()">
                        <option value="">Featured or not</option>
                        <option value="yes" {{ ($featured ?? '') === 'yes' ? 'selected' : '' }}>Featured</option>
                        <option value="no" {{ ($featured ?? '') === 'no' ? 'selected' : '' }}>Not featured</option>
                    </x-ui.filter-select>
                </template>
            </div>
        </form>

        <div id="businesses-admin-list-container" :class="{ 'pointer-events-none opacity-50': isSubmitting }" aria-live="polite">
            @include('businesses.admin.partials.list')
        </div>

        {{-- --- Status dialog --- --}}
        <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="modal-backdrop" data-status-modal-close aria-hidden="true"></div>
            <div class="relative flex min-h-full items-center justify-center p-4">
                <form id="statusForm" method="POST" class="modal-panel space-y-5">
                    @csrf
                    <div>
                        <h2 id="modal-title" class="card-title">Change status</h2>
                        <p class="mt-1 text-base text-gray-600" id="businessNameDisplay"></p>
                    </div>

                    <div>
                        <label for="statusSelect" class="field-label">New status</label>
                        <select name="status" id="statusSelect" data-status-select class="field-input">
                            <option value="pending">Pending approval</option>
                            <option value="approved">Approved</option>
                            <option value="need_revision">Needs revision</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div id="reasonField" class="hidden">
                        <label for="rejection_reason" class="field-label">Message to the owner</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" class="field-input" placeholder="Say what to change, or why it was rejected."></textarea>
                    </div>

                    <x-forms.actions>
                        <button type="button" data-status-modal-close class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save status</button>
                    </x-forms.actions>
                </form>
            </div>
        </div>

        @if (auth()->user()?->isAdmin())
            <x-admin.import-modal title="Import businesses" :action="route('businesses.import')" input-id="biz_csv_file" name-id="biz_file_name" />
        @endif

        @include('partials.import-progress')
    </div>
</x-app-layout>
