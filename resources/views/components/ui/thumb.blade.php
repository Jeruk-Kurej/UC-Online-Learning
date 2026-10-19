<span {{ $attributes->class(['relative inline-flex shrink-0 items-center justify-center overflow-hidden border border-line bg-brand-50 font-semibold text-brand-800', $size, $shapeClass]) }}>
    <span aria-hidden="true">{{ $initial }}</span>
    @if ($src)
        <img src="{{ $src }}" alt="" data-thumb class="absolute inset-0 h-full w-full bg-white object-contain" referrerpolicy="no-referrer">
    @endif
</span>
