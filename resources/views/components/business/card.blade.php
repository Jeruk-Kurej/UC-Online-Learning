{{-- --- Business or work-profile card used on the home page, the directory and profiles: the picture on its own (a product photo, or the logo on orange), then fixed room for category, name, place and description so a row lines up whatever the texts are --- --}}
@props(['business', 'href', 'kind' => 'entrepreneur', 'showOwner' => true, 'note' => null])

<a href="{{ $href }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-white transition-colors hover:border-brand-500">
    <span class="relative block aspect-[16/10] w-full shrink-0 overflow-hidden bg-gradient-to-br from-brand-400 to-brand-700">
        <span class="absolute inset-0 flex items-center justify-center">
            <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-24 w-24" class="bg-white text-3xl" />
        </span>
        @if ($kind === 'entrepreneur' && $business->showcase_photo_url)
            <img src="{{ $business->showcase_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
        @endif
    </span>

    <span class="flex flex-1 flex-col p-5">
        <p class="truncate text-sm font-semibold text-brand-700">{{ $note ?: $business->category?->name ?: 'No category' }}</p>
        <h3 class="mt-1 line-clamp-2 min-h-[3.1rem] font-display text-xl font-bold leading-snug text-ink group-hover:underline">{{ $business->name }}</h3>
        <p class="mt-1 h-5 truncate text-sm text-gray-700">
            @if ($kind === 'entrepreneur' && $business->city)
                <i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $business->city }}
            @elseif ($kind === 'intrapreneur' && $business->position)
                <i class="bi bi-person-badge" aria-hidden="true"></i> {{ $business->position }}
            @endif
        </p>
        <p class="mt-2 line-clamp-3 min-h-[4.875rem] text-base text-gray-700">{{ $kind === 'intrapreneur' ? $business->job_description : $business->unique_value_proposition ?? $business->description }}</p>

        @if ($showOwner && $business->public_owner)
            <span class="mt-auto flex items-center gap-2 pt-4">
                <x-ui.thumb :src="$business->public_owner->has_profile_photo ? $business->public_owner->profile_photo_url : null" :name="$business->public_owner->name" size="h-9 w-9" round class="text-sm" />
                <span class="min-w-0 truncate text-sm font-semibold text-brand-700">by {{ $business->public_owner->name }} &raquo;</span>
            </span>
        @endif
    </span>
</a>
