@props(['src' => null, 'alt' => '', 'class' => '', 'imgClass' => '', 'fallback' => null])

<div class="{{ $class }} relative flex items-center justify-center">
    @if ($src)
        <!-- Clean Fit Layer (aspect ratio preserved, no crop, no stretch, no bg, rounded-sm) -->
        <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'max-w-full max-h-full object-contain rounded-sm ' . $imgClass]) }} referrerpolicy="no-referrer">
    @elseif ($fallback)
        {{ $fallback }}
    @else
        <div class="flex h-full w-full items-center justify-center text-slate-400">
            <i class="bi bi-image text-xl"></i>
        </div>
    @endif
</div>
