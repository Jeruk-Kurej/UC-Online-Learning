{{-- --- Featured person: a large photo, name and studies, then their venture or workplace --- --}}
<a href="{{ route('users.show', $student) }}" class="group flex h-full flex-col overflow-hidden rounded-lg border border-line bg-white transition-colors hover:border-brand-500">
    <span class="relative block aspect-[4/3] w-full overflow-hidden bg-brand-100">
        <span class="absolute inset-0 flex items-center justify-center text-5xl font-semibold text-brand-800" aria-hidden="true">{{ $student->initial }}</span>
        @if ($student->has_profile_photo)
            <img src="{{ $student->profile_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full bg-white object-cover object-top" referrerpolicy="no-referrer">
        @endif
    </span>

    <span class="flex flex-1 flex-col gap-1 p-5">
        <span class="line-clamp-2 font-display text-xl font-semibold leading-snug text-brand-900 group-hover:underline">{{ $student->full_titled_name }}</span>
        @if ($student->major || $student->year_of_enrollment)
            <span class="text-sm text-gray-600">{{ collect([$student->major, $student->year_of_enrollment ? 'Joined in ' . $student->year_of_enrollment : null])->filter()->implode(', ') }}</span>
        @endif

        @if ($highlight)
            <span class="mt-auto pt-4">
                <span class="flex items-center gap-3 border-t border-line pt-4">
                    <x-ui.thumb :src="$highlight->logo_url" :name="$highlight->name" size="h-10 w-10" class="text-base" />
                    <span class="min-w-0">
                        <span class="block truncate text-base font-medium text-gray-900">{{ $highlight->name }}</span>
                        <span class="block truncate text-sm text-gray-600">{{ $isIntra ? ($highlight->position ?: 'Works here') : ($highlight->category?->name ?: 'Venture') }}</span>
                    </span>
                </span>
            </span>
        @endif
    </span>
</a>
