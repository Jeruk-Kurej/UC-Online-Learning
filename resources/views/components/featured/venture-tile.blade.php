{{-- --- Featured venture: a picture first (a product photo, or the logo on an orange tint), then name, pitch and owner --- --}}
@props(['business', 'large' => false])

<a href="{{ route('businesses.show', $business) }}" class="group flex h-full flex-col overflow-hidden rounded-lg border border-line bg-white text-gray-900 transition-colors hover:border-brand-500">
    <span @class([
        'relative block w-full overflow-hidden bg-brand-100',
        'aspect-[16/9]' => $large,
        'aspect-[16/10]' => !$large,
    ])>
        <span class="absolute inset-0 flex items-center justify-center">
            <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-24 w-24" class="bg-white text-3xl" />
        </span>
        @if ($business->showcase_photo_url)
            <img src="{{ $business->showcase_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full bg-white object-cover" referrerpolicy="no-referrer">
        @endif
    </span>

    <span class="flex flex-1 flex-col gap-2 p-5">
        @if ($business->category)
            <span class="badge badge-info self-start">{{ $business->category->name }}</span>
        @endif
        <span @class([
            'font-display font-semibold leading-snug text-brand-900 group-hover:underline',
            'text-2xl' => $large,
            'text-xl' => !$large,
        ])>{{ $business->name }}</span>
        <span @class([
            'text-base text-gray-700',
            'line-clamp-3' => $large,
            'line-clamp-2' => !$large,
        ])>{{ $business->unique_value_proposition ?? $business->description }}</span>

        <span class="mt-auto flex items-center justify-between gap-3 border-t border-line pt-4">
            @if ($business->public_owner)
                <span class="flex min-w-0 items-center gap-2">
                    <x-ui.thumb :src="$business->public_owner->has_profile_photo ? $business->public_owner->profile_photo_url : null" :name="$business->public_owner->name" size="h-8 w-8" round class="text-sm" />
                    <span class="truncate text-sm font-medium text-gray-800">{{ $business->public_owner->name }}</span>
                </span>
            @endif
            <span class="shrink-0 text-sm font-semibold text-brand-700">Visit venture</span>
        </span>
    </span>
</a>
