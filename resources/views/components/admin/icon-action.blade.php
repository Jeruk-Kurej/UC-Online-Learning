@if($href)
    <a href="{{ $href }}" {{ $attributes->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
@endif
    <i class="bi {{ $icon }} text-xs"></i>
    @if($tipAlign === 'right')
        <div class="absolute bottom-full right-0 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-end">
            <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">{{ $tip }}</div>
            <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5 {{ $arrowClass }}"></div>
        </div>
    @else
        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity duration-150 z-30 flex flex-col items-center">
            <div class="bg-gray-900 text-white text-[9px] font-black px-2 py-0.5 rounded shadow-md whitespace-nowrap">{{ $tip }}</div>
            <div class="w-1.5 h-1.5 bg-gray-900 rotate-45 -mt-0.5"></div>
        </div>
    @endif
@if($href)
    </a>
@else
    </button>
@endif
