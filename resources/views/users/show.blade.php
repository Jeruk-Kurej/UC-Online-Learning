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
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm text-center relative overflow-hidden transition hover:shadow-md duration-300">
                    <div class="relative z-10">
                        <div class="w-28 h-28 md:w-36 md:h-36 rounded-2xl border border-gray-100 shadow-sm mx-auto overflow-hidden bg-gray-50 flex items-center justify-center mb-6 hover:scale-105 transition duration-500">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 select-none">
                                    <span class="text-5xl font-black opacity-20 select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                        </div>
                        <h2 class="text-2xl font-extrabold text-gray-900 leading-tight tracking-tight">{{ $user->name }}</h2>
                        <p class="text-gray-500 font-bold text-xs mt-1.5 tracking-wider uppercase opacity-80">{{ $user->major ?: 'UCO Student' }}</p>
                        
                        {{-- Data Info Cards --}}
                        <div class="mt-8 grid grid-cols-1 gap-3 text-left border-t border-gray-100 pt-6">
                            <div class="flex items-center justify-between bg-gray-50/50 border border-gray-100 rounded-xl p-3.5">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Peminatan</span>
                                <span class="text-xs font-bold text-gray-800">{{ $user->major ?: '-' }}</span>
                            </div>
                            @if($user->year_of_enrollment)
                                <div class="flex items-center justify-between bg-gray-50/50 border border-gray-100 rounded-xl p-3.5">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Enrollment</span>
                                    <span class="text-xs font-bold text-gray-800">{{ $user->year_of_enrollment }}</span>
                                </div>
                            @endif
                            @if($user->graduate_year)
                                <div class="flex items-center justify-between bg-gray-50/50 border border-gray-100 rounded-xl p-3.5">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Graduate</span>
                                    <span class="text-xs font-bold text-gray-800">{{ $user->graduate_year }}</span>
                                </div>
                            @endif
                            @if($user->student_status)
                                <div class="flex items-center justify-between bg-gray-50/50 border border-gray-100 rounded-xl p-3.5">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</span>
                                    <span class="text-xs font-bold text-gray-800">{{ $user->student_status }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact Info Card --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm space-y-5 transition hover:shadow-md duration-300">
                    <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <i class="bi bi-envelope text-lg text-gray-400 flex-shrink-0"></i>
                            <span class="text-gray-700 font-bold truncate">{{ $user->email }}</span>
                        </div>
                        @if($user->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-emerald-50/30 transition duration-300 group">
                                <i class="bi bi-whatsapp text-lg text-emerald-500 flex-shrink-0"></i>
                                <span class="text-gray-700 font-bold group-hover:text-emerald-600 transition-colors">{{ $user->whatsapp }}</span>
                            </a>
                        @endif
                        @if($user->linkedin)
                            <a href="{{ $user->linkedin }}" target="_blank" class="flex items-center gap-3.5 text-xs bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-blue-50/30 transition duration-300 group">
                                <i class="bi bi-linkedin text-lg text-blue-600 flex-shrink-0"></i>
                                <span class="text-gray-700 font-bold group-hover:text-blue-600 transition-colors">LinkedIn Profile</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Core Skills Card --}}
                @if($user->skills->count() > 0)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm transition hover:shadow-md duration-300">
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
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                                <a href="{{ route('businesses.show', $biz) }}" class="grid grid-cols-1 md:grid-cols-12 h-full">
                                    <div class="md:col-span-4 lg:col-span-3 bg-gray-50 flex items-center justify-center relative border-b md:border-b-0 md:border-r border-gray-100 overflow-hidden min-h-[160px] md:min-h-0">
                                        @if($biz->logo_url)
                                            <img src="{{ $biz->logo_url }}" class="w-full h-full max-h-[140px] max-w-[140px] object-contain p-4 group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                <span class="text-6xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-4 right-4 rounded-xl bg-white/90 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-gray-600 border border-white/50 backdrop-blur-md shadow-sm">
                                            Entrepreneur
                                        </div>
                                    </div>
                                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 flex flex-col justify-between space-y-4 min-w-0 h-full">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-black text-gray-900 text-2xl leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                            </div>
                                            @if($biz->category)
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">{{ $biz->category->name }}</span>
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
                                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group flex flex-col h-full">
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
                                                <div class="absolute top-4 right-4 rounded-xl bg-white/90 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-gray-600 border border-white/50 backdrop-blur-md shadow-sm">
                                                    Entrepreneur
                                                </div>
                                            </div>
                                            <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                                    </div>
                                                    @if($biz->category)
                                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block">{{ $biz->category->name }}</span>
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
                            <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                                <a href="{{ route('businesses.show', $biz) }}" class="grid grid-cols-1 md:grid-cols-12 h-full">
                                    <div class="md:col-span-4 lg:col-span-3 bg-gray-50 flex items-center justify-center relative border-b md:border-b-0 md:border-r border-gray-100 overflow-hidden min-h-[160px] md:min-h-0">
                                        @if($biz->logo_url)
                                            <img src="{{ $biz->logo_url }}" class="w-full h-full max-h-[140px] max-w-[140px] object-contain p-4 group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-200 select-none">
                                                <span class="text-6xl font-black opacity-20 tracking-tighter">{{ substr($biz->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-4 right-4 rounded-xl bg-white/90 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-gray-600 border border-white/50 backdrop-blur-md shadow-sm">
                                            Team Member
                                        </div>
                                    </div>
                                    <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 flex flex-col justify-between space-y-4 min-w-0 h-full">
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-black text-gray-900 text-2xl leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">
                                                {{ $biz->pivot->position ?? 'Co-Owner' }}
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
                                    <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 hover:shadow-lg transition-all duration-300 group flex flex-col h-full">
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
                                                <div class="absolute top-4 right-4 rounded-xl bg-white/90 px-3 py-1 text-[9px] font-black uppercase tracking-wider text-gray-600 border border-white/50 backdrop-blur-md shadow-sm">
                                                    Team Member
                                                </div>
                                            </div>
                                            <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between">
                                                        <h3 class="font-bold text-gray-900 text-lg leading-snug group-hover:text-gray-700 transition">{{ $biz->name }}</h3>
                                                    </div>
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block">
                                                        {{ $biz->pivot->position ?? 'Co-Owner' }}
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