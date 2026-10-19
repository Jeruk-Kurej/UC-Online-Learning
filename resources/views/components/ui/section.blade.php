@props(['title', 'accent' => null, 'lead' => null, 'href' => null, 'linkLabel' => null])

<section {{ $attributes->class(['space-y-6']) }}>
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2">
        <div>
            <h2 class="section-heading">{{ $title }}@if ($accent)
                    <span class="text-brand-700">{{ $accent }}</span>
                @endif
            </h2>
            @if ($lead)
                <p class="mt-3 text-base text-gray-700">{{ $lead }}</p>
            @endif
        </div>
        @if ($href)
            <a href="{{ $href }}" class="text-link">{{ $linkLabel }} &raquo;</a>
        @endif
        @isset($action)
            {{ $action }}
        @endisset
    </div>

    {{ $slot }}
</section>
