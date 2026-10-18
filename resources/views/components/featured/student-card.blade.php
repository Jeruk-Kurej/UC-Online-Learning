<a href="{{ route('users.show', $student) }}" class="card group flex h-full flex-col transition-colors hover:border-brand-500">
    <div class="flex items-start gap-4">
        <x-ui.thumb :src="$student->profile_photo_url" :name="$student->name" size="h-20 w-20" round class="text-2xl" />
        <div class="min-w-0 flex-1">
            <h3 class="line-clamp-2 text-lg font-semibold leading-snug text-brand-900 group-hover:underline">{{ $student->full_titled_name }}</h3>
            @if ($student->major)
                <p class="mt-0.5 line-clamp-1 text-sm text-gray-600">{{ $student->major }}</p>
            @endif
            @if ($student->year_of_enrollment)
                <p class="mt-0.5 text-sm text-gray-600">Joined in {{ $student->year_of_enrollment }}</p>
            @endif
        </div>
    </div>

    @if ($highlight)
        <div class="mt-auto pt-4">
            <div class="flex items-center gap-3 border-t border-line pt-4">
                <x-ui.thumb :src="$highlight->logo_url" :name="$highlight->name" size="h-10 w-10" class="text-base" />
                <div class="min-w-0">
                    <p class="truncate text-base font-medium text-gray-900">{{ $highlight->name }}</p>
                    <p class="truncate text-sm text-gray-600">{{ $isIntra ? ($highlight->position ?: 'Works here') : ($highlight->category?->name ?: 'Venture') }}</p>
                </div>
            </div>
        </div>
    @endif
</a>
