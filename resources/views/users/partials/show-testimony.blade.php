{{-- --- Profile page testimony card (with an edit link for the person themselves) --- --}}
<div class="reveal-on-scroll space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <span class="h-6 w-1.5 flex-shrink-0 rounded-full bg-gradient-to-b from-[#f7931e] to-[#fdb913]"></span>
            <h2 class="text-xl font-black leading-none tracking-tight text-gray-950">
                Student <span class="uco-text-gradient-orange">Testimony</span>
            </h2>
        </div>
        @if (auth()->id() === $user->id)
            <a href="{{ route('uc-testimonies.my') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3.5 py-1.5 text-xs font-bold text-orange-700 shadow-sm transition-all hover:bg-orange-100">
                <i class="bi bi-pencil-square"></i> Edit My Testimony
            </a>
        @endif
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300 hover:border-orange-200 hover:shadow-lg md:p-8">
        {{-- --- Background Decorative Quote Mark --- --}}
        <div class="pointer-events-none absolute -bottom-10 -right-4 select-none opacity-5">
            <i class="bi bi-quote text-[200px] leading-none text-[#f7931e]"></i>
        </div>

        <div class="relative z-10 space-y-6">
            {{-- --- Quote Text --- --}}
            <p class="relative text-xs font-normal leading-relaxed text-gray-600 sm:text-sm">
                "{{ $user->testimony ?: 'Universitas Ciputra Online Learning helps me develop business capabilities and entrepreneurial mindset while managing my day-to-day operations.' }}"
            </p>

            {{-- --- Student Profile Signature Footer --- --}}
            <div class="flex items-center gap-3.5 border-t border-gray-100 pt-4">
                @if ($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" class="h-10 w-10 rounded-full border-2 border-orange-400 object-cover shadow-sm">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-tr from-orange-500 to-amber-400 text-sm font-extrabold text-white shadow-sm">
                        {{ $user->initial }}
                    </div>
                @endif
                <div>
                    <h4 class="text-sm font-extrabold leading-tight text-gray-900">{{ $user->name }}</h4>
                    <p class="text-xs font-medium text-gray-500">
                        {{ $user->isIntrapreneur() ? 'Intrapreneur' : 'Entrepreneur' }} • {{ $user->major ?? 'Universitas Ciputra' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
