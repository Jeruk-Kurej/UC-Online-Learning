@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
    @else
        <button {{ $attributes->merge(['type' => 'button'])->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
@endif
<i class="bi {{ $icon }} text-xs"></i>
@if ($tipAlign === 'right')
    <div class="pointer-events-none absolute bottom-full right-0 z-30 mb-1.5 flex flex-col items-end opacity-0 transition-opacity duration-150 group-hover:opacity-100">
        <div class="whitespace-nowrap rounded bg-gray-900 px-2 py-0.5 text-[9px] font-black text-white shadow-md">{{ $tip }}</div>
        <div class="{{ $arrowClass }} -mt-0.5 h-1.5 w-1.5 rotate-45 bg-gray-900"></div>
    </div>
@else
    <div class="pointer-events-none absolute bottom-full left-1/2 z-30 mb-1.5 flex -translate-x-1/2 flex-col items-center opacity-0 transition-opacity duration-150 group-hover:opacity-100">
        <div class="whitespace-nowrap rounded bg-gray-900 px-2 py-0.5 text-[9px] font-black text-white shadow-md">{{ $tip }}</div>
        <div class="-mt-0.5 h-1.5 w-1.5 rotate-45 bg-gray-900"></div>
    </div>
@endif
@if ($href)
    </a>
@else
    </button>
@endif
