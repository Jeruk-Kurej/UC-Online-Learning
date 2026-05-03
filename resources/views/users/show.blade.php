<x-app-layout>
    @section('title', $user->name . ' - ' . ($user->major ?? 'Profile'))

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Header / Breadcrumbs --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('businesses.index') }}" class="w-11 h-11 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-900 transition shadow-sm hover:border-gray-300">
                    <i class="bi bi-chevron-left text-base"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="px-2.5 py-1 bg-gray-100 text-[9px] font-bold uppercase tracking-wider text-gray-600 rounded-lg border border-gray-200/60">{{ $user->role }}</span>
                    </div>
                    <p class="text-gray-500 font-normal text-sm">{{ $user->email }} • {{ $user->display_status }}</p>
                </div>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                        <i class="bi bi-pencil text-sm"></i>
                        <span>Edit User</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Section: User Card & Profile details --}}
            <div class="space-y-8">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-sm text-center relative overflow-hidden transition hover:shadow-md duration-300">
                    <div class="relative z-10">
                        <div class="w-24 h-24 md:w-28 md:h-28 rounded-xl border border-gray-100 shadow-sm mx-auto overflow-hidden bg-gray-50 flex items-center justify-center mb-5 hover:scale-105 transition duration-500">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 select-none">
                                    <span class="text-4xl font-black opacity-20 select-none">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $user->name }}</h2>
                        <p class="text-gray-500 font-medium text-xs mt-1">{{ $user->major ?: 'UCO Student' }}</p>
                        
                        <div class="mt-6 space-y-3.5 text-left border-t border-gray-100 pt-5">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Major</span>
                                <span class="text-xs font-semibold text-gray-800">{{ $user->major ?: '-' }}</span>
                            </div>
                            @if($user->year_of_enrollment)
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Year of Enrollment</span>
                                    <span class="text-xs font-semibold text-gray-800">{{ $user->year_of_enrollment }}</span>
                                </div>
                            @endif
                            @if($user->graduate_year)
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Graduate Year</span>
                                    <span class="text-xs font-semibold text-gray-800">{{ $user->graduate_year }}</span>
                                </div>
                            @endif
                            @if($user->current_status)
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Current Status</span>
                                    <span class="text-xs font-semibold text-gray-800">{{ $user->current_status }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm space-y-4 transition hover:shadow-md duration-300">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-xs bg-gray-50/50 p-3 rounded-xl border border-gray-100">
                            <i class="bi bi-envelope text-gray-400"></i>
                            <span class="text-gray-600 font-medium truncate">{{ $user->email }}</span>
                        </div>
                        @if($user->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp) }}" target="_blank" class="flex items-center gap-3 text-xs bg-gray-50/50 p-3 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-emerald-50/30 transition duration-300">
                                <i class="bi bi-whatsapp text-emerald-500"></i>
                                <span class="text-gray-600 font-medium">{{ $user->whatsapp }}</span>
                            </a>
                        @endif
                        @if($user->linkedin)
                            <a href="{{ $user->linkedin }}" target="_blank" class="flex items-center gap-3 text-xs bg-gray-50/50 p-3 rounded-xl border border-gray-100 hover:border-gray-200 hover:bg-blue-50/30 transition duration-300">
                                <i class="bi bi-linkedin text-blue-600"></i>
                                <span class="text-gray-600 font-medium">LinkedIn Profile</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Core Skills --}}
                @if($user->skills->count() > 0)
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm transition hover:shadow-md duration-300">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Core Competencies</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->skills as $skill)
                                <span class="px-2.5 py-1 bg-gray-50 text-gray-600 rounded-lg text-[9px] font-medium border border-gray-100 hover:bg-white hover:border-gray-200 transition-all cursor-default">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Section: Businesses & Additional Owners --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Owned Businesses (Entrepreneur) --}}
                @if($user->businesses->count() > 0)
                    <div class="space-y-4">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="w-1 h-5 bg-gray-800 rounded-full"></span>
                            Businesses Founded
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($user->businesses as $biz)
                                <a href="{{ route('businesses.show', $biz) }}" class="flex items-center justify-between gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:border-gray-200 hover:shadow-md transition duration-300 group">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-11 h-11 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-500">
                                            @if($biz->logo_url)
                                                <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-1">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300 select-none font-bold">
                                                    {{ substr($biz->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="font-bold text-gray-900 truncate text-sm group-hover:text-gray-700 transition">{{ $biz->name }}</p>
                                            <p class="text-xs text-gray-400 font-medium truncate">{{ $biz->category->name ?? 'Business' }}</p>
                                        </div>
                                    </div>
                                    <i class="bi bi-arrow-right text-gray-300 group-hover:text-gray-600 transition"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Member of Businesses (As Team/Member) --}}
                @if($user->memberOfBusinesses->count() > 0)
                    <div class="space-y-4">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-2">
                            <span class="w-1 h-5 bg-gray-800 rounded-full"></span>
                            Team Involvement
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($user->memberOfBusinesses as $biz)
                                <a href="{{ route('businesses.show', $biz) }}" class="flex items-center justify-between gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:border-gray-200 hover:shadow-md transition duration-300 group">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-11 h-11 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-500">
                                            @if($biz->logo_url)
                                                <img src="{{ $biz->logo_url }}" class="w-full h-full object-contain p-1">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-gray-300 select-none font-bold">
                                                    {{ substr($biz->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <p class="font-bold text-gray-900 truncate text-sm group-hover:text-gray-700 transition">{{ $biz->name }}</p>
                                            <p class="text-xs text-gray-400 font-medium truncate">
                                                {{ $biz->pivot->position ?? 'Team Member' }} · {{ $biz->category->name ?? 'Business' }}
                                            </p>
                                        </div>
                                    </div>
                                    <i class="bi bi-arrow-right text-gray-300 group-hover:text-gray-600 transition"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>