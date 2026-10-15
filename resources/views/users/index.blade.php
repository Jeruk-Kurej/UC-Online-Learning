<x-app-layout>
    <div data-user-admin class="users-wrapper max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8"
        @start-import.window="showImportModal = false"
        @close-import-modal.window="showImportModal = false"
        x-data="userAdminList"
        @ajax-pagination.window="updateList($event.detail.url)">
        
        {{-- Page Header --}}
        <x-ui.page-header badge="Admin Portal" title="User Management" subtitle="Manage student and alumni profiles synced from the central database.">
            <x-slot:actions>
                <span class="inline-flex items-center justify-center gap-2.5 px-3 py-2 bg-uco-yellow-50 border border-uco-yellow-200 rounded-md shadow-[0_1px_2px_rgba(0,0,0,0.02)] select-none flex-grow sm:flex-initial">
                    <i class="bi bi-star-fill text-uco-yellow-500 text-sm flex-shrink-0"></i>
                    <span class="flex flex-col gap-0.5">
                        <span class="text-xs font-semibold text-uco-yellow-700 leading-none">
                            <span id="stat-featured-total">{{ $featuredUserCount }}</span> Featured
                        </span>
                        <span class="text-[10px] font-bold text-uco-yellow-600/90 leading-none mt-0.5">
                            <span class="text-green-600" id="stat-featured-intra">{{ $featuredIntrapreneurCount }}</span> Intra
                            <span class="text-uco-yellow-400 mx-0.5">·</span>
                            <span class="text-blue-600" id="stat-featured-entre">{{ $featuredEntrepreneurCount }}</span> Entre
                        </span>
                    </span>
                </span>
                @if(auth()->user()?->isAdmin())
                <button id="btn-open-import-modal" type="button" @click="showImportModal = true" class="btn-uco btn-uco-secondary px-4 py-2 text-sm flex-grow sm:flex-initial justify-center">
                    <i class="bi bi-cloud-upload mr-2"></i>
                    Import CSV
                </button>
                @endif

                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('users.create') }}" class="btn-uco btn-uco-primary px-4 py-2 text-sm flex-grow sm:flex-initial justify-center">
                        <i class="bi bi-person-plus-fill mr-2"></i>
                        Create User
                    </a>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        {{-- Statistics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8"
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

        {{-- Filters & Search --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 mb-8 shadow-sm reveal-on-scroll delay-300">
            <form x-ref="filterForm" action="{{ route('users.index') }}" method="GET" class="space-y-4"
                @submit.prevent="updateList()">
                
                {{-- Search & Reset Row --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search name, email, username, or NIS..."
                            @input="submitDebounced()"
                            @keydown.enter.prevent="updateList()"
                            class="w-full border-gray-300 bg-white rounded-md pl-10 pr-4 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm"
                        >
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="resetFilters()" x-show="!isSubmitting" title="Reset Filters" class="inline-flex items-center justify-center bg-white border border-gray-300 text-gray-500 hover:text-gray-900 hover:bg-gray-50 h-[38px] w-[38px] rounded-md transition shadow-sm flex-shrink-0">
                            <i class="bi bi-arrow-clockwise text-lg"></i>
                        </button>
                        <div x-show="isSubmitting" x-cloak class="inline-flex items-center justify-center bg-uco-orange-50 border border-uco-orange-200 text-uco-orange-700 h-[38px] px-3 rounded-md shadow-sm">
                            <x-ui.spinner />
                            <span class="ml-2 text-xs font-medium hidden sm:inline">Updating...</span>
                        </div>
                    </div>
                </div>

                {{-- Filters Row --}}
                <div class="flex flex-wrap items-center gap-3">
                    <select id="sort_name" name="sort_name" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Sort Name: Default</option>
                        <option value="asc" @selected(request('sort_name') === 'asc')>A → Z</option>
                        <option value="desc" @selected(request('sort_name') === 'desc')>Z → A</option>
                    </select>

                    <select id="sort_year" name="sort_year" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Student Year: Default</option>
                        <option value="desc" @selected(request('sort_year') === 'desc')>Latest → Earliest</option>
                        <option value="asc" @selected(request('sort_year') === 'asc')>Earliest → Latest</option>
                    </select>

                    <select id="student_status" name="student_status" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Status: All</option>
                        <option value="active" @selected(request('student_status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('student_status') === 'inactive')>Inactive</option>
                        <option value="cuti" @selected(request('student_status') === 'cuti')>Cuti</option>
                        <option value="alumni" @selected(request('student_status') === 'alumni')>Alumni</option>
                    </select>

                    <select id="current_status" name="current_status" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Category: All</option>
                        <option value="Entrepreneur" @selected(request('current_status') === 'Entrepreneur')>Entrepreneur</option>
                        <option value="Intrapreneur" @selected(request('current_status') === 'Intrapreneur')>Intrapreneur</option>
                    </select>

                    <select id="major" name="major" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Major: All</option>
                        @foreach($availableMajors as $majorOption)
                            <option value="{{ $majorOption }}" @selected(request('major') === $majorOption)>{{ $majorOption }}</option>
                        @endforeach
                    </select>

                    <select id="year_of_enrollment" name="year_of_enrollment" @change="updateList()" class="flex-1 min-w-[130px] border-gray-300 bg-white rounded-md px-3 py-2 text-sm focus:ring-uco-orange-500 focus:border-uco-orange-500 outline-none transition-all shadow-sm">
                        <option value="">Year: All</option>
                        @foreach($availableEnrollmentYears as $yearOption)
                            <option value="{{ $yearOption }}" @selected(request('year_of_enrollment') === $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Flash messages removed - handled by global toasts --}}
        @if($errors->has('featured'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm font-bold px-4 py-3 rounded-xl">
                {{ $errors->first('featured') }}
            </div>
        @endif

        {{-- Users List Container --}}
        <div id="users-list-container" :class="isSubmitting ? 'opacity-50 pointer-events-none transition-opacity' : 'transition-opacity'">
            @include('users.partials.list')
        </div>

        {{-- Import Modal --}}
        <x-admin.import-modal title="Import Data" :action="route('users.import')" input-id="csv_file" name-id="file_name" />

        @include('partials.import-progress')
    </div>

</x-app-layout>
