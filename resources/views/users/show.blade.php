<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header / Page Title (No Back Button, Premium 2026 Typography) --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-3 mb-1">
                    <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">{{ $user->name }}</h1>
                    <span class="px-3 py-1.5 bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-600 rounded-xl border border-gray-200/60 shadow-sm">{{ $user->role }}</span>
                </div>
                <p class="text-gray-500 font-normal text-sm md:text-base">{{ $user->email }} • {{ $user->display_status }}</p>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-bold rounded-xl transition shadow-sm flex items-center gap-2 hover:border-gray-300">
                        <i class="bi bi-pencil text-sm"></i>
                        <span>Edit User</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Left Section: Complete User Card & Professional details --}}
            <div class="space-y-8">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden transition hover:shadow-md duration-300">
                    {{-- Header: Avatar & Name --}}
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex-shrink-0 bg-gray-50 flex items-center justify-center">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-black opacity-20 select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl md:text-2xl font-extrabold text-gray-900 leading-tight tracking-tight truncate">{{ $user->name }}</h2>
                            <p class="text-gray-400 font-bold text-[11px] mt-1 tracking-[0.1em] uppercase">{{ $user->student_status ?: 'Active' }}</p>
                        </div>
                    </div>

                    <div class="w-full h-px bg-gray-100 my-6"></div>

                    {{-- Academic Details --}}
                    <div class="space-y-4">
                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-4">Academic Details</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Major</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $user->major ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Batch</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $user->year_of_enrollment ?: '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-medium text-gray-500">Focus</span>
                            <span class="text-[13px] font-bold text-gray-900">{{ $user->current_status ?: (ucfirst($user->role) ?: '-') }}</span>
                        </div>
                    </div>

                    {{-- Contacts / WhatsApp --}}
                    @if($user->whatsapp || $user->email)
                    <div class="w-full h-px bg-gray-100 my-6"></div>
                    <div class="space-y-4">
                        @if($user->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" class="flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                                    <i class="bi bi-whatsapp text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">WhatsApp</p>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-green-600 transition-colors leading-tight mt-0.5">{{ $user->whatsapp }}</p>
                                </div>
                            </div>
                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-green-500 transition-colors text-sm"></i>
                        </a>
                        @endif

                        @if($user->email && !$user->whatsapp)
                        <a href="mailto:{{ $user->email }}" class="flex items-center justify-between group cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                                    <i class="bi bi-envelope-fill text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Email</p>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors leading-tight mt-0.5 truncate max-w-[150px]">{{ $user->email }}</p>
                                </div>
                            </div>
                            <i class="bi bi-arrow-up-right text-gray-300 group-hover:text-blue-500 transition-colors text-sm"></i>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Documents Card --}}
                @if($user->cv_url || $user->activities_doc_url || $user->expertise_certification_url)
                    <div class="bg-white border border-gray-100 rounded-lg p-6 md:p-8 shadow-sm space-y-5 transition hover:shadow-md duration-300">
                        <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Documents</h3>
                        <div class="space-y-3">
                            @if($user->cv_url)
                                <a href="{{ $user->cv_url }}" target="_blank" class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-orange-50/30 transition duration-300 group">
                                    <i class="bi bi-file-earmark-person text-lg text-orange-500 flex-shrink-0"></i>
                                    <span class="text-gray-700 font-bold group-hover:text-orange-600 transition-colors">Curriculum Vitae (CV)</span>
                                </a>
                            @endif
                            @if($user->activities_doc_url)
                                @php
                                    $activitiesUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->activities_doc_url)));
                                @endphp
                                @foreach($activitiesUrls as $index => $actUrl)
                                    <a href="{{ $actUrl }}" target="_blank" class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-orange-50/30 transition duration-300 group">
                                        <i class="bi bi-file-earmark-image text-lg text-orange-500 flex-shrink-0"></i>
                                        <span class="text-gray-700 font-bold group-hover:text-orange-600 transition-colors">Professional Activities {{ count($activitiesUrls) > 1 ? '#' . ($index + 1) : '' }}</span>
                                    </a>
                                @endforeach
                            @endif
                            @if($user->expertise_certification_url)
                                @php
                                    $certUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->expertise_certification_url)));
                                @endphp
                                @foreach($certUrls as $index => $certUrl)
                                    <a href="{{ $certUrl }}" target="_blank" class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-orange-50/30 transition duration-300 group">
                                        <i class="bi bi-patch-check text-lg text-orange-500 flex-shrink-0"></i>
                                        <span class="text-gray-700 font-bold group-hover:text-orange-600 transition-colors">Expertise Certification {{ count($certUrls) > 1 ? '#' . ($index + 1) : '' }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Core Skills Card --}}
                @if($user->skills->count() > 0)
                    <div class="bg-white border border-gray-100 rounded-lg p-6 md:p-8 shadow-sm transition hover:shadow-md duration-300">
                        <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Core Competencies</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->skills as $skill)
                                <span class="px-3 py-1.5 bg-gray-50 text-gray-600 rounded-xl text-[10px] font-bold border border-gray-100 hover:bg-white hover:border-gray-200 transition-all cursor-default select-none">
                                    {{ $skill->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Section: Businesses Founded / Catalog Cards --}}
            <div class="lg:col-span-2 space-y-10">
                {{-- Employment History (As Intrapreneur) --}}
                @if($user->companies->count() > 0)
                    <div class="space-y-6">
                        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-gray-800 rounded-full"></span>
                            Employment History
                        </h2>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($user->companies as $company)
                                <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group p-6 md:p-8">
                                    <div class="flex flex-col md:flex-row gap-6 items-start">
                                        @if($company->logo_url)
                                            <div class="w-20 h-20 bg-gray-50 flex items-center justify-center border border-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                                <img src="{{ $company->logo_url }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition duration-500">
                                            </div>
                                        @endif
                                        <div class="flex-1 space-y-3">
                                            <div>
                                                <h3 class="font-black text-gray-900 text-xl leading-snug group-hover:text-gray-700 transition">{{ $company->name }}</h3>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    @if($company->position)
                                                        <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full">
                                                            {{ $company->position }}
                                                        </span>
                                                    @endif
                                                    @if($company->level_position)
                                                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">
                                                            {{ $company->level_position }}
                                                        </span>
                                                    @endif
                                                    @if($company->category)
                                                        <span class="text-xs font-bold text-gray-500 bg-gray-50 px-2.5 py-0.5 rounded-full border border-gray-100">
                                                            {{ $company->category->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($company->job_description)
                                                <div class="text-sm text-gray-600 font-normal leading-relaxed">
                                                    <strong>Job Description:</strong>
                                                    <p class="mt-1 text-gray-500">{{ $company->job_description }}</p>
                                                </div>
                                            @endif

                                            @if($company->achievement)
                                                <div class="text-sm text-gray-600 font-normal leading-relaxed">
                                                    <strong>Achievements:</strong>
                                                    <p class="mt-1 text-gray-500">{{ $company->achievement }}</p>
                                                </div>
                                            @endif

                                            <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-gray-400 pt-3 border-t border-gray-50">
                                                @if($company->year_started_working)
                                                    <span>Started Working: {{ $company->year_started_working }}</span>
                                                @endif
                                                @if($company->company_scale)
                                                    <span>Company Scale: {{ $company->company_scale }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Owned Businesses (As Entrepreneur) --}}
                @if($user->businesses->count() > 0)
                    <div class="space-y-6">
                        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-gray-800 rounded-full"></span>
                            Businesses Founded
                        </h2>
                        @if($user->businesses->count() === 1)
                            {{-- Premium Full-Width Horizontal Card for visual balance --}}
                            @php $biz = $user->businesses->first(); @endphp
                            <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                                <a href="{{ route('businesses.show', $biz) }}" class="grid grid-cols-1 md:grid-cols-12 h-full">
                                    <div class="md:col-span-4 lg:col-span-3 bg-gray-50 flex items-center justify-center relative border-b md:border-b-0 md:border-r border-gray-100 overflow-hidden min-h-[160px] md:min-h-0">
                                        @if($biz->logo_url)
                                            <img src="{{ $biz->logo_url }}" class="w-full h-full max-h-[140px] max-w-[140px] object-contain p-4 group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                <span class="text-6xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 flex flex-col justify-between space-y-4 min-w-0 h-full">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-black text-gray-900 text-2xl leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                            </div>
                                            @if($biz->category)
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                                                    {{ $biz->category->name }}
                                                </span>
                                            @endif
                                            @if($biz->unique_value_proposition || $biz->description)
                                                <p class="text-sm text-gray-500 font-normal leading-relaxed mt-1">
                                                    {{ $biz->unique_value_proposition ?? $biz->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-50 pt-4 mt-auto">
                                            @if($biz->city)
                                                <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                                    <i class="bi bi-geo-alt text-gray-400"></i>
                                                    {{ $biz->city }}
                                                </span>
                                            @endif
                                            <span class="text-sm font-bold text-gray-800 flex items-center gap-1.5 group-hover:text-gray-900 transition duration-300">
                                                View Details <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @else
                            {{-- Default Grid for 2+ businesses --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($user->businesses as $biz)
                                    <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group flex flex-col h-full">
                                        <a href="{{ route('businesses.show', $biz) }}" class="block flex-1 flex flex-col">
                                            {{-- Catalog Aspect Ratio Box --}}
                                            <div class="aspect-video bg-gray-50 flex items-center justify-center relative border-b border-gray-100 overflow-hidden">
                                                @if($biz->logo_url)
                                                    <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                        <span class="text-5xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                                    </div>
                                                    @if($biz->category)
                                                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                                                            {{ $biz->category->name }}
                                                        </span>
                                                    @endif
                                                    @if($biz->unique_value_proposition || $biz->description)
                                                        <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-1">
                                                            {{ $biz->unique_value_proposition ?? $biz->description }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="flex items-center justify-between border-t border-gray-50 pt-3.5 mt-auto">
                                                    @if($biz->city)
                                                        <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                                            <i class="bi bi-geo-alt text-gray-400"></i>
                                                            {{ $biz->city }}
                                                        </span>
                                                    @endif
                                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1 group-hover:text-gray-900 transition duration-300">
                                                        View Details <i class="bi bi-arrow-right"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Member of Businesses (As Team Involvement) --}}
                @if($user->memberOfBusinesses->count() > 0)
                    <div class="space-y-6">
                        <h2 class="text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-gray-800 rounded-full"></span>
                            Team Involvement
                        </h2>
                        @if($user->memberOfBusinesses->count() === 1)
                            {{-- Premium Full-Width Horizontal Card for visual balance --}}
                            @php $biz = $user->memberOfBusinesses->first(); @endphp
                            <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                                <a href="{{ route('businesses.show', $biz) }}" class="grid grid-cols-1 md:grid-cols-12 h-full">
                                    <div class="md:col-span-4 lg:col-span-3 bg-gray-50 flex items-center justify-center relative border-b md:border-b-0 md:border-r border-gray-100 overflow-hidden min-h-[160px] md:min-h-0">
                                        @if($biz->logo_url)
                                            <img src="{{ $biz->logo_url }}" class="w-full h-full max-h-[140px] max-w-[140px] object-contain p-4 group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                <span class="text-6xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 flex flex-col justify-between space-y-4 min-w-0 h-full">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-black text-gray-900 text-2xl leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                            </div>
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-blue-600 mt-1 block">
                                                {{ $biz->pivot->position ?? 'Co-Owner' }} (Team Member)
                                            </span>
                                            @if($biz->unique_value_proposition || $biz->description)
                                                <p class="text-sm text-gray-500 font-normal leading-relaxed mt-1">
                                                    {{ $biz->unique_value_proposition ?? $biz->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-50 pt-4 mt-auto">
                                            @if($biz->city)
                                                <span class="flex items-center gap-1.5 text-xs font-bold text-gray-500">
                                                    <i class="bi bi-geo-alt text-gray-400"></i>
                                                    {{ $biz->city }}
                                                </span>
                                            @endif
                                            <span class="text-sm font-bold text-gray-800 flex items-center gap-1.5 group-hover:text-gray-900 transition duration-300">
                                                View Details <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @else
                            {{-- Default Grid for 2+ businesses --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($user->memberOfBusinesses as $biz)
                                    <div class="bg-white border border-gray-100 rounded-lg overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group flex flex-col h-full">
                                        <a href="{{ route('businesses.show', $biz) }}" class="block flex-1 flex flex-col">
                                            {{-- Catalog Style --}}
                                            <div class="aspect-video bg-gray-50 flex items-center justify-center relative border-b border-gray-100 overflow-hidden">
                                                @if($biz->logo_url)
                                                    <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                        <span class="text-5xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                                    </div>
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-blue-600 mt-1 block">
                                                        {{ $biz->pivot->position ?? 'Co-Owner' }} (Team Member)
                                                    </span>
                                                    @if($biz->unique_value_proposition || $biz->description)
                                                        <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-1">
                                                            {{ $biz->unique_value_proposition ?? $biz->description }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="flex items-center justify-between border-t border-gray-50 pt-3.5 mt-auto">
                                                    @if($biz->city)
                                                        <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                                            <i class="bi bi-geo-alt text-gray-400"></i>
                                                            {{ $biz->city }}
                                                        </span>
                                                    @endif
                                                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1 group-hover:text-gray-900 transition duration-300">
                                                        View Details <i class="bi bi-arrow-right"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>