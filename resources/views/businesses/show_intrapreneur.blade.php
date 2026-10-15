<x-app-layout>
    @section('title', $company->name . ' - ' . ($company->category->name ?? 'Company'))

    <div class="mx-auto max-w-[1600px] px-4 py-10 sm:px-6 lg:px-8">
        {{-- --- Breadcrumbs --- --}}
        <nav class="mb-8 flex pt-4 text-sm font-medium sm:pt-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('businesses.index') }}?view=intrapreneur" class="text-gray-400 transition hover:text-uco-orange-500">Directory</a></li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                    </svg>
                    <span class="text-gray-900">{{ $company->name }}</span>
                </li>
            </ol>
        </nav>

        {{-- --- Hero Header Section --- --}}
        <div class="mb-8 px-4 sm:px-0">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="flex flex-1 flex-row items-center gap-4 sm:items-start sm:gap-5">
                    {{-- --- Logo --- --}}
                    @if ($company->logo_url)
                        <img src="{{ storage_image_url($company->logo_url, ['width' => 256, 'height' => 256, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']) }}"
                             alt="{{ $company->name }} Logo" loading="lazy"
                             class="h-16 w-16 flex-shrink-0 rounded-sm object-contain sm:h-20 sm:w-20">
                    @else
                        <div
                             class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-sm bg-gradient-to-br from-soft-gray-50 to-soft-gray-100 sm:h-20 sm:w-20">
                            <i class="bi bi-building text-2xl text-soft-gray-300 sm:text-3xl"></i>
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                                <i class="bi bi-person-workspace text-[10px]"></i>
                                Intrapreneur
                            </span>

                            @if ($company->category)
                                <span class="inline-flex items-center gap-1.5 rounded-xl bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                    {{ $company->category->name }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-bold leading-tight tracking-tight text-soft-gray-900 sm:text-3xl">
                            {{ $company->name }}
                        </h1>
                    </div>
                </div>
                @auth
                    @if ($canManage)
                        <a href="{{ route('intrapreneurs.edit', $company) }}"
                           class="btn-uco btn-uco-secondary flex-shrink-0">
                            <i class="bi bi-pencil-square"></i>
                            <span class="hidden sm:inline">Edit Work Profile</span>
                            <span class="sm:hidden">Edit</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- --- 2-column layout --- --}}
        <div class="grid grid-cols-1 items-start gap-6 md:gap-8" id="biz-layout-grid">
            {{-- --- LEFT COLUMN: Company Content --- --}}
            <div class="min-w-0 space-y-6">
                {{-- --- Company Overview Card --- --}}
                <div class="overflow-hidden border border-soft-gray-100 bg-white shadow-lg sm:rounded-lg">
                    <div class="p-4 sm:p-6 lg:p-8">
                        {{-- --- About Company --- --}}
                        <div class="mb-8">
                            <h4 class="mb-3 text-sm font-bold uppercase tracking-wider text-soft-gray-900">About This Company</h4>
                            <p class="w-full max-w-[1600px] text-base leading-relaxed text-soft-gray-700 2xl:max-w-[1720px]">
                                {{ $company->job_description ?: 'No job description or company overview provided.' }}
                            </p>
                        </div>

                        {{-- --- Metadata Grid --- --}}
                        <div class="mb-4 grid grid-cols-1 gap-x-4 gap-y-6 border-y border-gray-100 py-8 sm:grid-cols-3 md:gap-x-6 lg:gap-x-8">
                            {{-- --- Company Scale --- --}}
                            <div class="flex items-start gap-4 border-r border-gray-100 pr-4 sm:gap-2 md:gap-4 md:pr-6 lg:pr-8">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg border border-blue-100/50 bg-blue-50 text-blue-600 shadow-sm sm:h-9 sm:w-9 md:h-11 md:w-11">
                                    <i class="bi bi-graph-up-arrow text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1.5 text-[10px] font-bold uppercase leading-none tracking-widest text-gray-400">Company Scale</p>
                                    <h5 class="text-sm font-extrabold text-gray-800 sm:text-xs md:text-sm lg:text-base">{{ $company->company_scale ?: 'Undisclosed' }}</h5>
                                    <p class="mt-0.5 text-[9px] font-medium text-gray-400">Scale of operations</p>
                                </div>
                            </div>

                            {{-- --- Started Working --- --}}
                            <div class="flex items-start gap-4 border-r border-gray-100 pr-4 sm:gap-2 md:gap-4 md:pr-6 lg:pr-8">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg border border-indigo-100/50 bg-indigo-50 text-indigo-600 shadow-sm sm:h-9 sm:w-9 md:h-11 md:w-11">
                                    <i class="bi bi-calendar-check-fill text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1.5 text-[10px] font-bold uppercase leading-none tracking-widest text-gray-400">Started Working</p>
                                    <h5 class="text-sm font-extrabold text-gray-800 sm:text-xs md:text-sm lg:text-base">
                                        {{ $company->year_started_working ? 'Year ' . $company->year_started_working : 'Timeline Undisclosed' }}
                                    </h5>
                                    <p class="mt-0.5 text-[9px] font-medium text-gray-400">Employment inception</p>
                                </div>
                            </div>

                            {{-- --- Job Position --- --}}
                            <div class="flex items-start gap-4 sm:gap-2 md:gap-4">
                                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg border border-orange-100/50 bg-orange-50 text-uco-orange-600 shadow-sm sm:h-9 sm:w-9 md:h-11 md:w-11">
                                    <i class="bi bi-person-badge-fill text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="mb-1.5 text-[10px] font-bold uppercase leading-none tracking-widest text-gray-400">Job Role / Level</p>
                                    <h5 class="text-sm font-extrabold text-gray-800 sm:text-xs md:text-sm lg:text-base">{{ $company->position ?: 'Not specified' }}</h5>
                                    <p class="mt-0.5 text-[9px] font-medium text-gray-400">
                                        {{ $company->level_position ? $company->level_position : 'Career Track' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- --- Achievements & Contributions Card --- --}}
                @if ($company->achievement || $canManage)
                    <div
                         x-data="achievementList(@js($company->achievements_list), @js(route('intrapreneurs.delete_achievement', $company)))"
                         class="overflow-hidden border border-soft-gray-100 bg-white shadow-lg sm:rounded-lg">
                        {{-- --- Section Header — same structure as "Our Products" --- --}}
                        <div class="flex flex-col justify-between gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-1 flex-shrink-0 rounded-full bg-uco-orange-500"></div>
                                <div>
                                    <p class="mb-0.5 text-[11px] font-bold uppercase tracking-widest text-uco-orange-500">Career Highlights</p>
                                    <h2 class="text-xl font-bold leading-tight text-gray-900">Achievements & Contributions</h2>
                                </div>
                            </div>
                            @if ($canManage)
                                <a
                                   href="{{ route('intrapreneurs.create_achievement', $company) }}"
                                   class="btn-uco btn-uco-primary w-full text-center sm:w-auto">
                                    <i class="bi bi-plus-lg"></i>
                                    Add Achievement
                                </a>
                            @endif
                        </div>

                        {{-- --- Content Body --- --}}
                        <div class="p-6">
                            {{-- --- Achievements Stack --- --}}
                            <div class="space-y-3">
                                <template x-for="(item, index) in achievements" :key="index">
                                    <div class="group relative flex items-start gap-4 rounded-xl border border-gray-100 bg-gray-50/30 p-4 transition-all duration-300 hover:border-orange-200 hover:bg-white hover:shadow-md">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-orange-50 text-uco-orange-500 transition-transform group-hover:scale-110">
                                            <i class="bi bi-trophy-fill text-sm"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-normal leading-relaxed text-gray-700" x-text="item"></p>
                                        </div>
                                        @if ($canManage)
                                            <button
                                                    @click="deleteAchievement(index)"
                                                    class="btn-uco btn-uco-danger btn-uco-sm opacity-0 transition-opacity group-hover:opacity-100"
                                                    title="Delete Achievement">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        @endif
                                    </div>
                                </template>

                                {{-- --- Empty State --- --}}
                                <template x-if="achievements.length === 0">
                                    <div class="flex items-center justify-center rounded-lg border border-dashed border-gray-200 bg-gray-50/50 px-4 py-6">
                                        <span class="text-sm text-gray-500">No achievements added yet. Start adding milestones and contributions.</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- --- RIGHT COLUMN: Student Profile Sidebar --- --}}
            <div class="min-w-0 space-y-6">
                {{-- --- Employee Profile Section --- --}}
                @if ($student)
                    <x-people.profile-card :person="$student" title-start="Employed" title-end="Student" :status="$student->display_status"
                                           career="Intrapreneur" with-linkedin>
                        {{-- --- Skills --- --}}
                        @if ($student->skills->count() > 0)
                            <div class="pointer-events-none relative z-10 my-5 h-px w-full bg-gray-100"></div>
                            <div>
                                <h3 class="mb-4 text-[11px] font-black uppercase tracking-[0.15em] text-gray-400">Skills</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($student->skills as $skill)
                                        <span class="cursor-default rounded-xl border border-gray-100 bg-gray-50 px-3 py-1.5 text-[10px] font-bold uppercase text-gray-600">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </x-people.profile-card>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
