@props(['student', 'type' => 'intra', 'delay' => 0])

@php
    $isIntra = $type === 'intra';
    $highlight = $isIntra ? $student->companies->first() : $student->businesses->first();
@endphp

<a href="{{ route('users.show', $student) }}"
    class="reveal-on-scroll uco-premium-card {{ $isIntra ? 'uco-premium-card--blue hover:border-blue-100/70' : 'uco-premium-card--orange hover:border-orange-100/70' }} group rounded-[2rem] border border-gray-100 bg-white p-7 shadow-[0_20px_60px_rgba(0,0,0,0.03)] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl w-full flex flex-col justify-between cursor-pointer"
    style="transition-delay: {{ $delay }}ms"
>
    <div>
        <div class="flex items-start gap-4">
            <div class="h-20 w-20 overflow-hidden rounded-[1.5rem] border {{ $isIntra ? 'border-blue-100' : 'border-orange-100' }} bg-gray-50 shadow-sm flex-shrink-0">
                @if($student->profile_photo_url)
                    <img src="{{ $student->profile_photo_url }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br {{ $isIntra ? 'from-blue-50 to-blue-100/40 text-blue-500' : 'from-orange-50 to-orange-100/40 text-uco-orange-500' }} font-black text-2xl">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="line-clamp-1 text-lg font-[900] text-gray-950 group-hover:text-blue-600 transition-colors">{{ $student->name }}</h3>
                <p class="text-xs font-semibold text-gray-500 mt-0.5 line-clamp-1">{{ $student->major ?? 'General Studies' }}</p>
                <p class="text-[11px] font-bold text-gray-400 mt-0.5">Batch {{ $student->year_of_enrollment ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        @if($isIntra && $highlight)
            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100/80">
                <div class="flex items-center gap-3 mb-2">
                    @if($highlight->logo_url)
                        <img src="{{ $highlight->logo_url }}" class="rounded-md w-12 h-12 object-contain" alt="Logo">
                    @endif
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400">Career Highlight</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900 line-clamp-1">{{ $highlight->name }}</p>
                    </div>
                </div>
                @if($highlight->category)
                    <span class="inline-block mt-1 text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                        {{ $highlight->category->name }}
                    </span>
                @endif
                @if($highlight->job_description)
                    <p class="mt-2 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $highlight->job_description }}</p>
                @endif
            </div>
        @elseif(!$isIntra && $highlight)
            <div class="rounded-xl bg-orange-50/50 p-4 border border-orange-100/80">
                <div class="flex items-center gap-3 mb-2">
                    @if($highlight->logo_url)
                        <img src="{{ $highlight->logo_url }}" class="rounded-md w-12 h-12 object-contain " alt="Logo">
                    @endif
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.25em] text-orange-400/90">Venture Highlight</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-900 line-clamp-1">{{ $highlight->name }}</p>
                    </div>
                </div>
                @if($highlight->category)
                    <span class="inline-block mt-1 text-[9px] font-bold text-uco-orange-600 bg-orange-50 px-2 py-0.5 rounded-md">
                        {{ $highlight->category->name }}
                    </span>
                @endif
                @php
                    $ventureSummary = $highlight->unique_value_proposition ?? $highlight->description ?? null;
                @endphp
                @if($ventureSummary)
                    <p class="mt-2 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $ventureSummary }}</p>
                @endif
            </div>
        @else
            <div class="rounded-xl bg-slate-50/50 p-4 border border-dashed border-slate-200 text-center py-6">
                <i class="bi {{ $isIntra ? 'bi-briefcase' : 'bi-rocket-takeoff' }} text-slate-300 text-xl block mb-1"></i>
                <p class="text-[9px] text-slate-400 font-medium">
                    {{ $isIntra ? 'No company info added yet' : 'No venture info added yet' }}
                </p>
            </div>
        @endif
    </div>
</a>
