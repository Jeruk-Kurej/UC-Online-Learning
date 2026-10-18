{{-- --- One section of My Business: heading with an add button, then the cards or the empty note from the slot --- --}}
@props(['icon', 'title', 'subtitle', 'actionUrl', 'actionLabel'])

<div {{ $attributes }}>
    <div class="reveal-on-scroll mb-8 flex flex-col justify-between gap-4 border-b border-gray-100 pb-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="flex items-center gap-2 text-2xl font-bold text-gray-900">
                <i class="bi {{ $icon }} text-uco-orange-500"></i>
                <span>{{ $title }}</span>
            </h2>
            <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
        </div>
        <div>
            <a href="{{ $actionUrl }}" class="btn btn-primary inline-flex items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i>
                {{ $actionLabel }}
            </a>
        </div>
    </div>

    {{ $slot }}
</div>
