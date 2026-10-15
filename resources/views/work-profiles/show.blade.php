<x-app-layout>
    @section('title', $company->name . ' - ' . ($company->category->name ?? 'Company'))

    <div class="mx-auto max-w-[1600px] px-4 py-10 sm:px-6 lg:px-8">
        <x-ui.breadcrumbs class="mb-8 pt-4 sm:pt-6" :href="route('businesses.index', ['view' => 'intrapreneur'])" :current="$company->name" />

        <div class="mb-8 px-4 sm:px-0">
            <x-business.profile-heading :name="$company->name" :logo-url="$company->logo_url" fallback-icon="bi-building">
                <x-slot:badges>
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                        <i class="bi bi-person-workspace text-[10px]"></i>
                        Intrapreneur
                    </span>

                    @if ($company->category)
                        <span class="inline-flex items-center gap-1.5 rounded-xl bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                            {{ $company->category->name }}
                        </span>
                    @endif
                </x-slot:badges>

                <x-slot:actions>
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
                </x-slot:actions>
            </x-business.profile-heading>
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

                        {{-- --- Insights --- --}}
                        <div class="mb-4 grid grid-cols-1 gap-x-4 gap-y-6 border-y border-gray-100 py-8 sm:grid-cols-3 md:gap-x-6 lg:gap-x-8">
                            <x-business.insight class="border-r border-gray-100 pr-4 md:pr-6 lg:pr-8" icon="bi-graph-up-arrow" tone="blue" caption="Scale of operations">
                                <x-slot:label>Company Scale</x-slot:label>
                                {{ $company->company_scale ?: 'Undisclosed' }}
                            </x-business.insight>

                            <x-business.insight class="border-r border-gray-100 pr-4 md:pr-6 lg:pr-8" icon="bi-calendar-check-fill" tone="indigo" caption="Employment inception">
                                <x-slot:label>Started Working</x-slot:label>
                                {{ $company->year_started_working ? 'Year ' . $company->year_started_working : 'Timeline Undisclosed' }}
                            </x-business.insight>

                            <x-business.insight icon="bi-person-badge-fill" tone="orange" :caption="$company->level_position ?: 'Career Track'">
                                <x-slot:label>Job Role / Level</x-slot:label>
                                {{ $company->position ?: 'Not specified' }}
                            </x-business.insight>
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
