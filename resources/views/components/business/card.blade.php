@props(['business', 'href', 'kind' => 'entrepreneur', 'showOwner' => true, 'note' => null])

<a href="{{ $href }}" class="card group flex h-full flex-col transition-colors hover:border-brand-500">
    <div class="flex items-start gap-4">
        <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-16 w-16" class="text-xl" />
        <div class="min-w-0 flex-1">
            <h3 class="line-clamp-2 text-lg font-semibold leading-snug text-brand-900 group-hover:underline">{{ $business->name }}</h3>
            @if ($note)
                <p class="mt-0.5 text-sm font-medium text-gray-700">{{ $note }}</p>
            @endif
            @if ($business->category)
                <p class="mt-0.5 text-sm text-gray-600">{{ $business->category->name }}</p>
            @endif
            @if ($kind === 'entrepreneur' && $business->city)
                <p class="mt-1 text-sm text-gray-600"><i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $business->city }}</p>
            @elseif ($kind === 'intrapreneur' && $business->position)
                <p class="mt-1 truncate text-sm text-gray-600"><i class="bi bi-person-badge" aria-hidden="true"></i> {{ $business->position }}</p>
            @endif
        </div>
    </div>

    @if ($kind === 'intrapreneur' ? $business->job_description : $business->unique_value_proposition || $business->description)
        <p class="mt-4 line-clamp-3 text-base text-gray-700">{{ $kind === 'intrapreneur' ? $business->job_description : $business->unique_value_proposition ?? $business->description }}</p>
    @endif

    @if ($showOwner && $business->public_owner)
        <div class="mt-auto pt-4">
            <div class="flex items-center gap-3 border-t border-line pt-4">
                <x-ui.thumb :src="$business->public_owner->profile_photo_url" :name="$business->public_owner->name" size="h-9 w-9" round class="text-sm" />
                <p class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800">{{ $business->public_owner->name }}</p>
            </div>
        </div>
    @endif
</a>
