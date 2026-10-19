{{-- --- Featured venture: the picture on its own (a product photo, or the logo on orange), the text below it on white so a busy photo never gets in the way --- --}}
@props(['business'])

<a href="{{ route('businesses.show', $business) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-b-4 border-line border-b-brand-500 bg-white">
    <span class="relative block aspect-[4/3] w-full shrink-0 overflow-hidden bg-gradient-to-br from-brand-400 to-brand-700">
        <span class="absolute inset-0 flex items-center justify-center">
            <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-24 w-24" class="bg-white text-3xl" />
        </span>
        @if ($business->showcase_photo_url)
            <img src="{{ $business->showcase_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
        @endif
    </span>

    <span class="flex flex-1 flex-col p-5">
        @if ($business->category)
            <span class="truncate text-sm font-semibold text-brand-700">{{ $business->category->name }}</span>
        @endif
        <span class="mt-1 line-clamp-2 font-display text-xl font-bold leading-snug text-ink group-hover:underline">{{ $business->name }}</span>
        <span class="mt-2 line-clamp-3 text-base text-gray-700">{{ $business->unique_value_proposition ?? $business->description }}</span>
        @if ($business->public_owner)
            <span class="mt-auto truncate pt-4 text-sm font-semibold text-brand-700">by {{ $business->public_owner->name }} &raquo;</span>
        @endif
    </span>
</a>
