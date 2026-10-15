<x-app-layout>
    <div data-user-admin class="users-wrapper mx-auto max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8"
         @start-import.window="showImportModal = false"
         @close-import-modal.window="showImportModal = false"
         x-data="userAdminList"
         @ajax-pagination.window="updateList($event.detail.url)">

        {{-- --- Page Header --- --}}
        <x-ui.page-header badge="Admin Portal" title="User Management" subtitle="Manage student and alumni profiles synced from the central database.">
            <x-slot:actions>
                <span class="inline-flex flex-grow select-none items-center justify-center gap-2.5 rounded-md border border-uco-yellow-200 bg-uco-yellow-50 px-3 py-2 shadow-[0_1px_2px_rgba(0,0,0,0.02)] sm:flex-initial">
                    <i class="bi bi-star-fill flex-shrink-0 text-sm text-uco-yellow-500"></i>
                    <span class="flex flex-col gap-0.5">
                        <span class="text-xs font-semibold leading-none text-uco-yellow-700">
                            <span id="stat-featured-total">{{ $featuredUserCount }}</span> Featured
                        </span>
                        <span class="mt-0.5 text-[10px] font-bold leading-none text-uco-yellow-600/90">
                            <span class="text-green-600" id="stat-featured-intra">{{ $featuredIntrapreneurCount }}</span> Intra
                            <span class="mx-0.5 text-uco-yellow-400">·</span>
                            <span class="text-blue-600" id="stat-featured-entre">{{ $featuredEntrepreneurCount }}</span> Entre
                        </span>
                    </span>
                </span>
                @if (auth()->user()?->isAdmin())
                    <button id="btn-open-import-modal" type="button" @click="showImportModal = true" class="btn-uco btn-uco-secondary flex-grow justify-center px-4 py-2 text-sm sm:flex-initial">
                        <i class="bi bi-cloud-upload mr-2"></i>
                        Import CSV
                    </button>
                @endif

                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('users.create') }}" class="btn-uco btn-uco-primary flex-grow justify-center px-4 py-2 text-sm sm:flex-initial">
                        <i class="bi bi-person-plus-fill mr-2"></i>
                        Create User
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        {{-- --- Statistics --- --}}
        <div class="mb-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4"
             @ajax-update-stats.window="refreshStats()">
            <x-admin.stat-card class="delay-100">
                <x-slot:label>Total Users</x-slot:label>
                {{ $totalUsers }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-150" tone="blue">
                <x-slot:label>Entrepreneurs</x-slot:label>
                {{ $totalEntrepreneurs }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-200" tone="green">
                <x-slot:label>Intrapreneurs</x-slot:label>
                {{ $totalIntrapreneurs }}
            </x-admin.stat-card>
            <x-admin.stat-card class="delay-[250ms]" tone="purple">
                <x-slot:label>Alumni</x-slot:label>
                {{ $totalAlumni }}
            </x-admin.stat-card>
        </div>

        {{-- --- Filters & Search --- --}}
        <div class="reveal-on-scroll mb-8 rounded-lg border border-gray-200 bg-white p-5 shadow-sm delay-300">
            <form x-ref="filterForm" action="{{ route('users.index') }}" method="GET" class="space-y-4"
                  @submit.prevent="updateList()">

                {{-- --- Search & Reset Row --- --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative w-full flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                               type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search name, email, username, or NIS..."
                               @input="submitDebounced()"
                               @keydown.enter.prevent="updateList()"
                               class="w-full rounded-md border-gray-300 bg-white py-2 pl-10 pr-4 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                    </div>

                    <div class="flex w-full items-center gap-2 sm:w-auto">
                        <button type="button" @click="resetFilters()" x-show="!isSubmitting" title="Reset Filters" class="inline-flex h-[38px] w-[38px] flex-shrink-0 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-500 shadow-sm transition hover:bg-gray-50 hover:text-gray-900">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex h-[38px] items-center justify-center rounded-md border border-uco-orange-200 bg-uco-orange-50 px-3 text-uco-orange-700 shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 hidden text-xs font-medium sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>

                {{-- --- Filters Row --- --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select id="sort_name" name="sort_name" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Sort Name: Default</option>
                        <option value="asc" @selected(request('sort_name') === 'asc')>A → Z</option>
                        <option value="desc" @selected(request('sort_name') === 'desc')>Z → A</option>
                    </select>

                    <select id="sort_year" name="sort_year" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Student Year: Default</option>
                        <option value="desc" @selected(request('sort_year') === 'desc')>Latest → Earliest</option>
                        <option value="asc" @selected(request('sort_year') === 'asc')>Earliest → Latest</option>
                    </select>

                    <select id="student_status" name="student_status" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Status: All</option>
                        <option value="active" @selected(request('student_status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('student_status') === 'inactive')>Inactive</option>
                        <option value="cuti" @selected(request('student_status') === 'cuti')>Cuti</option>
                        <option value="alumni" @selected(request('student_status') === 'alumni')>Alumni</option>
                    </select>

                    <select id="current_status" name="current_status" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Category: All</option>
                        <option value="Entrepreneur" @selected(request('current_status') === 'Entrepreneur')>Entrepreneur</option>
                        <option value="Intrapreneur" @selected(request('current_status') === 'Intrapreneur')>Intrapreneur</option>
                    </select>

                    <select id="major" name="major" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Major: All</option>
                        @foreach ($availableMajors as $majorOption)
                            <option value="{{ $majorOption }}" @selected(request('major') === $majorOption)>{{ $majorOption }}</option>
                        @endforeach
                    </select>

                    <select id="year_of_enrollment" name="year_of_enrollment" @change="updateList()" class="min-w-[130px] flex-1 rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition-all focus:border-uco-orange-500 focus:ring-uco-orange-500">
                        <option value="">Year: All</option>
                        @foreach ($availableEnrollmentYears as $yearOption)
                            <option value="{{ $yearOption }}" @selected(request('year_of_enrollment') === $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if ($errors->has('featured'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ $errors->first('featured') }}
            </div>
        @endif

        {{-- --- Users List Container --- --}}
        <div id="users-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('users.partials.list')
        </div>

        {{-- --- Import Modal --- --}}
        <x-admin.import-modal title="Import Data" :action="route('users.import')" input-id="csv_file" name-id="file_name" />

        @include('partials.import-progress')
    </div>

</x-app-layout>
