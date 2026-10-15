{{-- --- Square icon link (with href) or button in an admin list row, with a hover tip (App\View\Components\Admin\IconAction picks the colour) --- --}}
@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
        <i class="bi {{ $icon }} text-xs"></i>
        <x-ui.tooltip :align="$tipAlign" :arrow-class="$arrowClass">{{ $tip }}</x-ui.tooltip>
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class(['relative group w-8 h-8 inline-flex items-center justify-center rounded-md text-white transition-colors', $colorClass]) }}>
        <i class="bi {{ $icon }} text-xs"></i>
        <x-ui.tooltip :align="$tipAlign" :arrow-class="$arrowClass">{{ $tip }}</x-ui.tooltip>
    </button>
@endif
