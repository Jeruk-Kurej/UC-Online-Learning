{{-- --- Featured person: the portrait on its own, then name, studies and their venture or workplace below it on white --- --}}
<a href="{{ route('users.show', $student) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-b-4 border-line border-b-brand-500 bg-white">
    <span class="relative block aspect-square w-full shrink-0 overflow-hidden bg-gradient-to-br from-brand-400 to-brand-700">
        <span class="absolute inset-0 flex items-center justify-center text-7xl font-bold text-white/90" aria-hidden="true">{{ $student->initial }}</span>
        @if ($student->has_profile_photo)
            <img src="{{ $student->profile_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover object-top transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
        @endif
    </span>

    <span class="flex flex-1 flex-col p-5">
        <span class="line-clamp-2 font-display text-xl font-bold leading-snug text-ink group-hover:underline">{{ $student->full_titled_name }}</span>
        @if ($student->major || $student->year_of_enrollment)
            <span class="mt-1 line-clamp-2 text-sm text-gray-700">{{ collect([$student->major, $student->year_of_enrollment ? 'Joined in ' . $student->year_of_enrollment : null])->filter()->implode(', ') }}</span>
        @endif

        @if ($highlight)
            <span class="mt-auto pt-4">
                <span class="flex items-center gap-3 border-t border-line pt-4">
                    <x-ui.thumb :src="$highlight->logo_url" :name="$highlight->name" size="h-10 w-10" class="text-base" />
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-gray-900">{{ $highlight->name }}</span>
                        <span class="block truncate text-xs text-gray-700">{{ $isIntra ? ($highlight->position ?: 'Works here') : ($highlight->category?->name ?: 'Venture') }}</span>
                    </span>
                </span>
            </span>
        @endif
    </span>
</a>
