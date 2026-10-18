<x-app-layout>
    @section('title', $company->name . ' - ' . ($company->category->name ?? 'Company'))

    <x-ui.breadcrumbs class="mb-6" :href="route('businesses.index', ['view' => 'intrapreneur'])" :current="$company->name" />

    <x-business.profile-heading :name="$company->name" :logo-url="$company->logo_url">
        <x-slot:badges>
            <span class="badge badge-info">Work profile</span>
            @if ($company->category)
                <span class="badge badge-neutral">{{ $company->category->name }}</span>
            @endif
        </x-slot:badges>

        <x-slot:actions>
            @auth
                @if ($canManage)
                    <a href="{{ route('intrapreneurs.edit', $company) }}" class="btn btn-secondary">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                        Edit work profile
                    </a>
                @endif
            @endauth
        </x-slot:actions>
    </x-business.profile-heading>

    <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
        {{-- --- Main column --- --}}
        <div class="min-w-0 space-y-10 lg:col-span-2">
            <section class="card space-y-6" aria-labelledby="about-company">
                <div>
                    <h2 id="about-company" class="card-title">About the role</h2>
                    <p class="mt-2 whitespace-pre-line text-base text-gray-800">{{ $company->job_description ?: 'No description added yet.' }}</p>
                </div>

                <dl class="divide-y divide-line border-y border-line">
                    @if ($company->position)
                        <x-ui.fact label="Position">{{ $company->position }}</x-ui.fact>
                    @endif
                    @if ($company->level_position)
                        <x-ui.fact label="Level">{{ $company->level_position }}</x-ui.fact>
                    @endif
                    @if ($company->year_started_working)
                        <x-ui.fact label="Working here since">{{ $company->year_started_working }}</x-ui.fact>
                    @endif
                    @if ($company->company_scale)
                        <x-ui.fact label="Company size">{{ $company->company_scale }}</x-ui.fact>
                    @endif
                </dl>
            </section>

            @if ($company->achievement || $canManage)
                <x-ui.section title="Achievements" x-data="achievementList(@js($company->achievements_list), @js(route('intrapreneurs.delete_achievement', $company)))">
                    @if ($canManage)
                        <x-slot:action>
                            <a href="{{ route('intrapreneurs.create_achievement', $company) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                Add achievement
                            </a>
                        </x-slot:action>
                    @endif

                    <ul class="space-y-3">
                        <template x-for="(item, index) in achievements" :key="index">
                            <li class="card flex items-start gap-4 !p-4">
                                <i class="bi bi-trophy mt-1 text-xl text-gold-700" aria-hidden="true"></i>
                                <p class="min-w-0 flex-1 text-base text-gray-800" x-text="item"></p>
                                @if ($canManage)
                                    <button type="button" @click="deleteAchievement(index)" class="btn btn-danger btn-sm">Delete</button>
                                @endif
                            </li>
                        </template>
                    </ul>

                    <template x-if="achievements.length === 0">
                        <x-ui.empty-state title="No achievements yet">Add milestones and contributions from this job.</x-ui.empty-state>
                    </template>
                </x-ui.section>
            @endif
        </div>

        {{-- --- Side column --- --}}
        <div class="min-w-0">
            @if ($student)
                <x-people.profile-card :person="$student" title="Employee" :status="$student->display_status" career="Intrapreneur" with-linkedin>
                    <x-people.skills :skills="$student->skills" />
                </x-people.profile-card>
            @endif
        </div>
    </div>
</x-app-layout>
