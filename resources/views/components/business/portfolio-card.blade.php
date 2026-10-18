{{-- --- Card on My Business for a venture or a work profile: logo, name, category, badges, then the slot --- --}}
@props(['href', 'item', 'categoryFallback'])

<a href="{{ $href }}"
   class="reveal-on-scroll group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-500 hover:border-uco-orange-300 hover:shadow-xl">
    <div class="flex items-start gap-4">
        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50 shadow-sm">
            @if ($item->logo_thumb_url)
                <img src="{{ $item->logo_thumb_url }}" class="h-full w-full object-contain">
            @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 text-gray-600">
                    <span class="select-none text-2xl font-bold opacity-30">{{ substr($item->name, 0, 1) }}</span>
                </div>
            @endif
        </div>

        <div class="min-w-0 flex-1 text-left">
            <h3 class="mb-1 line-clamp-2 text-lg font-bold leading-tight text-gray-900 transition-colors group-hover:text-uco-orange-600">{{ $item->name }}</h3>
            <span class="inline-block rounded-lg bg-soft-gray-100 px-2.5 py-0.5 text-xs font-bold text-soft-gray-600">
                {{ $item->category?->name ?? $categoryFallback }}
            </span>
        </div>
    </div>

    <div class="mt-4 flex items-center gap-2">
        {{ $badges }}
    </div>

    {{ $slot }}

    <div class="mt-auto flex items-center justify-end border-t border-gray-50 pt-4">
        <div class="text-uco-orange-500 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100">
            <i class="bi bi-arrow-right-circle-fill text-xl"></i>
        </div>
    </div>
</a>
