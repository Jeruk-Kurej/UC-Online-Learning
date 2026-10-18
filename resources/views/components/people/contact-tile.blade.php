{{-- --- Contact link in a profile card (App\View\Components\People\ContactTile picks the hover tint) --- --}}
<a href="{{ $href }}" @if ($newTab) target="_blank" @endif class="group flex cursor-pointer items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="{{ $circleClass }} flex h-10 w-10 items-center justify-center rounded-full transition-colors">
            <i class="bi {{ $icon }} text-lg"></i>
        </div>
        <div>
            <p class="text-xs font-bold leading-tight text-gray-600">{{ $label }}</p>
            <p class="{{ $valueClass }} mt-0.5 text-sm font-bold leading-tight text-gray-800 transition-colors">{{ $slot }}</p>
        </div>
    </div>
    <i class="{{ $arrowClass }} bi bi-arrow-up-right text-sm text-gray-600 transition-colors"></i>
</a>
