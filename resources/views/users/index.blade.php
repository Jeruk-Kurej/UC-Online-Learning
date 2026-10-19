<x-app-layout wide>
    @section('title', 'Manage users')

    <div data-user-admin data-stats-url="{{ route('users.stats', [], false) }}" @start-import.window="showImportModal = false" @close-import-modal.window="showImportModal = false" x-data="userAdminList" @ajax-pagination.window="updateList($event.detail.url)">
        <x-ui.page-header title="Manage users" subtitle="Students and alumni with a profile on the site.">
            <x-slot:actions>
                @if (auth()->user()?->isAdmin())
                    <button id="btn-open-import-modal" type="button" @click="showImportModal = true" class="btn btn-secondary">
                        <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                        Import from a file
                    </button>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                        Add a user
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Numbers --- --}}
        <div class="mb-3 grid grid-cols-2 gap-4 lg:grid-cols-4" @ajax-update-stats.window="refreshStats()">
            <x-admin.stat-card value-id="stat-total">
                <x-slot:label>All users</x-slot:label>
                {{ $totalUsers }}
            </x-admin.stat-card>
            <x-admin.stat-card value-id="stat-entrepreneurs">
                <x-slot:label>Entrepreneurs</x-slot:label>
                {{ $totalEntrepreneurs }}
            </x-admin.stat-card>
            <x-admin.stat-card value-id="stat-intrapreneurs">
                <x-slot:label>Intrapreneurs</x-slot:label>
                {{ $totalIntrapreneurs }}
            </x-admin.stat-card>
            <x-admin.stat-card value-id="stat-alumni">
                <x-slot:label>Alumni</x-slot:label>
                {{ $totalAlumni }}
            </x-admin.stat-card>
        </div>
        <p class="mb-8 text-base text-gray-700">
            Featured on the home page: <span id="stat-featured-total" class="font-semibold">{{ $featuredUserCount }}</span>
            (<span id="stat-featured-entre">{{ $featuredEntrepreneurCount }}</span> entrepreneurs, <span id="stat-featured-intra">{{ $featuredIntrapreneurCount }}</span> intrapreneurs)
        </p>

        {{-- --- Search and filters --- --}}
        <form x-ref="filterForm" action="{{ route('users.index') }}" method="GET" class="card mb-6 space-y-3" @submit.prevent="updateList()">
            <div class="flex flex-col gap-3 sm:flex-row">
                <x-ui.search-field class="flex-1" value="{{ request('search') }}" placeholder="Name, email or student number" />
                <x-ui.filter-reset />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <x-ui.filter-select id="sort_name" name="sort_name" aria-label="Sort by name" @change="updateList()">
                    <option value="">Name: any order</option>
                    <option value="asc" @selected(request('sort_name') === 'asc')>Name: A to Z</option>
                    <option value="desc" @selected(request('sort_name') === 'desc')>Name: Z to A</option>
                </x-ui.filter-select>

                <x-ui.filter-select id="sort_year" name="sort_year" aria-label="Sort by year joined" @change="updateList()">
                    <option value="">Year joined: any order</option>
                    <option value="desc" @selected(request('sort_year') === 'desc')>Year joined: newest first</option>
                    <option value="asc" @selected(request('sort_year') === 'asc')>Year joined: oldest first</option>
                </x-ui.filter-select>

                <x-ui.filter-select id="student_status" name="student_status" aria-label="Student status" @change="updateList()">
                    <option value="">All student statuses</option>
                    <option value="active" @selected(request('student_status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('student_status') === 'inactive')>Inactive</option>
                    <option value="cuti" @selected(request('student_status') === 'cuti')>On leave</option>
                    <option value="alumni" @selected(request('student_status') === 'alumni')>Alumni</option>
                </x-ui.filter-select>

                <x-ui.filter-select id="current_status" name="current_status" aria-label="Entrepreneur or intrapreneur" @change="updateList()">
                    <option value="">Entrepreneurs and intrapreneurs</option>
                    <option value="Entrepreneur" @selected(request('current_status') === 'Entrepreneur')>Entrepreneurs</option>
                    <option value="Intrapreneur" @selected(request('current_status') === 'Intrapreneur')>Intrapreneurs</option>
                </x-ui.filter-select>

                <x-ui.filter-select id="major" name="major" aria-label="Major" @change="updateList()">
                    <option value="">All majors</option>
                    @foreach ($availableMajors as $majorOption)
                        <option value="{{ $majorOption }}" @selected(request('major') === $majorOption)>{{ $majorOption }}</option>
                    @endforeach
                </x-ui.filter-select>

                <x-ui.filter-select id="year_of_enrollment" name="year_of_enrollment" aria-label="Year joined" @change="updateList()">
                    <option value="">All years joined</option>
                    @foreach ($availableEnrollmentYears as $yearOption)
                        <option value="{{ $yearOption }}" @selected(request('year_of_enrollment') === $yearOption)>{{ $yearOption }}</option>
                    @endforeach
                </x-ui.filter-select>
            </div>
        </form>

        @if ($errors->has('featured'))
            <p class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-base text-red-800">{{ $errors->first('featured') }}</p>
        @endif

        <div id="users-list-container" :class="{ 'pointer-events-none opacity-50': isSubmitting }" aria-live="polite">
            @include('users.partials.list')
        </div>

        <x-admin.import-modal title="Import users" :action="route('users.import')" input-id="csv_file" name-id="file_name" />

        @include('partials.import-progress')
    </div>
</x-app-layout>
