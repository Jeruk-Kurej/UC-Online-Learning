{{-- --- Featured person as a photo panel: portrait, name in white over a dark fade, their venture or workplace at the foot --- --}}
<a href="{{ route('users.show', $student) }}" class="group relative block aspect-[5/6] overflow-hidden rounded-2xl border-b-4 border-brand-500 bg-gradient-to-br from-brand-400 to-brand-700 text-white">
    <span class="absolute inset-x-0 top-[26%] flex justify-center text-7xl font-bold text-white/90" aria-hidden="true">{{ $student->initial }}</span>
    @if ($student->has_profile_photo)
        <img src="{{ $student->profile_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
    @endif
    <span class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></span>

    <span class="absolute inset-x-0 bottom-0 block p-5">
        <span class="block font-display text-2xl font-bold leading-tight">{{ $student->full_titled_name }}</span>
        @if ($student->major || $student->year_of_enrollment)
            <span class="mt-1 block text-sm text-white/90">{{ collect([$student->major, $student->year_of_enrollment ? 'Joined in ' . $student->year_of_enrollment : null])->filter()->implode(', ') }}</span>
        @endif

        @if ($highlight)
            <span class="mt-3 flex items-center gap-3 rounded-xl bg-white/15 p-2.5 backdrop-blur-sm">
                <x-ui.thumb :src="$highlight->logo_url" :name="$highlight->name" size="h-10 w-10" class="bg-white text-base" />
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold">{{ $highlight->name }}</span>
                    <span class="block truncate text-xs text-white/90">{{ $isIntra ? ($highlight->position ?: 'Works here') : ($highlight->category?->name ?: 'Venture') }}</span>
                </span>
            </span>
        @endif
    </span>
</a>
