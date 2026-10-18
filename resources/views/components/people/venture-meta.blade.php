{{-- --- Icon + text line in a venture card's footer (city, start year) --- --}}
@props(['icon'])

<span class="flex items-center gap-1 text-xs font-bold text-gray-600">
    <i class="bi {{ $icon }}"></i>
    {{ $slot }}
</span>
