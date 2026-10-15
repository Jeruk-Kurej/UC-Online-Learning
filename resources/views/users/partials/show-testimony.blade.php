{{-- --- Profile page testimony card (with an edit link for the person themselves) --- --}}
<div class="space-y-6 reveal-on-scroll">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-2.5">
            <span class="w-1.5 h-6 bg-gradient-to-b from-[#f7931e] to-[#fdb913] rounded-full flex-shrink-0"></span>
            <h2 class="text-xl font-black text-gray-950 tracking-tight leading-none">
                Student <span class="uco-text-gradient-orange">Testimony</span>
            </h2>
        </div>
        @if(auth()->id() === $user->id)
            <a href="{{ route('uc-testimonies.my') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-orange-200 bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs transition-all shadow-sm">
                <i class="bi bi-pencil-square"></i> Edit My Testimony
            </a>
        @endif
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.02)] hover:border-orange-200 hover:shadow-lg transition-all duration-300 relative overflow-hidden">
        {{-- Background Decorative Quote Mark --}}
        <div class="absolute -right-4 -bottom-10 opacity-5 pointer-events-none select-none">
            <i class="bi bi-quote text-[200px] text-[#f7931e] leading-none"></i>
        </div>

        <div class="relative z-10 space-y-6">
            {{-- Quote Text --}}
            <p class="text-xs sm:text-sm text-gray-600 font-normal leading-relaxed relative">
                "{{ $user->testimony ?: 'Universitas Ciputra Online Learning helps me develop business capabilities and entrepreneurial mindset while managing my day-to-day operations.' }}"
            </p>

            {{-- Student Profile Signature Footer --}}
            <div class="flex items-center gap-3.5 pt-4 border-t border-gray-100">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-orange-400 shadow-sm">
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-amber-400 text-white font-extrabold flex items-center justify-center text-sm shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h4 class="text-sm font-extrabold text-gray-900 leading-tight">{{ $user->name }}</h4>
                    <p class="text-xs text-gray-500 font-medium">
                        {{ $user->isIntrapreneur() ? 'Intrapreneur' : 'Entrepreneur' }} • {{ $user->major ?? 'Universitas Ciputra' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
