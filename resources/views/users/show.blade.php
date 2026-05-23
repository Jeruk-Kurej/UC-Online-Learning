<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    @php
        $activitiesUrls = [];
        if ($user->activities_doc_url) {
            $rawUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->activities_doc_url)));
            foreach ($rawUrls as $rawUrl) {
                if (str_contains($rawUrl, 'drive.google.com') || str_contains($rawUrl, 'docs.google.com')) {
                    if (preg_match('/(?:id=|\/d\/)([a-zA-Z0-9-_]{25,})/', $rawUrl, $matches)) {
                        $activitiesUrls[] = route('google-drive-image', ['id' => $matches[1]]);
                    } else {
                        $activitiesUrls[] = $rawUrl;
                    }
                } else {
                    $activitiesUrls[] = $rawUrl;
                }
            }
        }

        $directoryView = match (true) {
            $user->isIntrapreneur() => 'intrapreneur',
            $user->isEntrepreneur() => 'entrepreneur',
            $user->companies->isNotEmpty() => 'intrapreneur',
            default => 'entrepreneur',
        };
        $directoryUrl = route('businesses.index', ['view' => $directoryView]);
    @endphp

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumbs (matches business / intrapreneur detail pages) --}}
        <nav class="flex mb-8 text-sm font-medium" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ $directoryUrl }}" class="text-gray-400 hover:text-uco-orange-500 transition">Directory</a>
                </li>
                <li class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                    </svg>
                    <span class="text-gray-900">{{ $user->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Side: Vertical Profile Column (4 cols) --}}
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
                <!-- Profile details card -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-100 p-6 md:p-8 flex flex-col space-y-6">
                    <!-- Profile Photo & Meta Side-by-Side -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                        <div class="relative w-28 h-28 md:w-32 md:h-32 rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center shadow-sm flex-shrink-0">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl font-black text-slate-400 select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>

                        <div class="flex-1 text-center sm:text-left pt-2">
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-tight mb-1">{{ $user->name }}</h1>
                            <p class="text-slate-500 font-medium text-xs break-all">{{ $user->email }}</p>
                            
                            {{-- Edit Button (Admin only) --}}
                            @if(Auth::check() && Auth::user()->isAdmin())
                                <div class="mt-3">
                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 hover:text-slate-900 text-xs font-bold rounded-xl transition shadow-sm">
                                        <i class="bi bi-pencil text-xs"></i>
                                        <span>Edit User</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Details Block -->
                    <div class="space-y-1.5 pt-4 border-t border-slate-100">
                        <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Major</span>
                            <span class="text-slate-700 font-extrabold text-right ml-2">{{ $user->major ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Batch</span>
                            <span class="text-slate-700 font-extrabold text-right ml-2">{{ $user->year_of_enrollment ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl border border-transparent">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Focus</span>
                            <span class="text-slate-700 font-extrabold capitalize text-right ml-2">{{ $user->current_status ?: (ucfirst($user->role) ?: '-') }}</span>
                        </div>
                    </div>

                    <!-- Contact Details Block -->
                    @if($user->whatsapp || $user->email)
                        <div class="space-y-1.5 pt-4 border-t border-slate-100">
                            @if($user->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" 
                                   class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl hover:bg-green-50/50 border border-transparent hover:border-green-100/50 transition duration-200 group">
                                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 group-hover:text-green-600 transition-colors">
                                        <i class="bi bi-whatsapp text-green-600 text-sm"></i>
                                        WhatsApp
                                    </span>
                                    <span class="text-slate-700 font-extrabold break-all ml-2 text-right group-hover:text-green-700 transition-colors">
                                        {{ $user->whatsapp }}
                                    </span>
                                </a>
                            @endif
                            @if($user->email)
                                <a href="mailto:{{ $user->email }}" 
                                   class="flex justify-between items-center text-xs p-2 -mx-2 rounded-xl hover:bg-blue-50/50 border border-transparent hover:border-blue-100/50 transition duration-200 group">
                                    <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1.5 group-hover:text-blue-600 transition-colors">
                                        <i class="bi bi-envelope text-blue-600 text-sm"></i>
                                        Email
                                    </span>
                                    <span class="text-slate-700 font-extrabold break-all ml-2 text-right group-hover:text-blue-700 transition-colors">
                                        {{ $user->email }}
                                    </span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Documents Block -->
                    @if($user->expertise_certification_url)
                        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100">
                            @php
                                $certUrls = array_filter(array_map('trim', preg_split('/[;,]+/', $user->expertise_certification_url)));
                            @endphp
                            @foreach($certUrls as $index => $certUrl)
                                <a href="{{ $certUrl }}" target="_blank" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-[10px] font-bold rounded-lg border border-slate-200/60 transition">
                                    <i class="bi bi-patch-check"></i>
                                    <span>Cert {{ count($certUrls) > 1 ? '#' . ($index + 1) : '' }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Core Competencies Block -->
                    @if($user->skills->count() > 0)
                        <div class="space-y-2 pt-4 border-t border-slate-100">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Core Competencies</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($user->skills as $skill)
                                    <span class="px-2 py-0.5 bg-slate-50 text-slate-600 rounded-md text-[9px] font-bold border border-slate-200/40">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Activities / Testimony Section -->
                    <div class="pt-6 border-t border-slate-100">
                        @if(count($activitiesUrls) > 0)
                            {{-- Sleek Carousel --}}
                            <div x-data="{ 
                                activeIndex: 0, 
                                total: {{ count($activitiesUrls) }},
                                autoplayTimer: null,
                                startAutoplay() {
                                    if (this.total > 1) {
                                        this.autoplayTimer = setInterval(() => {
                                            this.activeIndex = (this.activeIndex + 1) % this.total;
                                        }, 3500);
                                    }
                                },
                                resetAutoplay() {
                                    if (this.autoplayTimer) {
                                        clearInterval(this.autoplayTimer);
                                        this.startAutoplay();
                                    }
                                },
                                init() {
                                    this.startAutoplay();
                                }
                            }" class="relative w-full h-[200px] md:h-[240px] bg-slate-50 rounded-2xl overflow-hidden group">
                                <!-- Slides -->
                                <div class="w-full h-full relative bg-slate-100 flex items-center justify-center">
                                    @foreach($activitiesUrls as $index => $url)
                                        <div class="absolute inset-0 w-full h-full transition-opacity duration-[800ms] ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none' }}"
                                             :class="activeIndex === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'">
                                            <img src="{{ $url }}" 
                                                 class="w-full h-full object-cover" 
                                                 alt="Activity Image">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Navigation Arrows -->
                                <template x-if="total > 1">
                                    <div>
                                        <button @click="activeIndex = (activeIndex - 1 + total) % total; resetAutoplay()" 
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-10 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                                            <i class="bi bi-chevron-left text-sm"></i>
                                        </button>
                                        <button @click="activeIndex = (activeIndex + 1) % total; resetAutoplay()" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-800 flex items-center justify-center transition backdrop-blur-sm z-10 border border-slate-200/60 shadow-sm opacity-0 group-hover:opacity-100 duration-200">
                                            <i class="bi bi-chevron-right text-sm"></i>
                                        </button>
                                    </div>
                                </template>

                                <!-- Dots Indicators -->
                                <template x-if="total > 1">
                                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                                        <template x-for="(item, index) in total" :key="index">
                                            <button @click="activeIndex = index; resetAutoplay()" 
                                                    class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                                    :class="activeIndex === index ? 'bg-[#f7931e] w-4' : 'bg-slate-300'"></button>
                                        </template>
                                    </div>
                                </template>

                                <!-- Label -->
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-md bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 border border-slate-200/60 shadow-sm z-10 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-[#f7931e] rounded-full animate-pulse"></span>
                                    Activities
                                </div>
                            </div>
                        @else
                            {{-- Fallback Card: Premium testimonial styling --}}
                            <div class="w-full h-[200px] md:h-[240px] flex flex-col justify-between p-6 bg-slate-50/50 rounded-2xl relative overflow-hidden">
                                <div class="absolute top-0 right-0 transform translate-x-8 -translate-y-8 opacity-[0.05] pointer-events-none select-none">
                                    <i class="bi bi-quote text-[160px] text-[#f7931e]"></i>
                                </div>
                                <div class="relative z-10 space-y-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-4 bg-[#f7931e] rounded-full"></span>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Student Testimony</span>
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium leading-relaxed italic line-clamp-4">
                                        "{{ $user->testimony ?: 'Universitas Ciputra Online Learning helps me develop business capabilities and entrepreneurial mindset while managing my day-to-day operations.' }}"
                                    </p>
                                </div>
                                <div class="relative z-10 flex items-center gap-2 pt-3 border-t border-slate-200">
                                    <div class="w-6 h-6 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-[10px] font-black border border-orange-100">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-500">— {{ $user->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Side: Businesses & Jobs (8 cols) --}}
            <div class="lg:col-span-8 space-y-10">
                {{-- Owned Businesses (As Entrepreneur) --}}
                @if($user->businesses->count() > 0)
                    <div class="space-y-6">
                        <div class="relative mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
                                <h2 class="text-xl font-black text-gray-950 tracking-tight leading-none">Owned <span class="uco-text-gradient-orange">Businesses</span></h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($user->businesses as $biz)
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-orange-200 hover:shadow-lg transition-all duration-300 group flex flex-col sm:flex-row shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative">
                                    <a href="{{ route('businesses.show', $biz) }}" class="block flex-1 flex flex-col sm:flex-row">
                                        {{-- Logo Box on Left --}}
                                        <div class="w-full sm:w-52 h-48 sm:h-auto bg-gray-50 flex items-center justify-center relative border-b sm:border-b-0 sm:border-r border-gray-100 overflow-hidden flex-shrink-0">
                                            @if($biz->logo_url)
                                                <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none absolute inset-0">
                                                    <span class="text-4xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Content Box on Right --}}
                                        <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
                                            <div class="space-y-1.5">
                                                <h3 class="font-extrabold text-gray-900 text-base leading-tight group-hover:text-orange-600 transition-colors line-clamp-1">{{ $biz->name }}</h3>
                                                @if($biz->category)
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                                                        {{ $biz->category->name }}
                                                    </span>
                                                @endif
                                                @if($biz->unique_value_proposition || $biz->description)
                                                    <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                                                        {{ $biz->unique_value_proposition ?? $biz->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto">
                                                @if($biz->city)
                                                    <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                                        <i class="bi bi-geo-alt-fill text-orange-500"></i>
                                                        {{ $biz->city }}
                                                    </span>
                                                @endif
                                                <span class="text-xs font-bold text-gray-800 flex items-center gap-1 group-hover:text-orange-600 transition duration-300">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Member of Businesses (As Team Involvement) --}}
                @if($user->memberOfBusinesses->count() > 0)
                    <div class="space-y-6">
                        <div class="relative mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-6 bg-gradient-to-b from-[#2563eb] to-[#60a5fa] rounded-full flex-shrink-0"></span>
                                <h2 class="text-xl font-black text-gray-950 tracking-tight leading-none">Also <span class="uco-text-gradient-blue">Manages</span></h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($user->memberOfBusinesses as $biz)
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-blue-200 hover:shadow-lg transition-all duration-300 group flex flex-col sm:flex-row shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative">
                                    <a href="{{ route('businesses.show', $biz) }}" class="block flex-1 flex flex-col sm:flex-row">
                                        {{-- Logo Box on Left --}}
                                        <div class="w-full sm:w-52 h-48 sm:h-auto bg-gray-50 flex items-center justify-center relative border-b sm:border-b-0 sm:border-r border-gray-100 overflow-hidden flex-shrink-0">
                                            @if($biz->logo_url)
                                                <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none absolute inset-0">
                                                    <span class="text-4xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Content Box on Right --}}
                                        <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
                                            <div class="space-y-1.5">
                                                <h3 class="font-bold text-gray-950 text-base leading-tight group-hover:text-blue-600 transition-colors line-clamp-1">{{ $biz->name }}</h3>
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-blue-600 mt-1 block">
                                                    {{ $biz->pivot->position ?? 'Co-Owner' }} (Team Member)
                                                </span>
                                                @if($biz->unique_value_proposition || $biz->description)
                                                    <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                                                        {{ $biz->unique_value_proposition ?? $biz->description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto">
                                                @if($biz->city)
                                                    <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                                        <i class="bi bi-geo-alt-fill text-blue-500"></i>
                                                        {{ $biz->city }}
                                                    </span>
                                                @endif
                                                <span class="text-xs font-bold text-gray-800 flex items-center gap-1 group-hover:text-blue-600 transition duration-300">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Employment History (As Intrapreneur) --}}
                @if($user->companies->count() > 0)
                    <div class="space-y-6">
                        <div class="relative mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
                                <h2 class="text-xl font-black text-gray-950 tracking-tight leading-none">Employment <span class="uco-text-gradient-orange">History</span></h2>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($user->companies as $company)
                                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-orange-200 hover:shadow-lg transition-all duration-300 group flex flex-col sm:flex-row shadow-[0_8px_30px_rgb(0,0,0,0.02)] relative">
                                    <a href="{{ route('intrapreneurs.show', $company) }}" class="block flex-1 flex flex-col sm:flex-row">
                                        {{-- Logo Box on Left --}}
                                        <div class="w-full sm:w-52 h-48 sm:h-auto bg-gray-50 flex items-center justify-center relative border-b sm:border-b-0 sm:border-r border-gray-100 overflow-hidden flex-shrink-0">
                                            @if($company->logo_url)
                                                <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none absolute inset-0">
                                                    <span class="text-4xl font-black opacity-20 tracking-tighter">{{ substr($company->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Content Box on Right --}}
                                        <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
                                            <div class="space-y-1.5">
                                                <h3 class="font-extrabold text-gray-900 text-base leading-tight group-hover:text-orange-600 transition-colors line-clamp-1">{{ $company->name }}</h3>
                                                @if($company->position)
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block line-clamp-1">
                                                        {{ $company->position }}@if($company->level_position) · {{ $company->level_position }}@endif
                                                    </span>
                                                @elseif($company->category)
                                                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500 mt-1 block">
                                                        {{ $company->category->name }}
                                                    </span>
                                                @endif
                                                @if($company->job_description)
                                                    <p class="text-xs text-gray-500 font-normal leading-relaxed line-clamp-2 mt-2">
                                                        {{ $company->job_description }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto gap-3">
                                                <div class="flex flex-wrap gap-x-4 gap-y-1 min-w-0">
                                                    @if($company->year_started_working)
                                                        <span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
                                                            <i class="bi bi-calendar-check text-orange-500"></i>
                                                            Since {{ $company->year_started_working }}
                                                        </span>
                                                    @endif
                                                    @if($company->company_scale)
                                                        <span class="text-[11px] font-bold text-gray-400 line-clamp-1" title="{{ $company->company_scale }}">
                                                            {{ $company->company_scale }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-bold text-gray-800 flex items-center gap-1 group-hover:text-orange-600 transition duration-300 flex-shrink-0">
                                                    View Details <i class="bi bi-arrow-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>