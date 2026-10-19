{{-- --- Featured venture as a photo panel: a product photo (or the logo on orange), the name in white over a dark fade, an orange line at the foot --- --}}
@props(['business'])

<a href="{{ route('businesses.show', $business) }}" class="group relative block aspect-[5/6] overflow-hidden rounded-2xl border-b-4 border-brand-500 bg-gradient-to-br from-brand-400 to-brand-700 text-white">
    <span class="absolute inset-x-0 top-[22%] flex justify-center">
        <x-ui.thumb :src="$business->logo_url" :name="$business->name" size="h-24 w-24" class="bg-white text-3xl" />
    </span>
    @if ($business->showcase_photo_url)
        <img src="{{ $business->showcase_photo_url }}" alt="" data-thumb loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
    @endif
    <span class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-transparent"></span>

    <span class="absolute inset-x-0 bottom-0 block p-5">
        @if ($business->category)
            <span class="mb-2 inline-block rounded bg-white/20 px-2 py-0.5 text-xs font-semibold backdrop-blur-sm">{{ $business->category->name }}</span>
        @endif
        <span class="block font-display text-2xl font-bold leading-tight">{{ $business->name }}</span>
        <span class="mt-1 line-clamp-2 block text-sm text-white/90">{{ $business->unique_value_proposition ?? $business->description }}</span>
        @if ($business->public_owner)
            <span class="mt-3 block text-sm font-medium">by {{ $business->public_owner->name }} &raquo;</span>
        @endif
    </span>
</a>
