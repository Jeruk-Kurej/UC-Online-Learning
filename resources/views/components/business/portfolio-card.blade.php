{{-- --- Card on My Businesses for a business or a work profile: logo, name, category, badges, then the slot and the actions --- --}}
@props(['href', 'editHref', 'item', 'categoryFallback'])

<article class="card flex h-full flex-col gap-4">
    <div class="flex items-start gap-4">
        <x-ui.thumb :src="$item->logo_thumb_url" :name="$item->name" size="h-16 w-16" class="text-xl" />
        <div class="min-w-0 flex-1">
            <h3 class="line-clamp-2 text-lg font-semibold leading-snug text-brand-900"><a href="{{ $href }}" class="hover:underline">{{ $item->name }}</a></h3>
            <p class="mt-0.5 text-sm text-gray-600">{{ $item->category?->name ?? $categoryFallback }}</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{ $badges }}
    </div>

    <div class="flex-1 space-y-3">
        {{ $slot }}
    </div>

    <div class="flex gap-2 border-t border-line pt-4">
        <a href="{{ $href }}" class="btn btn-secondary btn-sm flex-1">View</a>
        <a href="{{ $editHref }}" class="btn btn-secondary btn-sm flex-1">Edit</a>
    </div>
</article>
