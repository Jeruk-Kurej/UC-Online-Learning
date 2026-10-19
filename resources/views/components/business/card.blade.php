{{-- --- Business or work-profile card with a fixed layout: the head, the description and the owner row always take the same room, so a row of cards lines up whatever the texts are --- --}}
@props(['business', 'href', 'kind' => 'entrepreneur', 'showOwner' => true, 'note' => null])

<a href="{{ $href }}" class="card group flex h-full flex-col transition-colors hover:border-brand-500">
    <div class="flex items-start gap-4">
        <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-16 w-16" class="text-xl" />
        <div class="min-w-0 flex-1">
            <h3 class="line-clamp-2 min-h-[3.1rem] text-lg font-semibold leading-snug text-brand-900 group-hover:underline">{{ $business->name }}</h3>
            <p class="{{ $note ? 'font-medium text-gray-700' : 'text-gray-600' }} mt-0.5 truncate text-sm">{{ $note ?: $business->category?->name ?: 'No category' }}</p>
            @if ($kind === 'entrepreneur')
                <p class="mt-0.5 h-5 truncate text-sm text-gray-600">
                    @if ($business->city)
                        <i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $business->city }}
                    @endif
                </p>
            @else
                <p class="mt-0.5 h-5 truncate text-sm text-gray-600">
                    @if ($business->position)
                        <i class="bi bi-person-badge" aria-hidden="true"></i> {{ $business->position }}
                    @endif
                </p>
            @endif
        </div>
    </div>

    <p class="mt-4 line-clamp-3 min-h-[4.875rem] text-base text-gray-700">{{ $kind === 'intrapreneur' ? $business->job_description : $business->unique_value_proposition ?? $business->description }}</p>

    @if ($showOwner && $business->public_owner)
        <div class="mt-auto pt-4">
            <div class="flex items-center gap-3 border-t border-line pt-4">
                <x-ui.thumb :src="$business->public_owner->profile_photo_url" :name="$business->public_owner->name" size="h-9 w-9" round class="text-sm" />
                <p class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800">{{ $business->public_owner->name }}</p>
            </div>
        </div>
    @endif
</a>
