{{-- --- Insight item (App\View\Components\Business\Insight picks the icon colours); dividers come from the caller's class --- --}}
<div {{ $attributes->class(['flex items-start gap-4 sm:gap-2 md:gap-4']) }}>
    <div class="{{ $toneClass }} flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg border shadow-sm sm:h-9 sm:w-9 md:h-11 md:w-11">
        <i class="bi {{ $icon }} text-lg sm:text-sm md:text-lg"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p {{ $label->attributes->class(['mb-1.5 text-xs font-bold  leading-none  text-gray-600']) }}>{{ $label }}</p>
        <h5 class="text-sm font-bold text-gray-800 sm:text-xs md:text-sm lg:text-base">{{ $slot }}</h5>
        <p @class([
            'mt-0.5 text-xs font-medium text-gray-600',
            'italic' => $italicCaption,
        ])>{{ $caption }}</p>
    </div>
</div>
