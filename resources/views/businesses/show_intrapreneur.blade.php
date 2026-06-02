<x-app-layout>
    @section('title', $company->name . ' - ' . ($company->category->name ?? 'Company'))
    
    @php
        $student = $company->user;
        $studentPhotoUrl = $student && $student->profile_photo_url
            ? storage_image_url($student->profile_photo_url, ['width' => 300, 'height' => 300, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto'])
            : null;
        $canManage = auth()->check() && (auth()->user()->id === $company->user_id || auth()->user()->isAdmin());
    @endphp

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumbs --}}
        <nav class="flex pt-4 sm:pt-6 mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('businesses.index') }}?view=intrapreneur" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a></li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    <span class="text-gray-900">{{ $company->name }}</span>
                </li>
            </ol>
        </nav>

        {{-- Hero Header Section --}}
        <div class="mb-8 px-4 sm:px-0">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                <div class="flex-1 flex flex-row items-center sm:items-start gap-4 sm:gap-5">
                    {{-- Logo --}}
                    @if ($company->logo_url)
                        <img src="{{ storage_image_url($company->logo_url, ['width' => 256, 'height' => 256, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']) }}"
                            alt="{{ $company->name }} Logo" loading="lazy"
                            class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-sm object-contain">
                    @else
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 rounded-sm bg-gradient-to-br from-soft-gray-50 to-soft-gray-100 flex items-center justify-center">
                            <i class="bi bi-building text-2xl sm:text-3xl text-soft-gray-300"></i>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-xl bg-indigo-100 text-indigo-700">
                                <i class="bi bi-person-workspace text-[10px]"></i>
                                Intrapreneur
                            </span>

                            @if($company->category)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-xl bg-purple-100 text-purple-700">
                                {{ $company->category->name }}
                            </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-soft-gray-900 tracking-tight leading-tight">
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

        {{-- ═══ 2-COLUMN LAYOUT ═══ --}}
        <div class="grid grid-cols-1 gap-6 md:gap-8 items-start" id="biz-layout-grid">
            {{-- LEFT COLUMN: Company Content --}}
            <div class="space-y-6 min-w-0">
                {{-- Company Overview Card --}}
                <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border border-soft-gray-100">
                    <div class="p-4 sm:p-6 lg:p-8">
                        {{-- About Company --}}
                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-soft-gray-900 uppercase tracking-wider mb-3">About This Company</h4>
                            <p class="text-base text-soft-gray-700 leading-relaxed w-full max-w-[1600px] 2xl:max-w-[1720px]">
                                {{ $company->job_description ?: 'No job description or company overview provided.' }}
                            </p>
                        </div>

                        {{-- Metadata Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-6 gap-x-4 md:gap-x-6 lg:gap-x-8 py-8 mb-4 border-y border-gray-100">
                            {{-- Company Scale --}}
                            <div class="flex items-start gap-4 sm:gap-2 md:gap-4 border-r border-gray-100 pr-4 md:pr-6 lg:pr-8">
                                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50 shadow-sm flex-shrink-0">
                                    <i class="bi bi-graph-up-arrow text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Company Scale</p>
                                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $company->company_scale ?: 'Undisclosed' }}</h5>
                                    <p class="text-[9px] text-gray-400 font-medium mt-0.5">Scale of operations</p>
                                </div>
                            </div>

                            {{-- Started Working --}}
                            <div class="flex items-start gap-4 sm:gap-2 md:gap-4 border-r border-gray-100 pr-4 md:pr-6 lg:pr-8">
                                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shadow-sm flex-shrink-0">
                                    <i class="bi bi-calendar-check-fill text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Started Working</p>
                                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">
                                        {{ $company->year_started_working ? 'Year ' . $company->year_started_working : 'Timeline Undisclosed' }}
                                    </h5>
                                    <p class="text-[9px] text-gray-400 font-medium mt-0.5">Employment inception</p>
                                </div>
                            </div>

                            {{-- Job Position --}}
                            <div class="flex items-start gap-4 sm:gap-2 md:gap-4">
                                <div class="w-11 h-11 sm:w-9 sm:h-9 md:w-11 md:h-11 rounded-lg bg-orange-50 text-uco-orange-600 flex items-center justify-center border border-orange-100/50 shadow-sm flex-shrink-0">
                                    <i class="bi bi-person-badge-fill text-lg sm:text-sm md:text-lg"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Job Role / Level</p>
                                    <h5 class="text-sm sm:text-xs md:text-sm lg:text-base font-extrabold text-gray-800">{{ $company->position ?: 'Not specified' }}</h5>
                                    <p class="text-[9px] text-gray-400 font-medium mt-0.5">
                                        {{ $company->level_position ? $company->level_position : 'Career Track' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Achievements & Contributions Card --}}
                @if ($company->achievement || $canManage)
                    <div 
                        x-data="{
                            achievements: {{ json_encode($company->achievements_list) }},
                            loading: false,
                            deleteAchievement(index) {
                                if (!confirm('Are you sure you want to delete this achievement?')) return;
                                this.loading = true;
                                fetch('{{ route('intrapreneurs.delete_achievement', $company) }}', {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ index: index })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => { throw err; });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        this.achievements = data.achievements;
                                        $dispatch('notify', { message: 'Achievement deleted successfully!', type: 'success' });
                                    } else {
                                        $dispatch('notify', { message: data.message || 'Failed to delete achievement.', type: 'error' });
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                    $dispatch('notify', { message: 'An error occurred while deleting.', type: 'error' });
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                            }
                        }"
                        class="bg-white shadow-lg sm:rounded-lg border border-soft-gray-100 overflow-hidden"
                    >
                        {{-- Section Header — same structure as "Our Products" --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-5 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-uco-orange-500 rounded-full flex-shrink-0"></div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-widest text-uco-orange-500 mb-0.5">Career Highlights</p>
                                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Achievements & Contributions</h2>
                                </div>
                            </div>
                            @if ($canManage)
                                <a
                                    href="{{ route('intrapreneurs.create_achievement', $company) }}"
                                    class="btn-uco btn-uco-primary w-full sm:w-auto text-center"
                                >
                                    <i class="bi bi-plus-lg"></i>
                                    Add Achievement
                                </a>
                            @endif
                        </div>

                        {{-- Content Body --}}
                        <div class="p-6">
                            {{-- Achievements Stack --}}
                            <div class="space-y-3">
                                <template x-for="(item, index) in achievements" :key="index">
                                    <div class="group relative flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-orange-200 hover:shadow-md transition-all duration-300">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-uco-orange-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <i class="bi bi-trophy-fill text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 leading-relaxed font-normal" x-text="item"></p>
                                        </div>
                                        @if ($canManage)
                                            <button
                                                @click="deleteAchievement(index)"
                                                class="btn-uco btn-uco-danger btn-uco-sm opacity-0 group-hover:opacity-100 transition-opacity"
                                                title="Delete Achievement"
                                            >
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        @endif
                                    </div>
                                </template>

                                {{-- Empty State --}}
                                <template x-if="achievements.length === 0">
                                    <div class="flex items-center justify-center py-6 px-4 bg-gray-50/50 rounded-lg border border-dashed border-gray-200">
                                        <span class="text-sm text-gray-500">No achievements added yet. Start adding milestones and contributions.</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: Student Profile Sidebar --}}
            <div class="space-y-6 min-w-0">
                {{-- Employee Profile Section --}}
                @if($student)
                    {{-- ✨ Elegant Employee Card --}}
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden transition hover:shadow-md duration-300 group/card hover:border-orange-200 cursor-pointer">
                        {{-- Invisible link that stretches over the whole card --}}
                        <a href="{{ route('users.show', $student) }}" class="absolute inset-0 z-10" aria-label="View {{ $student->name }} Profile"></a>

                        {{-- Arrow Icon --}}
                        <div class="absolute top-4 right-4 text-gray-300 group-hover/card:text-orange-500 transition-colors z-10">
                            <i class="bi bi-box-arrow-up-right text-xs"></i>
                        </div>

                        {{-- Section Title --}}
                        <div class="flex items-center gap-2 mb-6 relative z-10 pointer-events-none">
                            <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
                            <h4 class="text-base font-black uppercase tracking-[0.15em] text-gray-700">Employed <span class="uco-text-gradient-orange">Student</span></h4>
                        </div>

                        {{-- Header: Avatar & Name --}}
                        <div class="flex items-center gap-5 relative z-10 pointer-events-none">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-sm overflow-hidden flex-shrink-0 flex items-center justify-center">
                                @if ($studentPhotoUrl)
                                    <x-premium-image :src="$studentPhotoUrl" :alt="$student->name" class="w-full h-full" />
                                @else
                                    <span class="text-3xl font-black opacity-20 select-none">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg md:text-xl font-extrabold text-gray-900 leading-tight tracking-tight truncate">{{ $student->name }}</h2>
                                <p class="text-gray-400 font-bold text-[11px] mt-1 tracking-[0.1em] uppercase">{{ $student->display_status ?: 'Active' }}</p>
                            </div>
                        </div>

                            <div class="w-full h-px bg-gray-100 my-5"></div>

                            {{-- Academic Details --}}
                            <div class="space-y-3">
                                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-3">Academic Details</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-[13px] font-medium text-gray-500">Major</span>
                                    <span class="text-[13px] font-bold text-gray-900">{{ $student->major ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[13px] font-medium text-gray-500">Join UC Online</span>
                                    <span class="text-[13px] font-bold text-gray-900">{{ $student->year_of_enrollment ?: '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[13px] font-medium text-gray-500">Focus</span>
                                    <span class="text-[13px] font-bold text-gray-900">Intrapreneur</span>
                                </div>
                            </div>

                            {{-- Contacts --}}
                            @if($student->whatsapp || $student->email || $student->linkedin)
                                <div class="w-full h-px bg-gray-100 my-5 relative z-10 pointer-events-none"></div>
                                <div class="space-y-4 relative z-20">
                                    @if($student->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->whatsapp) }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                                    <i class="bi bi-whatsapp text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">WhatsApp</p>
                                                    <p class="text-sm font-bold text-gray-800 group-hover:text-green-600 transition-colors leading-tight mt-0.5">{{ $student->whatsapp }}</p>
                                                </div>
                                            </div>
                                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-green-500 transition-colors text-sm"></i>
                                        </a>
                                    @endif

                                    @if($student->email)
                                        <a href="mailto:{{ $student->email }}" class="flex items-center justify-between group cursor-pointer">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                                    <i class="bi bi-envelope text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Email</p>
                                                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors leading-tight mt-0.5">{{ $student->email }}</p>
                                                </div>
                                            </div>
                                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-blue-500 transition-colors text-sm"></i>
                                        </a>
                                    @endif

                                    @if($student->linkedin)
                                        <a href="{{ str_starts_with($student->linkedin, 'http') ? $student->linkedin : 'https://linkedin.com/in/' . $student->linkedin }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                                    <i class="bi bi-linkedin text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">LinkedIn</p>
                                                    <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors leading-tight mt-0.5">{{ $student->linkedin }}</p>
                                                </div>
                                            </div>
                                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-indigo-500 transition-colors text-sm"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{-- Skills --}}
                            @if($student->skills->count() > 0)
                                <div class="w-full h-px bg-gray-100 my-5 relative z-10 pointer-events-none"></div>
                                <div>
                                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-4">Skills</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($student->skills as $skill)
                                            <span class="px-3 py-1.5 bg-gray-50 border border-gray-100 text-gray-600 rounded-xl text-[10px] font-bold uppercase cursor-default">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
    </div>

    {{-- Grid Layout Helper Styles --}}
    <style>
        @media (min-width: 768px) {
            #biz-layout-grid {
                grid-template-columns: 65fr 35fr;
            }
        }
    </style>
</x-app-layout>
