@props(['src' => null, 'alt' => '', 'class' => '', 'imgClass' => '', 'fallback' => null])

<div class="relative flex items-center justify-center {{ $class }}">
    @if ($src)
        <!-- Clean Fit Layer (aspect ratio preserved, no crop, no stretch, no bg, rounded-sm) -->
        <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'max-w-full max-h-full object-contain rounded-sm ' . $imgClass]) }} referrerpolicy="no-referrer">
    @elseif ($fallback)
        {{ $fallback }}
    @else
        <div class="w-full h-full flex items-center justify-center text-slate-400">
            <i class="bi bi-image text-xl"></i>
        </div>
    @endif
</div>
