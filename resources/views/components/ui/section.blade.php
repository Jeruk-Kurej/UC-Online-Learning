@props(['title', 'lead' => null, 'href' => null, 'linkLabel' => null])

<section {{ $attributes->class(['space-y-5']) }}>
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-2 border-b border-line pb-3">
        <div>
            <h2 class="section-heading">{{ $title }}</h2>
            @if ($lead)
                <p class="mt-1 text-base text-gray-600">{{ $lead }}</p>
            @endif
        </div>
        @if ($href)
            <a href="{{ $href }}" class="text-link">{{ $linkLabel }}</a>
        @endif
        @isset($action)
            {{ $action }}
        @endisset
    </div>

    {{ $slot }}
</section>
