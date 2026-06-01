@props(['src' => null, 'alt' => '', 'class' => '', 'imgClass' => '', 'fallback' => null])

<div class="relative overflow-hidden bg-slate-950/5 flex items-center justify-center {{ $class }}">
    @if ($src)
        <!-- Blurred Background Layer to fill the container -->
        <img src="{{ $src }}" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110 pointer-events-none" aria-hidden="true" referrerpolicy="no-referrer">
        <!-- Foreground Clean Fit Layer (aspect ratio preserved, no crop, no stretch) -->
        <img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'relative z-10 max-w-full max-h-full object-contain ' . $imgClass]) }} referrerpolicy="no-referrer">
    @elseif ($fallback)
        {{ $fallback }}
    @else
        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
            <i class="bi bi-image text-xl"></i>
        </div>
    @endif
</div>
