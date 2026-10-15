{{-- --- Top of a business or work-profile page: logo (or an icon), badges, name, and the actions slot on the right --- --}}
@props(['name', 'logoUrl', 'fallbackIcon'])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
    <div class="flex flex-1 flex-row items-center gap-4 sm:items-start sm:gap-5">
        @if ($logoUrl)
            <img src="{{ storage_image_url($logoUrl, ['width' => 256, 'height' => 256, 'crop' => 'thumb', 'quality' => 'auto', 'fetch_format' => 'auto']) }}"
                 alt="{{ $name }} Logo" loading="lazy"
                 class="h-16 w-16 flex-shrink-0 rounded-sm object-contain sm:h-20 sm:w-20">
        @else
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-sm bg-gradient-to-br from-soft-gray-50 to-soft-gray-100 sm:h-20 sm:w-20">
                <i class="bi {{ $fallbackIcon }} text-2xl text-soft-gray-300 sm:text-3xl"></i>
            </div>
        @endif

        <div class="min-w-0 flex-1">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                {{ $badges }}
            </div>
            <h1 class="text-2xl font-bold leading-tight tracking-tight text-soft-gray-900 sm:text-3xl">{{ $name }}</h1>
        </div>
    </div>

    {{ $actions }}
</div>
