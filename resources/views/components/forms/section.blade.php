{{-- --- A titled card of fields; the slot sits in a one or two column grid --- --}}
@props(['title', 'hint' => null, 'grid' => true])

<section {{ $attributes->class(['card space-y-5']) }}>
    <div>
        <h2 class="card-title">{{ $title }}</h2>
        @if ($hint)
            <p class="mt-1 text-base text-gray-600">{{ $hint }}</p>
        @endif
    </div>

    @if ($grid)
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</section>
