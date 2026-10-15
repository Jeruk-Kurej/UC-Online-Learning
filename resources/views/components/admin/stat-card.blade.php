<div {{ $attributes->class(['bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 reveal-on-scroll', $compactOnMobile ? 'p-4 sm:p-6' : 'p-6']) }}>
    <p class="{{ $labelClass }} mb-1 text-[10px] font-bold uppercase tracking-wider">{{ $label }}</p>
    <p @class([
        $compactOnMobile ? 'text-2xl sm:text-3xl' : 'text-3xl',
        'font-bold',
        $valueClass,
    ]) @if ($valueId) id="{{ $valueId }}" @endif>{{ $slot }}</p>
</div>
