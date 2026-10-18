{{-- --- Icon + text line in a venture card's footer (city, start year) --- --}}
@props(['icon'])

<span class="flex items-center gap-1 text-[11px] font-bold text-gray-500">
    <i class="bi {{ $icon }}"></i>
    {{ $slot }}
</span>
