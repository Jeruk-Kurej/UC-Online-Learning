{{-- --- Dark hover tip above its parent (the parent needs `group relative`); align="right" pins it to the right edge --- --}}
@props(['align' => 'center', 'arrowClass' => ''])

<div @class([
    'pointer-events-none absolute bottom-full z-30 mb-1.5 flex flex-col opacity-0 transition-opacity duration-150 group-hover:opacity-100',
    'left-1/2 -translate-x-1/2 items-center' => $align === 'center',
    'right-0 items-end' => $align === 'right',
])>
    <div {{ $attributes->class(['whitespace-nowrap rounded bg-gray-900 px-2 py-0.5 text-xs font-bold text-white shadow-md']) }}>{{ $slot }}</div>
    <div @class([$arrowClass, '-mt-0.5 h-1.5 w-1.5 rotate-45 bg-gray-900'])></div>
</div>
