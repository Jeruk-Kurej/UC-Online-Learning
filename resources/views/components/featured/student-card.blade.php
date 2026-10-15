<a href="{{ route('users.show', $student) }}"
   class="reveal-on-scroll uco-premium-card {{ $isIntra ? 'uco-premium-card--blue hover:border-blue-100/70' : 'uco-premium-card--orange hover:border-orange-100/70' }} group flex w-full cursor-pointer flex-col justify-between rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
    <div>
        <div class="flex items-start gap-5">
            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-sm transition-transform duration-500 group-hover:scale-105">
                <x-premium-image :src="$student->profile_photo_url" :alt="$student->full_titled_name" class="size-full">
                    <x-slot name="fallback">
                        <div class="{{ $isIntra ? 'from-blue-50 to-blue-100/40 text-blue-500' : 'from-orange-50 to-orange-100/40 text-uco-orange-500' }} flex h-full w-full items-center justify-center bg-gradient-to-br text-3xl font-black">
                            {{ $student->initial }}
                        </div>
                    </x-slot>
                </x-premium-image>
            </div>

            <div class="min-w-0 flex-1 pt-1">
                <h3 class="line-clamp-1 text-xl font-[900] tracking-tight text-gray-950 transition-colors group-hover:text-blue-600">{{ $student->full_titled_name }}</h3>
                <p class="mt-1 line-clamp-1 text-xs font-semibold text-gray-500">{{ $student->major ?? 'General Studies' }}</p>
                <p class="mt-1.5 flex items-center gap-1.5 text-[11px] font-bold text-gray-400">
                    <span class="{{ $isIntra ? 'bg-blue-400' : 'bg-orange-400' }} h-1.5 w-1.5 rounded-full"></span>
                    Join UC Online {{ $student->year_of_enrollment ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        @if ($isIntra && $highlight)
            <div class="rounded-2xl border border-slate-100/80 bg-slate-50 p-5 transition-all duration-300 group-hover:border-blue-200 group-hover:bg-white">
                <div class="mb-3 flex items-center gap-4">
                    @if ($highlight->logo_url)
                        <img src="{{ $highlight->logo_url }}" class="h-16 w-16 rounded-xl border border-slate-100 bg-white object-contain p-1 shadow-sm" alt="Logo">
                    @endif
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Corporate Role</p>
                        <p class="mt-0.5 line-clamp-1 text-base font-bold tracking-tight text-slate-900">{{ $highlight->name }}</p>
                    </div>
                </div>
                @if ($highlight->category)
                    <span class="mt-1 inline-block rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-600">
                        {{ $highlight->category->name }}
                    </span>
                @endif
                @if ($highlight->job_description)
                    <p class="mt-3 line-clamp-2 text-xs font-medium leading-relaxed text-slate-500">{{ $highlight->job_description }}</p>
                @endif
            </div>
        @elseif(!$isIntra && $highlight)
            <div class="rounded-2xl border border-orange-100/80 bg-orange-50/50 p-5 transition-all duration-300 group-hover:border-orange-200 group-hover:bg-white">
                <div class="mb-3 flex items-center gap-4">
                    @if ($highlight->logo_url)
                        <img src="{{ $highlight->logo_url }}" class="h-16 w-16 rounded-xl border border-orange-100 bg-white object-contain p-1 shadow-sm" alt="Logo">
                    @endif
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-orange-400/90">Founded Venture</p>
                        <p class="mt-0.5 line-clamp-1 text-base font-bold tracking-tight text-slate-900">{{ $highlight->name }}</p>
                    </div>
                </div>
                @if ($highlight->category)
                    <span class="mt-1 inline-block rounded-lg bg-orange-50 px-2.5 py-1 text-[10px] font-bold text-uco-orange-600">
                        {{ $highlight->category->name }}
                    </span>
                @endif
                @if ($ventureSummary)
                    <p class="mt-3 line-clamp-2 text-xs font-medium leading-relaxed text-slate-500">{{ $ventureSummary }}</p>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-4 py-6 text-center">
                <i class="bi {{ $isIntra ? 'bi-briefcase' : 'bi-rocket-takeoff' }} mb-1 block text-xl text-slate-300"></i>
                <p class="text-[9px] font-medium text-slate-400">
                    {{ $isIntra ? 'No company info added yet' : 'No venture info added yet' }}
                </p>
            </div>
        @endif
    </div>
</a>
